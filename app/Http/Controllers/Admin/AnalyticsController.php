<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user=auth()->user();
        $canCrm=$user->can('Manage Students') || $user->can('Manage Free Consultations');
        $canLms=$user->can('Manage Courses');
        $canFinance=$user->can('Manage Transaction');
        abort_unless($canCrm || $canLms || $canFinance,403);
        $data=$request->validate(['from'=>['nullable','date'],'to'=>['nullable','date','after_or_equal:from']]);
        $from=isset($data['from']) ? now()->parse($data['from'])->startOfDay() : now()->subDays(29)->startOfDay();
        $to=isset($data['to']) ? now()->parse($data['to'])->endOfDay() : now()->endOfDay();
        if ($from->diffInDays($to)>366) throw ValidationException::withMessages(['to'=>__('Report date ranges cannot exceed one year.')]);
        $crmRows=collect(); $applicationRows=collect(); $courseRows=collect(); $paymentRows=collect();
        if ($canCrm && Schema::hasTable('crm_leads')) $crmRows=\DB::table('crm_leads')->whereBetween('created_at',[$from,$to])->selectRaw('status, COUNT(*) as total')->groupBy('status')->orderBy('status')->get();
        if ($canCrm && Schema::hasTable('admission_applications')) $applicationRows=\DB::table('admission_applications')->whereBetween('created_at',[$from,$to])->selectRaw('status, COUNT(*) as total')->groupBy('status')->orderBy('status')->get();
        if ($canLms && Schema::hasTable('course_enrollments')) {
            $query=CourseEnrollment::join('courses','courses.id','=','course_enrollments.course_id')->whereBetween('course_enrollments.created_at',[$from,$to])->selectRaw('courses.id,courses.title,COUNT(course_enrollments.id) as enrollments')->groupBy('courses.id','courses.title')->orderByDesc('enrollments');
            if (Schema::hasColumn('course_enrollments','access_expires_at')) $query->where(fn($q)=>$q->whereNull('course_enrollments.access_expires_at')->orWhere('course_enrollments.access_expires_at','>',$to));
            $courseRows=$query->limit(50)->get();
        }
        if ($canFinance && Schema::hasTable('payments')) $paymentRows=Payment::where('payment_status',PAYMENT_STATUS_PAID)->whereBetween('payment_time',[$from,$to])->selectRaw('paymentable_type, system_currency, COUNT(*) as payments, SUM(grand_total) as gross_total')->groupBy('paymentable_type','system_currency')->orderByDesc('gross_total')->get();
        return view('admin.analytics.index',compact('from','to','canCrm','canLms','canFinance','crmRows','applicationRows','courseRows','paymentRows'));
    }
}
