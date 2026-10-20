<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;
use App\Models\Event;
use App\Models\Payment;
use App\Models\StudentServiceOrder;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{

    use ResponseTrait;

    public function index()
    {
        $data['pageTitle'] = __('Dashboard');
        $data['isDashboard'] = true;
        $data['activeDashboard'] = 'active';
        $courseEnrollments = CourseEnrollment::where('user_id',auth()->user()->id)->where('status',STATUS_ACTIVE);
        if (Schema::hasColumn('course_enrollments','access_expires_at')) $courseEnrollments->where(fn ($q) => $q->whereNull('access_expires_at')->orWhere('access_expires_at','>',now()));
        $data['totalPurchasedCourse'] = $courseEnrollments->count();
        $data['totalPurchasedService'] = StudentServiceOrder::where('student_id',auth()->user()->id)->count();
        $data['totalTicketBooking'] = Payment::where(['paymentable_type'=> Event::class,'user_id' => auth()->user()->id,'payment_status' => PAYMENT_STATUS_PAID])->count();
        $data['totalPayment'] = Payment::where(['user_id' => auth()->user()->id,'payment_status' => PAYMENT_STATUS_PAID])->sum('grand_total');

        return view('student.dashboard', $data);
    }
}
