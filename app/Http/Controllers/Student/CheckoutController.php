<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Services\Payment\Payment;
use App\Mail\EmailNotify;
use App\Models\Appointment;
use App\Models\ConsultationSlot;
use App\Models\CourseEnrollment;
use App\Models\Payment as ModelPayment;
use App\Models\Bank;
use App\Models\Course;
use App\Models\Currency;
use App\Models\Event;
use App\Models\FileManager;
use App\Models\Gateway;
use App\Models\GatewayCurrency;
use App\Models\Service;
use App\Models\StudentServiceOrder;
use App\Models\StudentServiceOrderInvoice;
use App\Models\User;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Affiliate\AffiliateController;
use App\Http\Services\Affiliate\AffiliateCommissionService;
use App\Services\ConsultationBookingService;

class CheckoutController extends Controller
{

    use ResponseTrait;

    public function checkout(Request $request)
    {
        $data['pageTitle'] = __('Checkout');
        if ($request->type == 'course' && !is_null($request->slug)) {
            $course = Course::publiclyVisible()->where('slug', $request->slug)->first();
            if (is_null($course)) {
                abort(404, __('Course Not Found'));
            }
            $activeQuery = CourseEnrollment::where('user_id',auth()->id())->where('course_id',$course->id)->where('status',STATUS_ACTIVE);
            if (Schema::hasColumn('course_enrollments','access_expires_at')) $activeQuery->where(fn ($q) => $q->whereNull('access_expires_at')->orWhere('access_expires_at','>',now()));
            $activeEnrollment = $activeQuery->first();
            if ($activeEnrollment) return redirect()->route('student.my_courses.list')->with('success', __('You are already enrolled in this course.'));
            if ((float) $course->price <= 0) {
                app(\App\Services\LmsEnrollmentService::class)->enroll((int) auth()->id(), (int) $course->id, 'free');
                return redirect()->route('student.my_courses.list')->with('success', __('Free course enrollment completed.'));
            }
            $data['course'] = $course;
            $data['itemTitle'] = $course->title;
            $data['itemPrice'] = $course->price;
            $data['type'] = $request->type;
            $data['slug'] = $course->slug;
            $data['id'] = $course->id;
            $data['gateways'] = Gateway::where('status', ACTIVE)->get();
            $data['banks'] = Bank::where('status', ACTIVE)->get();
        } else if ($request->type == 'event' && !is_null($request->slug)) {
            $event = Event::where('slug', $request->slug)->where('status', STATUS_ACTIVE)->first();
            if (is_null($event)) {
                abort(404, __('Event Not Found'));
            }
            $data['event'] = $event;
            $data['itemTitle'] = $event->title;
            $data['itemPrice'] = $event->price;
            $data['type'] = $request->type;
            $data['slug'] = $event->slug;
            $data['id'] = $event->id;
            $data['gateways'] = Gateway::where('status', ACTIVE)->get();
            $data['banks'] = Bank::where('status', ACTIVE)->get();

            if ($event->price < 1) {
                $alreadyHave = ModelPayment::where(['paymentable_type' => Event::class, 'paymentable_id' => $event->id, 'user_id' => auth()->id()])->first();
                if ($alreadyHave) {
                    return back()->with(['success' => __('Already Booked')]);
                }

                $gateway = Gateway::where('slug', 'cash')->first()->first();
                $gatewayCurrency = GatewayCurrency::where(['gateway_id' => $gateway->id])->first();
                $order = $event->payments()->create([
                    'user_id' => auth()->id(),
                    'tnxId' => uniqid(),
                    'amount' => 0,
                    'system_currency' => Currency::where('current_currency', 1)->first()->currency_code,
                    'gateway_id' => $gateway->id,
                    'payment_currency' => $gatewayCurrency->currency,
                    'conversion_rate' => $gatewayCurrency->conversion_rate,
                    'sub_total' => 0,
                    'grand_total' => 0,
                    'grand_total_with_conversation_rate' => 0 * $gatewayCurrency->conversion_rate,
                    'payment_status' => PAYMENT_STATUS_PAID,
                    'payment_time' => now(),
                ]);

                $order->transaction()->create([
                    'user_id' => $order->user_id,
                    'reference_id' => $reference->id ?? null,
                    'type' => TRANSACTION_EVENT,
                    'tnxId' => $order->tnxId,
                    'amount' => $order->grand_total,
                    'purpose' => __('Event booked successfully'),
                    'payment_time' => $order->payment_time,
                    'payment_method' => $gateway->title
                ]);

                $purpose = __('Event booked successfully');

                setCommonNotification(
                    auth()->id(),
                    $purpose,
                    __('You have booked an event. Please check the my event'),
                );

                return back()->with(['success' => __('Event booked successfully')]);
            }
        } else if ($request->type == 'consultation' && !is_null($request->id)) {
            $consultant = User::where(['role' => USER_ROLE_CONSULTANT, 'status' => STATUS_ACTIVE, 'id' => decodeId($request->id)])->firstOrFail();

            if ($consultant->fee < 1) {
                app(ConsultationBookingService::class)->book(
                    (int) auth()->id(), (int) $consultant->id, (int) $request->consultation_slot_id,
                    (string) $request->date, (int) $request->consultationType, PAYMENT_STATUS_PAID
                );

                $purpose = __('Consultation booked successfully');

                setCommonNotification(
                    auth()->id(),
                    $purpose,
                    __('You have booked an appointment. Please check the my appointment tab'),
                );

                return back()->with(['success' => __('Consultation booked successfully')]);
            }

            $consultantSlot = $consultant->slots()->where('consultation_slots.id', $request->consultation_slot_id)->where('consultation_slots.status', STATUS_ACTIVE)->firstOrFail();
            $data['consultant'] = $consultant;
            $data['itemTitle'] = $consultant->name;
            $data['itemPrice'] = $consultant->fee;
            $data['date'] = $request->date;
            $data['slot'] = Carbon::createFromFormat('H:i', $consultantSlot->start_time)->format('g:i a');
            $data['consultationType'] = $request->consultationType;
            $data['consultation_slot_id'] = $request->consultation_slot_id;
            $data['type'] = $request->type;
            $data['slug'] = $consultant->id;
            $data['id'] = $consultant->id;
            $data['gateways'] = Gateway::where('status', ACTIVE)->get();
            $data['banks'] = Bank::where('status', ACTIVE)->get();
        } else if ($request->type == 'service' && !is_null($request->slug)) {
            $service = Service::where('slug', $request->slug)->where('status', STATUS_ACTIVE)->first();
            if (is_null($service)) {
                abort(404, __('Service Not Found'));
            }
            $data['service'] = $service;
            $data['itemTitle'] = $service->title;
            $data['itemPrice'] = $service->price;
            $data['type'] = $request->type;
            $data['slug'] = $service->slug;
            $data['id'] = $service->id;
            $data['gateways'] = Gateway::where('status', ACTIVE)->get();
            $data['banks'] = Bank::where('status', ACTIVE)->get();
        } else if ($request->type == 'service-invoice' && !is_null($request->id)) {
            $serviceOrderInvoice = StudentServiceOrderInvoice::where('id', decodeId($request->id))->where('payment_status', PAYMENT_STATUS_PENDING)->first();
            if (is_null($serviceOrderInvoice)) {
                abort(404, __('Service Invoice Not Found'));
            }

            $data['serviceOrder'] = $serviceOrderInvoice;
            $data['itemTitle'] = $serviceOrderInvoice->details ?? $serviceOrderInvoice->service->title;
            $data['itemPrice'] = $serviceOrderInvoice->total;
            $data['type'] = $request->type;
            $data['slug'] = $serviceOrderInvoice->id;
            $data['id'] = $serviceOrderInvoice->id;
            $data['gateways'] = Gateway::where('status', ACTIVE)->get();
            $data['banks'] = Bank::where('status', ACTIVE)->get();
        } else {
            return back()->with(['error' => __('Data Not Found')]);
        }

        return view('student.checkout', $data);
    }

