<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\EmailNotify;
use App\Models\Appointment;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Event;
use App\Models\Gateway;
use App\Models\Payment;
use App\Models\StudentServiceOrderInvoice;
use App\Models\User;
use App\Http\Services\Affiliate\AffiliateCommissionService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use function Clue\StreamFilter\fun;

class OrderController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        // Retrieve the orderType from the request
        $orderType = $request->query('orderType');
        $permission = $this->permissionForOrderType($orderType);
        abort_if($permission === null, 404, 'Order type not found');
        abort_unless($request->user()->can($permission), 403);

        // Check if request is AJAX
        if ($request->ajax()) {
            $status = null;
            if ($request->status == 'Paid') {
                $status = PAYMENT_STATUS_PAID;
            } elseif ($request->status == 'Pending') {
                $status = PAYMENT_STATUS_PENDING;
            } elseif ($request->status == 'Cancelled') {
                $status = PAYMENT_STATUS_CANCELLED;
            }

            // Define a mapping for order types to their respective models and fields
            $typeMapping = [
                'event' => [
                    'model' => Event::class,
                    'titleField' => 'events.title'
                ],
                'course' => [
                    'model' => Course::class,
                    'titleField' => 'courses.title'
                ],
                'consultation' => [
                    'model' => Appointment::class, // Use Appointment model for consultation type
                    'titleField' => "CONCAT(consultants.first_name, ' ', consultants.last_name)"
                ],
                'service-invoice' => [
                    'model' => StudentServiceOrderInvoice::class,
                    'titleField' => 'student_service_order_invoices.details'
                ],
            ];

            $typeConfig = $typeMapping[$orderType] ?? null;

            if (!$typeConfig) {
                abort(404, 'Order type not found');
            }

            $paymentableType = $typeConfig['model'];

            // Retrieve payments based on type and apply filters
            $orders = Payment::with(['paymentable', 'user', 'gateway', 'bank'])
                ->where('paymentable_type', $paymentableType)
                ->when($status !== null, function ($query) use ($status) {
                    return $query->where('payments.payment_status', $status);
                })
                ->where(function ($query) {
                    $query->whereNotIn('gateways.slug', ['bank', 'cash'])
                        ->whereIn('payments.payment_status', [STATUS_ACTIVE, STATUS_REJECT])
                        ->orWhere(function ($query) {
                            $query->whereIn('gateways.slug', ['bank', 'cash']);
                        });
                })
                ->when($request->item_id, function ($query) use ($request, $orderType) {
                    if ($orderType === 'consultation') {
                        // For consultations, filter by consultant's ID
                        return $query->where('consultants.id', $request->item_id);
                    } else {
                        // For other types, filter by paymentable_id
                        return $query->where('paymentable_id', $request->item_id);
                    }
                })
                ->when($request->search_key, function ($query) use ($request) {
                    $searchKey = $request->search_key;
                    $query->where(function ($query) use ($searchKey) {
                        $query->where('payments.tnxId', 'like', "%$searchKey%")
                            ->orWhere('customer.first_name', 'like', "%$searchKey%")
                            ->orWhere('customer.last_name', 'like', "%$searchKey%")
                            ->orWhere('customer.email', 'like', "%$searchKey%")
                            ->orWhere('gateways.title', 'like', "%$searchKey%");
                    });
                })
                ->select([
                    'payments.*',
                    DB::raw($typeConfig['titleField'] . ' as itemTitle'),
                    DB::raw("CONCAT(customer.first_name, ' ', customer.last_name) as customerName"),
                    'customer.email as customerEmail',
                    'gateways.title as gatewayName',
                    'gateways.slug as gatewaySlug',
                    'banks.name as bankName'
                ])
                ->leftJoin('users as customer', 'payments.user_id', '=', 'customer.id')
                ->leftJoin('gateways', 'payments.gateway_id', '=', 'gateways.id')
                ->leftJoin('banks', 'payments.bank_id', '=', 'banks.id')
                ->when($paymentableType === Event::class, function ($query) {
                    $query->leftJoin('events', 'payments.paymentable_id', '=', 'events.id');
                })
                ->when($paymentableType === Course::class, function ($query) {
                    $query->leftJoin('courses', 'payments.paymentable_id', '=', 'courses.id');
                })
                ->when($paymentableType === Appointment::class, function ($query) {
                    // Join the appointments and users tables for consultation type
                    $query->leftJoin('appointments', 'payments.paymentable_id', '=', 'appointments.id')
                        ->leftJoin('users as consultants', 'appointments.consulter_id', '=', 'consultants.id');
                })
                ->when($paymentableType === StudentServiceOrderInvoice::class, function ($query) {
                    $query->leftJoin('student_service_order_invoices', 'payments.paymentable_id', '=', 'student_service_order_invoices.id');
                })->orderBy('payments.id', 'DESC');

            // Return DataTable response for AJAX
            return datatables($orders)
                ->addColumn('date', fn($order) => $order->created_at->format('Y-m-d h:i'))
                ->addColumn('amount', fn($order) => showPrice($order->sub_total))
                ->editColumn('gatewayName', function($order){
                    if($order->sub_total <1){
                        return __('N/A');
                    }
                    return $order->gatewayName;
                })
                ->addColumn('payment_info', function ($order) {
                    if($order->sub_total <1){
                        return __('N/A');
                    }
                    return $order->gatewaySlug === 'cash'
                        ? '<div class="text-nowrap">' . __('Cash Paid') . '</div>'
                        : '<div class="text-nowrap">' .
                        __('Bank Name') . ' : ' . $order->bankName . '<br>' .
                        __('Deposit Slip') . ' : ' . '<a href="' . (getFileUrl($order->deposit_slip)) . '" target="_blank">' . __('View slip') . '</a>' .
                        '</div>';
                })
                ->addColumn('status', function ($order) {
                    if($order->sub_total <1){
                        return __('Free');
                    }
                    return match ($order->payment_status) {
                        PAYMENT_STATUS_PAID => '<div class="zBadge zBadge-paid">' . __('Paid') . '</div>',
                        PAYMENT_STATUS_PENDING => '<div class="zBadge zBadge-pending">' . __('Pending') . '</div>',
                        default => '<div class="zBadge zBadge-cancelled">' . __('Cancelled') . '</div>',
                    };
                })
                ->addColumn('action', function ($data) {
                    return '<div class="d-flex align-items-center g-10 justify-content-end">
                        <button onclick="getEditModal(\'' . route('admin.orders.status_change_modal', $data->id) . '\', \'#order-status-change-modal\')" class="align-items-center bd-c-stroke bd-one bg-transparent d-flex h-25 justify-content-center rounded-circle w-25" title="Edit">
                            ' . view('partials.icons.edit')->render() . '
                        </button>
                    </div>';
                })
                ->rawColumns(['status', 'action', 'payment_info'])
                ->make(true);
        }

        // Non-AJAX request: Load the page with the order type data for the filter dropdown
        $typeMapping = [
            'event' => [
                'pageTitle' => __('Event Orders'),
                'filterFieldTitle' => __('Event'),
                'items' => Event::all(),
                'showFlag' => 'showManageEvent',
                'activeFlag' => 'activeEventOrder'
            ],
            'course' => [
                'pageTitle' => __('Course Orders'),
                'filterFieldTitle' => __('Course'),
                'items' => Course::all(),
                'showFlag' => 'showManageCourse',
                'activeFlag' => 'activeCourseOrder'
            ],
            'consultation' => [
                'pageTitle' => __('Consultation Orders'),
                'filterFieldTitle' => __('Consultant'),
                'items' => User::where(['role' => USER_ROLE_CONSULTANT])->get(),
                'showFlag' => 'showManageConsultation',
                'activeFlag' => 'activeAppointmentOrder'
            ],
            'service-invoice' => [
                'pageTitle' => __('Service Invoice Orders'),
                'filterFieldTitle' => __('Service'),
                'items' => [], // No filter items for service invoices
                'showFlag' => 'showManageService',
                'activeFlag' => 'activeServiceOrder'
            ],
        ];

        $typeConfig = $typeMapping[$orderType] ?? null;

        if (!$typeConfig) {
            abort(404, 'Order type not found');
        }

        // Set flags dynamically based on the order type
        $data[$typeConfig['showFlag']] = 'show';
        $data[$typeConfig['activeFlag']] = 'active';
        $data['pageTitle'] = $typeConfig['pageTitle'];
        $data['items'] = $typeConfig['items'];
        $data['orderType'] = $orderType;
        $data['filterFieldTitle'] = $typeConfig['filterFieldTitle'];
        return view('admin.orders.index', $data);
    }

    public function statusModal($id)
    {
        $data['order'] = Payment::with('paymentable')->findOrFail($id);
        $this->authorizePaymentType($data['order']);
        $data['gateways'] = Gateway::where('status', STATUS_ACTIVE)->get();
        return view('admin.orders.status_change_modal', $data);
    }

    public function statusChange(Request $request, $id)
    {
        $data = $request->validate(['payment_status' => ['required', 'integer', 'in:0,1,2']]);
        $this->authorizePaymentType(Payment::with('paymentable')->findOrFail($id));
        DB::beginTransaction();
        try {
            $order = Payment::where('id', $id)->with('gateway', 'paymentable', 'user.parent')->lockForUpdate()->first();
            if (!$order) {
                DB::rollBack();
                return $this->error([], __('Order not found'));
            }
            $alreadyPaid = (int)$order->payment_status === PAYMENT_STATUS_PAID;
            if ($alreadyPaid && (int)$data['payment_status'] === PAYMENT_STATUS_PAID && $order->transaction()->exists()) {
                DB::commit();
                return $this->success([], __('Payment was already processed.'));
            }
            $order->update(['payment_status' => (int)$data['payment_status'], 'paid_by' => auth()->id()]);

            if ((int)$data['payment_status'] === PAYMENT_STATUS_PAID) {
                $order->paymentId = sha1($order->id);
                $order->payment_time = now();
                $order->save();

                $reference = $order->paymentable;
                if (!$reference) {
                    DB::rollBack();
                    return $this->error([], __('Related item not found'));
                }
                $className = class_basename(get_class($reference));

                // Notification and custom actions based on type
                if ($className == 'Event') {
                    $type = TRANSACTION_EVENT;
                    $purpose = __('Event reservation payment for ') . $reference->title;

                    if (getOption('app_mail_status')) {
                        $link = route('student.event.list');
                        $viewData = [
                            '{{name}}' => $order->user->name,
                            '{{email}}' => $order->user->email,
                            '{{link}}' => $link
                        ];
                        $data = getEmailTemplate('event-booking-confirmation', $viewData);
                        Mail::to($order->user->email)->send(new EmailNotify($data));
                    }

                } elseif ($className == 'Course') {
                    app(\App\Services\LmsEnrollmentService::class)->enroll((int) $order->user_id, (int) $reference->id, 'manual_payment', (int) $order->id);

                    $purpose = __('Course purchase payment for ') . $reference->title;
                    $type = TRANSACTION_COURSE;

                    if (getOption('app_mail_status')) {
                        $link = route('student.my_courses.list');
                        $viewData = [
                            '{{name}}' => $order->user->name,
                            '{{email}}' => $order->user->email,
                            '{{link}}' => $link
                        ];
                        $data = getEmailTemplate('course-purchase-success', $viewData);
                        Mail::to($order->user->email)->send(new EmailNotify($data));
                    }

                } elseif ($className == 'Appointment') {
                    $purpose = __('Consultation payment for ') . $reference->title;
                    $type = TRANSACTION_CONSULTATION;

                    if (getOption('app_mail_status')) {
                        $link = route('student.consultation-appointment.list');
                        $viewData = [
                            '{{name}}' => $order->user->name,
                            '{{email}}' => $order->user->email,
                            '{{link}}' => $link
                        ];
                        $data = getEmailTemplate('consultation-booking-success', $viewData);
                        Mail::to($order->user->email)->send(new EmailNotify($data));
                    }

                    // Notify the consultant
                    $consultant = $reference->consulter;
                    $consultationPurpose = __('You have a new consultation booking payment for ') . $reference->title;

                    // Send consultant notification
                    setCommonNotification(
                        $consultant->id,
                        __('New Consultation Booked'),
                        $consultationPurpose,
                        route('consultant.consultations.appointments.index', ['appointment' => $reference->id])
                    );

                    // Send email notification to consultant if enabled
                    if (getOption('app_mail_status')) {
                        $consultantLink = route('consultant.consultations.appointments.index', ['appointment' => $reference->id]);
                        $consultantViewData = [
                            '{{name}}' => $consultant->name,
                            '{{student_name}}' => $order->user->name,
                            '{{link}}' => $consultantLink
                        ];
                        $consultantData = getEmailTemplate('consultation-booking-consultant-notification', $consultantViewData);
                        Mail::to($consultant->email)->send(new EmailNotify($consultantData));
                    }
                } elseif ($className == 'StudentServiceOrderInvoice') {
                    $reference->update(['payment_status' => PAYMENT_STATUS_PAID]);

                    $purpose = __('Invoice payment for ') . $reference->details ?? $reference->service->title;
                    $type = TRANSACTION_INVOICE;
                }

                // Create Transaction Record
                $order->transaction()->firstOrCreate(['payment_id' => $order->id], [
                    'user_id' => $order->user_id,
                    'reference_id' => $reference->id ?? null,
                    'type' => $type,
                    'tnxId' => $order->tnxId,
                    'amount' => $order->grand_total,
                    'purpose' => $purpose,
                    'payment_time' => $order->payment_time,
                    'payment_method' => $order->gateway->title
                ]);

                // Record affiliate commission
                $affiliateService = new AffiliateCommissionService();
                $affiliateService->recordCommission($order);

                // Notify user about successful payment
                setCommonNotification($order->user_id, __('Payment has been successfully'), $purpose, route('student.transactions'));
            }

            if ($order->paymentable_type === Course::class && (int)$data['payment_status'] !== PAYMENT_STATUS_PAID && \Illuminate\Support\Facades\Schema::hasColumn('course_enrollments','payment_id')) {
                \App\Models\CourseEnrollment::where('payment_id',$order->id)->where('status',STATUS_ACTIVE)->get()->each(function ($enrollment) {
                    app(\App\Services\LmsEnrollmentService::class)->revoke($enrollment, __('Related course payment was reversed or cancelled'));
                });
            }

            DB::commit();
            return $this->success([], __('Updated Successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], __('Something went wrong! Please try again'));
        }
    }

    private function permissionForOrderType(?string $orderType): ?string
    {
        return match ($orderType) {
            'event' => 'Manage Events',
            'course' => 'Manage Courses',
            'consultation' => 'Manage Consultations',
            'service-invoice' => 'Manage Service',
            default => null,
        };
    }

    private function authorizePaymentType(Payment $payment): void
    {
        $paymentable = $payment->paymentable;
        $type = $paymentable ? class_basename($paymentable) : class_basename($payment->paymentable_type);
        $permission = match ($type) {
            'Event' => 'Manage Events',
            'Course' => 'Manage Courses',
            'Appointment' => 'Manage Consultations',
            'StudentServiceOrderInvoice' => 'Manage Service',
            default => null,
        };

        abort_unless($permission && auth()->user()->can($permission), 403);
    }
}
