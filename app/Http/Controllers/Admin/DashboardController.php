<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AdmissionApplication;
use App\Models\ContactUs;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CrmFollowUp;
use App\Models\CrmLead;
use App\Models\Event;
use App\Models\FreeConsultation;
use App\Models\Payment;
use App\Models\Service;
use App\Models\StudentServiceOrderInvoice;
use App\Models\StudentServiceOrder;
use App\Models\User;
use App\Models\University;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{

    use ResponseTrait;

    public function index()
    {
        $data = $this->dashboardSnapshot();
        $data['pageTitle'] = __('Dashboard');
        $data['isDashboard'] = true;
        $data['activeDashboard'] = 'active';
        return view('admin.dashboard', $data);
    }

    /** Return fresh, database-backed dashboard values for the page and live refresh endpoint. */
    public function metrics()
    {
        return response()->json($this->dashboardSnapshot()['live']);
    }

    private function dashboardSnapshot(): array
    {
        $enrollmentQuery = CourseEnrollment::where('status', STATUS_ACTIVE);
        if (Schema::hasColumn('course_enrollments', 'access_expires_at')) {
            $enrollmentQuery->where(fn ($query) => $query->whereNull('access_expires_at')->orWhere('access_expires_at', '>', now()));
        }

        $crmAvailable = Schema::hasTable('crm_leads') && Schema::hasTable('crm_follow_ups');
        $visibleLeads = $crmAvailable ? $this->visibleDashboardLeads() : null;
        $leadCounts = $visibleLeads
            ? (clone $visibleLeads)->select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status')
            : collect();

        $admissionCounts = Schema::hasTable('admission_applications')
            ? DB::table('admission_applications')->whereNull('deleted_at')->select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status')
            : collect();
        $dueFollowUpsQuery = CrmFollowUp::whereNull('completed_at')->where('due_at', '<=', now());
        if ($visibleLeads) $dueFollowUpsQuery->whereIn('lead_id', (clone $visibleLeads)->select('id'));

        $monthStarts = collect(range(11, 0))->map(fn ($monthsAgo) => now()->startOfMonth()->subMonths($monthsAgo));
        $enrollmentRows = CourseEnrollment::where('created_at', '>=', $monthStarts->first())->get(['created_at']);
        $enrollmentCounts = $enrollmentRows->groupBy(fn ($row) => $row->created_at?->format('Y-m') ?? '')->map->count();
        $enrollmentTrend = [
            'labels' => $monthStarts->map(fn ($month) => $month->format('M Y'))->values(),
            'values' => $monthStarts->map(fn ($month) => (int) $enrollmentCounts->get($month->format('Y-m'), 0))->values(),
        ];

        $coursePopularity = Course::query()
            ->leftJoin('course_enrollments', function ($join) {
                $join->on('courses.id', '=', 'course_enrollments.course_id')
                    ->whereNull('course_enrollments.deleted_at')
                    ->where('course_enrollments.status', STATUS_ACTIVE);
            })
            ->where('courses.status', STATUS_ACTIVE)
            ->select('courses.title', DB::raw('COUNT(course_enrollments.id) as total'))
            ->groupBy('courses.id', 'courses.title')
            ->orderByDesc('total')
            ->orderBy('courses.title')
            ->limit(6)
            ->get();

        $kpis = [
            'totalEvent' => Event::where('status', STATUS_ACTIVE)->count(),
            'totalService' => Service::where('status', STATUS_ACTIVE)->count(),
            'totalAppointment' => Appointment::where('status', '!=', STATUS_REFUNDED)->where('appointments.payment_status', '!=', PAYMENT_STATUS_NOT_INIT)->count(),
            'totalCourse' => Course::where('status', STATUS_ACTIVE)->count(),
            'totalStudent' => User::where(['role' => USER_ROLE_STUDENT, 'status' => STATUS_ACTIVE])->count(),
            'totalStaff' => User::where(['role' => USER_ROLE_STAFF, 'status' => STATUS_ACTIVE])->count(),
            'totalAffiliate' => User::where(['role' => USER_ROLE_AFFILIATE, 'status' => STATUS_ACTIVE])->count(),
            'totalUniversity' => University::where('status', STATUS_ACTIVE)->count(),
            'pendingEnquiries' => FreeConsultation::where('status', STATUS_PENDING)->count(),
            'pendingInvoices' => StudentServiceOrderInvoice::where('payment_status', PAYMENT_STATUS_PENDING)->count(),
            'totalEnrollments' => (clone $enrollmentQuery)->count(),
            'crmAvailable' => $crmAvailable,
            'newLeads' => $crmAvailable ? (int) $leadCounts->get('new', 0) : null,
            'totalLeads' => $crmAvailable ? (int) $leadCounts->sum() : null,
            'dueFollowUps' => $crmAvailable ? $dueFollowUpsQuery->count() : null,
            'totalAdmissions' => Schema::hasTable('admission_applications') ? (int) $admissionCounts->sum() : null,
        ];

        $recentStudents = User::where('role', USER_ROLE_STUDENT)->latest('id')->limit(6)->get(['id', 'first_name', 'last_name', 'email', 'status', 'created_at']);
        $recentLeads = $visibleLeads ? (clone $visibleLeads)->latest('id')->limit(5)->get(['id', 'first_name', 'last_name', 'email', 'status', 'source', 'created_at']) : collect();

        $snapshot = [
            ...$kpis,
            'recentStudents' => $recentStudents,
            'recentLeads' => $recentLeads,
            'enrollmentTrend' => $enrollmentTrend,
            'leadPipeline' => [
                'labels' => ['New', 'Contacted', 'Qualified', 'Consultation', 'Converted', 'Lost'],
                'values' => collect(['new', 'contacted', 'qualified', 'consultation', 'converted', 'lost'])->map(fn ($status) => (int) $leadCounts->get($status, 0))->values(),
            ],
            'admissionsPipeline' => [
                'labels' => AdmissionApplication::STATUSES,
                'values' => collect(AdmissionApplication::STATUSES)->map(fn ($status) => (int) $admissionCounts->get($status, 0))->values(),
            ],
            'coursePopularity' => [
                'labels' => $coursePopularity->pluck('title')->values(),
                'values' => $coursePopularity->pluck('total')->map(fn ($total) => (int) $total)->values(),
            ],
            'live' => [
                'kpis' => $kpis,
                'enrollmentTrend' => $enrollmentTrend,
                'leadPipeline' => [
                    'labels' => ['New', 'Contacted', 'Qualified', 'Consultation', 'Converted', 'Lost'],
                    'values' => collect(['new', 'contacted', 'qualified', 'consultation', 'converted', 'lost'])->map(fn ($status) => (int) $leadCounts->get($status, 0))->values(),
                ],
                'admissionsPipeline' => [
                    'labels' => AdmissionApplication::STATUSES,
                    'values' => collect(AdmissionApplication::STATUSES)->map(fn ($status) => (int) $admissionCounts->get($status, 0))->values(),
                ],
                'coursePopularity' => [
                    'labels' => $coursePopularity->pluck('title')->values(),
                    'values' => $coursePopularity->pluck('total')->map(fn ($total) => (int) $total)->values(),
                ],
                'updatedAt' => now()->toIso8601String(),
            ],
        ];

        return $this->limitDashboardSnapshot($snapshot);
    }

    /** Prevent dashboard HTML and its live JSON feed from exposing modules the user cannot open. */
    private function limitDashboardSnapshot(array $snapshot): array
    {
        $user = auth()->user();
        $kpiPermissions = [
            'totalEvent' => 'Manage Events',
            'totalService' => 'Manage Service',
            'totalAppointment' => 'Manage Consultations',
            'totalCourse' => 'Manage Courses',
            'totalStudent' => 'Manage Students',
            'totalStaff' => 'Manage Staffs',
            'totalAffiliate' => 'Manage Affiliates',
            'totalUniversity' => 'Manage Cms Settings',
            'pendingEnquiries' => 'Manage Free Consultations',
            'pendingInvoices' => 'Manage Service',
            'totalEnrollments' => 'Manage Courses',
            'newLeads' => 'Manage Leads',
            'totalLeads' => 'Manage Leads',
            'dueFollowUps' => 'Manage Leads',
            'totalAdmissions' => 'Manage Admissions',
        ];

        foreach ($kpiPermissions as $key => $permission) {
            if (!$user->can($permission)) {
                $snapshot[$key] = 0;
                $snapshot['live']['kpis'][$key] = 0;
            }
        }

        if (!$user->can('Manage Students')) {
            $snapshot['recentStudents'] = collect();
        }
        if (!$user->can('Manage Leads')) {
            $snapshot['recentLeads'] = collect();
            $snapshot['leadPipeline']['values'] = collect(array_fill(0, $snapshot['leadPipeline']['values']->count(), 0));
            $snapshot['live']['leadPipeline']['values'] = array_fill(0, count($snapshot['live']['leadPipeline']['values']), 0);
        }
        if (!$user->can('Manage Admissions')) {
            $snapshot['admissionsPipeline']['values'] = collect(array_fill(0, $snapshot['admissionsPipeline']['values']->count(), 0));
            $snapshot['live']['admissionsPipeline']['values'] = array_fill(0, count($snapshot['live']['admissionsPipeline']['values']), 0);
        }
        if (!$user->can('Manage Courses')) {
            $snapshot['enrollmentTrend']['values'] = collect(array_fill(0, $snapshot['enrollmentTrend']['values']->count(), 0));
            $snapshot['live']['enrollmentTrend']['values'] = array_fill(0, count($snapshot['live']['enrollmentTrend']['values']), 0);
            $snapshot['coursePopularity'] = ['labels' => collect(), 'values' => collect()];
            $snapshot['live']['coursePopularity'] = ['labels' => [], 'values' => []];
        }

        return $snapshot;
    }

    private function visibleDashboardLeads()
    {
        $query = CrmLead::query();
        if (in_array((int) auth()->user()->role, [USER_ROLE_STAFF, USER_ROLE_CONSULTANT], true)) {
            $query->where(function ($scope) {
                $scope->where('assigned_to', auth()->id());
                if ((int) auth()->user()->role === USER_ROLE_STAFF) $scope->orWhere('created_by', auth()->id());
            });
        }

        return $query;
    }

    public function adminDashboardEventOrder()
    {
        $topEvents = Payment::join('events', 'payments.paymentable_id', '=', 'events.id')
            ->select('events.id', 'events.title', DB::raw('COUNT(payments.id) as payment_count'))
            ->where('payments.paymentable_type', Event::class)
            ->where('payment_status', PAYMENT_STATUS_PAID)
            ->groupBy('events.id', 'events.title')
            ->orderBy('payment_count', 'desc')
            ->take(5)
            ->get();

        // Limit titles to 30 characters with '...'
        $eventOrderData['mainData'] = $topEvents;
        $eventOrderData['eventName'] = $topEvents->pluck('title')->map(function ($title) {
            return Str::limit($title, 30, '...');
        })->toArray();
        $eventOrderData['eventOrder'] = $topEvents->pluck('payment_count')->toArray();

        return $eventOrderData;
    }

    public function adminDashboardConsultantOrder()
    {
        $nameExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "users.first_name || ' ' || users.last_name"
            : "CONCAT(users.first_name, ' ', users.last_name)";
        $topConsultant = Payment::join('appointments', 'payments.paymentable_id', '=', 'appointments.id')
            ->join('users','appointments.consulter_id','=','users.id')
            ->select('users.id', DB::raw($nameExpression . ' as userName'), DB::raw('COUNT(payments.id) as payment_count'))
            ->where('payments.paymentable_type', Appointment::class)
            ->where('payments.payment_status', PAYMENT_STATUS_PAID)
            ->groupBy('users.id')
            ->orderBy('payment_count', 'desc')
            ->take(5)
            ->get();

        $consultantOrderData['mainData'] = $topConsultant;
        $consultantOrderData['consultantName'] = $topConsultant->pluck('userName')->toArray();
        $consultantOrderData['consultantOrder'] =  $topConsultant->pluck('payment_count')->toArray();

        return $consultantOrderData;
    }

    public function adminDashboardServiceOrderHistory()
    {
        $categories = [];
        $data = [];

        $currentMonth = now()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $month_name = $currentMonth->format('M');
            $categories[] = $month_name;
            $data[$month_name] = ['subtotal' => 0];
            $currentMonth->subMonth();
        }

        $categories = array_reverse($categories);

        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "CAST(strftime('%m', created_at) AS INTEGER)"
            : 'MONTH(created_at)';
        $totalQuery = StudentServiceOrder::select(
            DB::raw($monthExpression . ' AS month'),
            DB::raw('SUM(subtotal) AS total_paid')
        )
            ->where('payment_status', PAYMENT_STATUS_PAID)
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy(DB::raw($monthExpression))
            ->get();

        foreach ($totalQuery as $record) {
            $month_name = now()->startOfYear()->addMonths($record->month - 1)->format('M');
            if (isset($data[$month_name])) {
                $data[$month_name]['subtotal'] = $record->total_paid;
            }
        }

        $yearlyChartDataService = [];
        foreach ($categories as $month_name) {
            $yearlyChartDataService[] = $data[$month_name]['subtotal'];
        }

        return [
            'serviceMonth' => $categories,
            'yearlyChartDataService' => $yearlyChartDataService,
        ];
    }

    public function adminDashboardCourseOrderHistory()
    {
        $categories = [];
        $data = [];

        $currentMonth = now()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $month_name = $currentMonth->format('M');
            $categories[] = $month_name;
            $data[$month_name] = ['sub_total' => 0];
            $currentMonth->subMonth();
        }

        $categories = array_reverse($categories);

        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "CAST(strftime('%m', created_at) AS INTEGER)"
            : 'MONTH(created_at)';
        $totalQuery = Payment::select(
            DB::raw($monthExpression . ' AS month'),
            DB::raw('SUM(sub_total) AS total_paid')
        )
            ->where('paymentable_type', Course::class)
            ->where('payment_status', PAYMENT_STATUS_PAID)
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy(DB::raw($monthExpression))
            ->get();

        foreach ($totalQuery as $record) {
            $month_name = now()->startOfYear()->addMonths($record->month - 1)->format('M');
            if (isset($data[$month_name])) {
                $data[$month_name]['sub_total'] = $record->total_paid;
            }
        }

        $yearlyChartDataService = [];
        foreach ($categories as $month_name) {
            $yearlyChartDataService[] = $data[$month_name]['sub_total'];
        }

        return [
            'courseMonth' => $categories,
            'yearlyChartDataCourse' => $yearlyChartDataService,
        ];
    }

    public function contactusList(Request $request)
    {
        if ($request->ajax()) {

            $contactUs = ContactUs::orderByDesc('id');

            return DataTables::of($contactUs)
                ->addIndexColumn()
                ->addColumn('userName', function ($contactUs) {
                    return $contactUs->first_name . ' ' . $contactUs->last_name;
                })
                ->filterColumn('userName', function($query, $keyword) {
                        $query->where('first_name', 'LIKE', "%{$keyword}%")
                            ->orWhere('last_name', 'LIKE', "%{$keyword}%")
                            ->orWhere('email', 'LIKE', "%{$keyword}%")
                            ->orWhere('mobile', 'LIKE', "%{$keyword}%");
                })
                ->make(true);
        }

        $data['pageTitle'] = __('Contact Us List');
        $data['activeContactUs'] = 'active';

        return view('admin.contact-us',$data);

    }
}