    public function pay(Request $request)
    {

        $gateway = Gateway::where(['slug' => $request->gateway, 'status' => ACTIVE])->first();
        if (is_null($gateway)) {

            return back()->with(['error' => __('Gateway Not Found')]);
        }

        if ($gateway->slug == 'bank' && !$request->hasFile('bank_slip')) {
            return back()->with(['error' => __('Bank slip is required')]);
        }

        // Validate the gateway currency
        $gatewayCurrency = GatewayCurrency::where(['gateway_id' => $gateway->id, 'currency' => $request->gateway_currency])->first();
        if (is_null($gatewayCurrency)) {
            return back()->with(['error' => __('Gateway Currency Not Found')]);
        }

        switch ($request->type) {
            case 'course':
                $object = Course::where('slug', $request->slug)->where('status', STATUS_ACTIVE)->first();
                $price = $object->price;
                $purpose = __('Course purchase order placed successfully');
                break;

            case 'event':
                $object = Event::where('slug', $request->slug)->where('status', STATUS_ACTIVE)->first();
                $price = $object->price;
                $purpose = __('Event purchase order placed successfully');
                break;

            case 'service':
                $object = Service::where('slug', $request->slug)->where('status', STATUS_ACTIVE)->first();
                $price = $object->price;
                $purpose = __('Service purchase order placed successfully');
                break;

            case 'service-invoice':
                $object = StudentServiceOrderInvoice::where('id', $request->slug)->first();
                $price = $object->total;
                $purpose = __('Service Invoice order placed successfully');
                break;

            case 'consultation':
                $consultant = User::where('id', $request->input('consulter_id'))->where('role', USER_ROLE_CONSULTANT)->where('status', STATUS_ACTIVE)->firstOrFail();
                $object = app(ConsultationBookingService::class)->book(
                    (int) auth()->id(), (int) $consultant->id, (int) $request->input('consultation_slot_id'),
                    (string) $request->input('date'), (int) $request->input('consultation_type'), PAYMENT_STATUS_NOT_INIT
                );
                $price = $consultant->fee;
                $purpose = __('Consultation booking order placed successfully');
                break;

            default:
                return back()->with(['error' => __('Invalid Payment Type')]);
        }

        if (is_null($object)) {
            return back()->with(['error' => __('Desired payment data not found')]);
        }


        if ($gateway->slug == 'bank') {
            if ($request->type == 'consultation') {
                $object->update(['payment_status' => PAYMENT_STATUS_PENDING]);
            }
            return $this->handleBankPayment($request, $object, $price, $gateway, $gatewayCurrency, $purpose);
        } elseif ($gateway->slug == 'cash') {
            if ($request->type == 'consultation') {
                $object->update(['payment_status' => PAYMENT_STATUS_PENDING]);
            }
            return $this->handleCashPayment($object, $price, $gateway, $gatewayCurrency, $purpose);
        } else {
            return $this->handleNonBankPayment($request, $object, $price, $gateway, $gatewayCurrency, $purpose);
        }
    }

