<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateHistory;
use App\Models\AffiliateRequest;
use App\Models\Course;
use App\Models\Service;
use App\Models\User;
use App\Traits\General;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AffiliateController extends Controller
{
    use General, ResponseTrait;
    public function dashboard()
    {
        $data['pageTitle'] = 'Affiliate';
        $affiliate = AffiliateHistory::where(['user_id'=> auth()->user()->id, 'status' => AFFILIATE_HISTORY_STATUS_PAID])->sum('actual_price');
        $earnings = AffiliateHistory::where(['user_id'=> auth()->user()->id, 'status' => AFFILIATE_HISTORY_STATUS_PAID])->sum('commission');
        $count = AffiliateHistory::where(['user_id'=> auth()->user()->id, 'status' => AFFILIATE_HISTORY_STATUS_PAID])->count();
        $data['totalAffiliate'] = getNumberFormat($affiliate);
        $data['totalCommission'] = getNumberFormat($earnings);
        $data['totalAffiliateCount'] = $count;
        $data['affiliateKey'] = auth()->user()->affiliate_key;
        $data['affiliate'] = auth()->user();

        $data['commissionEnabled'] = [
            'course' => getOption('affiliate_commission_on_course', 0),
            'service' => getOption('affiliate_commission_on_service', 0),
            'consultation' => getOption('affiliate_commission_on_consultation', 0),
        ];

        $data['commissionRates'] = [
            'course' => getOption('affiliate_commission_course_rate', 10),
            'service' => getOption('affiliate_commission_service_rate', 8),
            'consultation' => getOption('affiliate_commission_consultation_rate', 12),
        ];

        $data['commissionLevels'] = [
            'course' => getOption('affiliate_commission_course_levels', 1),
            'service' => getOption('affiliate_commission_service_levels', 1),
            'consultation' => getOption('affiliate_commission_consultation_levels', 1),
        ];

        // Chart data
        $commissionHistory = $this->affiliateCommissionHistory();
        $data['commissionMonth'] = json_encode($commissionHistory['commissionMonth']);
        $data['commissionChartData'] = json_encode($commissionHistory['commissionChartData']);

        $referralHistory = $this->affiliateReferralHistory();
        $data['referralMonth'] = json_encode($referralHistory['referralMonth']);
        $data['referralChartData'] = json_encode($referralHistory['referralChartData']);

        return view('affiliate.dashboard', $data);
    }
    public function myAffiliations(Request $request){
        if($request->ajax()) {
            $aff = AffiliateHistory::where(['user_id'=>Auth::id(), 'status' => AFFILIATE_HISTORY_STATUS_PAID]);
            $search = $request->input('search.value');
            if ($search) {
                $aff = $aff->where(function($query) use ($search) {
                    $query->whereHas('course', function($q) use ($search) {
                        $q->where('title', 'like', "%$search%");
                    })
                    ->orWhereHas('service', function($q) use ($search) {
                        $q->where('title', 'like', "%$search%");
                    })
                    ->orWhere('actual_price', 'like', "%$search%")
                    ->orWhere('commission', 'like', "%$search%")
                    ->orWhere('created_at', 'like', "%$search%");
                });
            }

            return datatables($aff)
                ->addColumn('item_name', function ($item) {
                    if ($item->course_id) {
                        return $item->course->title ?? 'N/A';
                    } elseif ($item->service_id) {
                        return $item->service->title ?? 'N/A';
                    } elseif ($item->consultation_slot_id) {
                        // Assuming consultationSlot has user relationship
                        $user = $item->consultationSlot->user ?? null;
                        return 'Consultation with ' . ($user ? $user->first_name . ' ' . $user->last_name : 'Unknown');
                    }
                    return 'N/A';
                })
                ->addColumn('actual_price', function ($item) {
                    if(get_currency_placement() == 'after') {
                        return getNumberFormat($item->actual_price) . ' ' . get_currency_symbol();
                    } else {
                        return get_currency_symbol() . ' ' . getNumberFormat($item->actual_price);
                    }
                })
                ->addColumn('commission', function ($item) {
                    $amount = get_currency_placement() == 'after' ?
                        getNumberFormat($item->commission) . ' ' . get_currency_symbol() :
                        get_currency_symbol() . ' ' . getNumberFormat($item->commission);
                    return $amount;
                })
                ->addColumn('commission_percentage', function ($item) {
                    return $item->commission_percentage . '%';
                })
                ->addColumn('date', function ($item) {
                    return $item->created_at->format('Y-m-d H:i:s');
                })
                ->make(true);
        }
        $data['pageTitle'] = __('My Affiliations');
        return view('affiliate.dashboard', $data);
    }


    public function students(Request $request)
    {
        if ($request->ajax()) {
            $students = User::query()
                ->where('role', USER_ROLE_STUDENT)
                ->where('parent_id', auth()->id())
                ->select('users.id', 'users.first_name', 'users.last_name', 'users.status', 'users.email', 'users.image', 'users.mobile');

            return datatables($students)
                ->addColumn('name', function ($user) {
                    return $user->first_name . ' ' . $user->last_name;
                })
                ->addColumn('email', function ($user) {
                    return $user->email;
                })
                ->addColumn('mobile', function ($user) {
                    return $user->mobile;
                })
                ->addColumn('status', function ($user) {
                    switch ($user->status) {
                        case STATUS_ACTIVE:
                            return "<p class='zBadge zBadge-active'>" . __('Active') . "</p>";
                        case STATUS_SUSPENDED:
                            return "<p class='zBadge zBadge-suspended'>" . __('Suspended') . "</p>";
                        default:
                            return "<p class='zBadge zBadge-inactive'>" . __('Inactive') . "</p>";
                    }
                })
                ->addColumn('image', function ($user) {
                    return '<div class="min-w-160 d-flex align-items-center cg-10">
                            <div class="flex-shrink-0 w-41 h-41 bd-one bd-c-stroke rounded-circle overflow-hidden bg-eaeaea d-flex justify-content-center align-items-center">
                                <img src="' . getFileUrl($user->image) . '" alt="Image" class="rounded avatar-xs w-100 h-100 object-fit-cover">
                            </div>
                        </div>';
                })
                ->rawColumns(['status', 'image'])
                ->make(true);
        }

        $data['pageTitle'] = __('My Students');
        return view('affiliate.students.index', $data);
    }


    public function storeStudent(Request $request)
    {

        $request->validate([
            'first_name' => ['required', 'max:120'],
            'last_name' => ['required', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'mobile' => ['required', 'min:6'],
            'password' => ['required', 'min:6'],
            'status' => ['required'],
        ]);

        try {
            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->password = bcrypt($request->password);
            $user->role = USER_ROLE_STUDENT;
            $user->status = $request->status;
            $user->parent_id = auth()->id();
            $user->verify_token = str_replace('-', '', Str::uuid()->toString());
            $user->save();



            $message = __(CREATED_SUCCESSFULLY);
            return $this->success([], $message);
        } catch (Exception $e) {

            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([], $message);
        }
    }


    public function statusChangeModal($id)
    {
        $data['user'] = User::where('parent_id', auth()->id())->findOrFail($id);

        return view('affiliate.students.status_change_modal', $data);
    }

    public function statusChange(Request $request, $id)
    {

        try {
            $user = User::where('parent_id', auth()->id())->findOrFail($id);
            $user->status = $request->status;
            $user->save();


            return $this->success([], __('Updated Successfully'));

        } catch (\Exception $e) {

            return $this->error([], __('Something went wrong! Please try again.'));
        }
    }

    public function affiliateCommissionHistory()
    {
        $categories = [];
        $data = [];

        $currentMonth = now()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $month_name = $currentMonth->format('M');
            $categories[] = $month_name;
            $data[$month_name] = ['commission' => 0];
            $currentMonth->subMonth();
        }

        $categories = array_reverse($categories);

        $totalQuery = AffiliateHistory::select(
            DB::raw('MONTH(created_at) AS month'),
            DB::raw('SUM(commission) AS total_commission')
        )
            ->where('user_id', auth()->id())
            ->where('status', AFFILIATE_HISTORY_STATUS_PAID)
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        foreach ($totalQuery as $record) {
            $month_name = now()->startOfYear()->addMonths($record->month - 1)->format('M');
            if (isset($data[$month_name])) {
                $data[$month_name]['commission'] = $record->total_commission;
            }
        }

        $commissionChartData = [];
        foreach ($categories as $month_name) {
            $commissionChartData[] = $data[$month_name]['commission'];
        }

        // If no data, ensure we have 12 zeros
        if (array_sum($commissionChartData) == 0) {
            $commissionChartData = array_fill(0, 12, 0);
        }

        return [
            'commissionMonth' => $categories,
            'commissionChartData' => $commissionChartData,
        ];
    }

    public function affiliateReferralHistory()
    {
        $categories = [];
        $data = [];

        $currentMonth = now()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $month_name = $currentMonth->format('M');
            $categories[] = $month_name;
            $data[$month_name] = ['count' => 0];
            $currentMonth->subMonth();
        }

        $categories = array_reverse($categories);

        $totalQuery = AffiliateHistory::select(
            DB::raw('MONTH(created_at) AS month'),
            DB::raw('COUNT(*) AS referral_count')
        )
            ->where('user_id', auth()->id())
            ->where('status', AFFILIATE_HISTORY_STATUS_PAID)
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        foreach ($totalQuery as $record) {
            $month_name = now()->startOfYear()->addMonths($record->month - 1)->format('M');
            if (isset($data[$month_name])) {
                $data[$month_name]['count'] = $record->referral_count;
            }
        }

        $referralChartData = [];
        foreach ($categories as $month_name) {
            $referralChartData[] = $data[$month_name]['count'];
        }

        // If no data, ensure we have 12 zeros
        if (array_sum($referralChartData) == 0) {
            $referralChartData = array_fill(0, 12, 0);
        }

        return [
            'referralMonth' => $categories,
            'referralChartData' => $referralChartData,
        ];
    }


}
