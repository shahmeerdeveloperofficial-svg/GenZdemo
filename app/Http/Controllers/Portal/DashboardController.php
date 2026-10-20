<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Course;
use App\Models\StudentServiceOrderInvoice;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $metrics = [];

        if ((int) $user->role === USER_ROLE_INSTRUCTOR) {
            $metrics = [
                ['label' => __('Courses assigned to you'), 'value' => Course::whereJsonContains('instructors', (string) $user->id)->count()],
                ['label' => __('Learner enrollments'), 'value' => Course::whereJsonContains('instructors', (string) $user->id)->withCount('enrollments')->get()->sum('enrollments_count')],
            ];
        } elseif ((int) $user->role === USER_ROLE_FINANCE) {
            $metrics = [
                ['label' => __('Transactions'), 'value' => Transaction::count()],
                ['label' => __('Unpaid service invoices'), 'value' => StudentServiceOrderInvoice::where('payment_status', PAYMENT_STATUS_PENDING)->count()],
            ];
        } elseif ((int) $user->role === USER_ROLE_CMS_EDITOR) {
            $metrics = [
                ['label' => __('Blog posts you authored'), 'value' => Blog::where('created_by', $user->id)->count()],
                ['label' => __('Published blog posts'), 'value' => Blog::where('created_by', $user->id)->where('status', STATUS_ACTIVE)->count()],
            ];
        }

        return view('portal.dashboard', [
            'pageTitle' => getRoleName($user->role) . ' ' . __('Overview'),
            'metrics' => $metrics,
            'roleName' => getRoleName($user->role),
        ]);
    }
}