    public function handleBankPayment($request, $object, $price, $gateway, $gatewayCurrency, $purpose)
    {
        DB::beginTransaction();
        try {
            $bank = Bank::where(['gateway_id' => $gateway->id, 'id' => $request->bank_id])->firstOrFail();
            $bank_id = $bank->id;
            $deposit_slip_id = null;

            // If deposit slip exists, upload it
            if ($request->hasFile('bank_slip')) {
                $new_file = new FileManager();
                $uploaded = $new_file->upload('payments', $request->bank_slip);
                $deposit_slip_id = $uploaded->id;
            }

            // If the payment is for a service, create ServiceOrder and Invoice
            if ($request->type == 'service') {
                $studentServiceOrder = StudentServiceOrder::create([
                    'service_id' => $object->id,
                    'student_id' => auth()->id(),
                    'amount' => $price,
                    'subtotal' => $price,
                    'total' => $price,
                    'transaction_amount' => $price,
                    'payment_status' => PAYMENT_STATUS_PENDING,
                    'working_status' => WORKING_STATUS_PENDING,
                    'created_by' => auth()->id(),
                ]);

                $serviceOrderInvoice = StudentServiceOrderInvoice::create([
                    'student_id' => auth()->id(),
                    'student_service_order_id' => $studentServiceOrder->id,
                    'service_id' => $studentServiceOrder->service_id,
                    'payable_amount' => $studentServiceOrder->total,
                    'total' => $studentServiceOrder->total,
                    'payment_status' => PAYMENT_STATUS_PENDING,
                    'due_date' => now(),
                    'created_by' => auth()->id(),
                ]);

                $studentServiceOrder->update(['student_service_order_invoice_id' => $serviceOrderInvoice->id]);
            }

            // Place the order and bind it to the ServiceOrderInvoice (if created)
            $order = $this->placeOrder(
                $serviceOrderInvoice ?? $object,
                $price,
                $gateway,
                $gatewayCurrency,
                $bank_id,
                $deposit_slip_id
            );



            // Notify admins for pending payment approval
            $this->notifyAdminsForPendingPayment($order, $purpose, 'bank');

            DB::commit();
            return redirect()->route('student.checkout.success', ['success' => true, 'message' => __('Bank details sent successfully! Wait for approval')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('student.checkout.success')->with(['error' => __('Your payment has failed!')]);
        }
    }


    public function handleCashPayment($object, $price, $gateway, $gatewayCurrency, $purpose)
    {
        DB::beginTransaction();
        try {
            // If the payment is for a service, create ServiceOrder and Invoice
            if ($object instanceof Service) {
                $studentServiceOrder = StudentServiceOrder::create([
                    'service_id' => $object->id,
                    'student_id' => auth()->id(),
                    'amount' => $price,
                    'subtotal' => $price,
                    'total' => $price,
                    'transaction_amount' => $price,
                    'payment_status' => PAYMENT_STATUS_PENDING,
                    'working_status' => WORKING_STATUS_PENDING,
                    'created_by' => auth()->id(),
                ]);

                $serviceOrderInvoice = StudentServiceOrderInvoice::create([
                    'student_id' => auth()->id(),
                    'student_service_order_id' => $studentServiceOrder->id,
                    'service_id' => $studentServiceOrder->service_id,
                    'payable_amount' => $studentServiceOrder->total,
                    'total' => $studentServiceOrder->total,
                    'payment_status' => PAYMENT_STATUS_PENDING,
                    'due_date' => now(),
                    'created_by' => auth()->id(),
                ]);

                $studentServiceOrder->update(['student_service_order_invoice_id' => $serviceOrderInvoice->id]);
            }

            // Place the order and bind it to the ServiceOrderInvoice (if created)
            $order = $this->placeOrder(
                $serviceOrderInvoice ?? $object,
                $price,
                $gateway,
                $gatewayCurrency
            );

            // Notify admins for pending payment approval
            $this->notifyAdminsForPendingPayment($order, $purpose, 'cash');

            DB::commit();
            return redirect()->route('student.checkout.success', ['success' => true, 'message' => __('Cash payment initiated! Wait for approval')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('student.checkout.success')->with(['error' => __('Your payment has failed!')]);
        }
    }

    public function handleNonBankPayment($request, $object, $price, $gateway, $gatewayCurrency, $purpose)
    {
        DB::beginTransaction();

        try {

            if ($request->type == 'service') {
                $studentServiceOrder = StudentServiceOrder::create([
                    'service_id' => $object->id,
                    'student_id' => auth()->id(),
                    'amount' => $price,
                    'subtotal' => $price,
                    'total' => $price,
                    'transaction_amount' => $price,
                    'payment_status' => PAYMENT_STATUS_PENDING,
                    'working_status' => WORKING_STATUS_PENDING,
                    'created_by' => auth()->id(),
                ]);

                $serviceOrderInvoice = StudentServiceOrderInvoice::create([
                    'student_id' => auth()->id(),
                    'student_service_order_id' => $studentServiceOrder->id,
                    'service_id' => $studentServiceOrder->service_id,
                    'payable_amount' => $studentServiceOrder->total,
                    'total' => $studentServiceOrder->total,
                    'payment_status' => PAYMENT_STATUS_PENDING,
                    'due_date' => now(),
                    'created_by' => auth()->id(),
                ]);

                $studentServiceOrder->update([
                    'student_service_order_invoice_id' => $serviceOrderInvoice->id
                ]);

                $object = $serviceOrderInvoice;
            }

            $order = $this->placeOrder($object, $price, $gateway, $gatewayCurrency);

            $paymentService = new Payment($gateway->slug, [
                'currency' => $gatewayCurrency->currency,
                'callback_url' => route('student.checkout.verify'),
                'cancel_url' => route('student.checkout'),
                'id' => $order->id,
                'type' => $request->type
            ]);

            $paymentResponse = $paymentService->makePayment($price);

            if (!$paymentResponse['success']) {
                throw new \Exception($paymentResponse['message'] ?? 'Payment failed');
            }

            $order->paymentId = $paymentResponse['payment_id'];
            $order->save();

            DB::commit();

            if (isset($paymentResponse['redirect_url'])) {
                return redirect($paymentResponse['redirect_url']);
            }

            return redirect()->route('student.checkout.success')
                ->with(['success' => true, 'message' => __('Payment initiated successfully!')]);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('student.checkout.success')
                ->with(['error' => __('Your payment has failed!') . ' ' . $e->getMessage()]);
        }
    }

    // Helper function to notify admins of a pending payment
    private function notifyAdminsForPendingPayment($order, $purpose, $paymentType)
    {
        $admins = User::where(['role' => USER_ROLE_ADMIN, 'status' => STATUS_ACTIVE])->get();

        // Determine the correct admin link based on the paymentable type
        $paymentableType = class_basename(get_class($order->paymentable));
        $adminLink = match ($paymentableType) {
            'Event' => route('admin.orders.index', ['orderType' => 'event']),
            'Course' => route('admin.orders.index', ['orderType' => 'course']),
            'Service' => route('admin.orders.index', ['orderType' => 'service']),
            'Appointment' => route('admin.orders.index', ['orderType' => 'consultation']),
            'StudentServiceOrderInvoice' => route('admin.orders.index', ['orderType' => 'service-invoice']),
            default => route('admin.orders.index') // Fallback route if type is unknown
        };

        foreach ($admins as $admin) {
            // Notification for admins
            setCommonNotification(
                $admin->id,
                __('New Payment Pending Approval'),
                __('A new ') . $paymentType . __(' payment is pending approval for ') . $purpose,
                $adminLink
            );
        }
    }



    public function placeOrder($object, $price, $gateway, $gatewayCurrency, $bank_id = null, $deposit_slip_id = null)
    {
        return $object->payments()->create([
            'user_id' => auth()->id(),
            'tnxId' => uniqid(),
            'amount' => $price,
            'system_currency' => Currency::where('current_currency', 1)->first()->currency_code,
            'gateway_id' => $gateway->id,
            'payment_currency' => $gatewayCurrency->currency,
            'conversion_rate' => $gatewayCurrency->conversion_rate,
            'sub_total' => $price,
            'grand_total' => $price,
            'grand_total_with_conversation_rate' => $price * $gatewayCurrency->conversion_rate,
            'bank_id' => $bank_id,
            'deposit_slip' => $deposit_slip_id,
            'payment_details' => json_encode($object),
            'payment_status' => PAYMENT_STATUS_PENDING
        ]);
    }

    public function verify(Request $request)
    {
        $order_id = $request->get('id', '');
        $payerId = $request->get('PayerID', null);
        $payment_id = $request->get('paymentId', null);

        $order = ModelPayment::find($order_id);
        if (is_null($order)) {
            return redirect()->route('student.checkout')->with(['error' => __('Your order does not exist!')]);
        }
        DB::beginTransaction();
        try {
            $order = ModelPayment::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ((int)$order->payment_status === PAYMENT_STATUS_PAID && $order->transaction()->exists()) {
                DB::commit();
                return redirect()->route('student.transactions')->with(['success' => __('Your payment was already confirmed.')]);
            }
            $gateway = Gateway::findOrFail($order->gateway_id);

            if ($order->gateway_id == $gateway->id && $gateway->slug == MERCADOPAGO) {
                $order->paymentId = $payment_id;
                $order->save();
            }

            $payment_id = $order->paymentId;
            $gatewayBasePayment = new Payment($gateway->slug, [
                'currency' => $order->payment_currency
            ]);

            $payment_data = $gatewayBasePayment->paymentConfirmation($payment_id, $payerId);
            $reference = $order->paymentable;

            if ($payment_data['success'] && $payment_data['data']['payment_status'] == 'success') {
                $order->payment_status = PAYMENT_STATUS_PAID;
                $order->payment_time = now();
                $order->gateway_callback_details = json_encode($request->all());
                $order->save();

                $className = class_basename(get_class($reference));
                $adminLink = '';
                $type = '';
                $purpose = '';

                if ($className == 'Event') {

                    $type = TRANSACTION_EVENT;
                    $purpose = __('Event reservation payment for ') . $reference->title;
                    $adminLink = route('admin.orders.index', ['orderType' => 'event']);
                } elseif ($className == 'Course') {

                    $type = TRANSACTION_COURSE;
                    $purpose = __('Course purchase payment for ') . $reference->title;
                    $adminLink = route('admin.orders.index', ['orderType' => 'course']);

                    app(\App\Services\LmsEnrollmentService::class)->enroll((int) $order->user_id, (int) $reference->id, 'paid', (int) $order->id);
                } elseif ($className == 'Service') {

                    $type = TRANSACTION_INVOICE;
                    $purpose = __('Service purchase payment for ') . $reference->title;
                    $adminLink = route('admin.service_orders');

                    $studentServiceOrder = StudentServiceOrder::create([
                        'service_id' => $reference->id,
                        'student_id' => $order->user_id,
                        'amount' => $order->sub_total,
                        'subtotal' => $order->sub_total,
                        'total' => $order->sub_total,
                        'transaction_amount' => $order->sub_total,
                        'payment_status' => PAYMENT_STATUS_PAID,
                        'working_status' => WORKING_STATUS_PROCESSING,
                        'created_by' => $order->user_id,
                    ]);

                    $invoice = StudentServiceOrderInvoice::create([
                        'student_id' => $order->user_id,
                        'student_service_order_id' => $studentServiceOrder->id,
                        'service_id' => $studentServiceOrder->service_id,
                        'payable_amount' => $studentServiceOrder->total,
                        'total' => $studentServiceOrder->total,
                        'payment_status' => PAYMENT_STATUS_PAID,
                        'due_date' => now(),
                        'created_by' => $order->user_id,
                    ]);

                    $studentServiceOrder->update([
                        'student_service_order_invoice_id' => $invoice->id,
                    ]);

                    $order->update([
                        'paymentable_id' => $invoice->id,
                        'paymentable_type' => StudentServiceOrderInvoice::class,
                    ]);

                    $reference = $invoice;
                } elseif ($className == 'Appointment') {

                    $type = TRANSACTION_CONSULTATION;
                    $purpose = __('Consultation payment for ') . $reference->consulter->name;
                    $adminLink = route('admin.orders.index', ['orderType' => 'consultation']);

                    $reference->update(['payment_status' => STATUS_ACTIVE]);
                } elseif ($className == 'StudentServiceOrderInvoice') {

                    $reference->update([
                        'payment_status' => PAYMENT_STATUS_PAID,
                    ]);

                    $type = TRANSACTION_INVOICE;
                    $purpose = __('Invoice payment for ') . ($reference->details ?? $reference->service->title);
                    $adminLink = route('admin.service_orders');
                }


                $affiliateService = new AffiliateCommissionService();
                $affiliateService->recordCommission($order);


                $order->transaction()->firstOrCreate(['payment_id' => $order->id], [
                    'user_id' => $order->user_id,
                    'reference_id' => $reference->id ?? null,
                    'type' => $type,
                    'tnxId' => $order->tnxId,
                    'amount' => $order->grand_total,
                    'purpose' => $purpose,
                    'payment_time' => $order->payment_time,
                    'payment_method' => $gateway->title
                ]);

                DB::commit();

                return redirect()->route('student.checkout.success', ['success' => true, 'message' => __('Your payment has been successful!')]);
            } else {
                return redirect()->route('student.checkout.success')->with(['error' => __('Your payment has failed!')]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('student.checkout.success')->with(['error' => __('Your payment has failed!')]);
        }
    }

    public function getCurrencyByGateway(Request $request)
    {
        $gateway = Gateway::where('slug', $request->gateway)->where('status', STATUS_ACTIVE)->first();
        $data = GatewayCurrency::where('gateway_id', $gateway->id)->get();
        foreach ($data as $currency) {
            $currency->symbol = $currency->symbol;
        }
        return $this->success($data);
    }

    public function successOrFail(Request $request)
    {
        $data['pageTitle'] = __('Payment Confirmation');
        $data['success'] = $request->success;
        $data['message'] = $request->message;
        return view('student.checkout-success', $data);
    }
}
