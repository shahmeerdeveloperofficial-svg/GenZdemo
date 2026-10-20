<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\Affiliate\AffiliateService;
use App\Models\CashTransaction;
use App\Models\Gateway;
use App\Models\Withdraw;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayoutController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->get('status', 'pending');

            $withdraws = Withdraw::with(['user', 'beneficiary'])->orderBy('created_at', 'desc');

            if ($status == 'pending') {
                $withdraws->where('status', WITHDRAWAL_STATUS_PENDING);
            } elseif ($status == 'completed') {
                $withdraws->where('status', WITHDRAWAL_STATUS_COMPLETE);
            } elseif ($status == 'rejected') {
                $withdraws->where('status', WITHDRAWAL_STATUS_REJECTED);
            }
            // For 'all' status, don't apply any status filter

            if ($request->has('search_key') && !empty($request->search_key)) {
                $search = $request->search_key;
                $withdraws->where(function ($query) use ($search) {
                    $query->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                        ->orWhere('amount', 'like', "%{$search}%")
                        ->orWhere('transection_id', 'like', "%{$search}%");
                });
            }

            return datatables($withdraws)
                ->addColumn('uuid', fn($withdraw) => $withdraw->uuid)
                ->addColumn('transaction_id', fn($withdraw) => $withdraw->status != WITHDRAWAL_STATUS_PENDING ? ($withdraw->transection_id ?? '') : '')
                ->addColumn('user_name', fn($withdraw) => $withdraw->user?->name ?? 'N/A')
                ->addColumn('email', fn($withdraw) => $withdraw->user?->email ?? 'N/A')
                ->addColumn('amount', fn($withdraw) => showPrice($withdraw->amount))
                ->addColumn('gateway', function($withdraw) {
                    $affiliateService = new AffiliateService();
                    return $affiliateService->getBeneficiaryName($withdraw->beneficiary?->type) ?? 'N/A';
                })
                ->addColumn('transaction_date', fn($withdraw) => $withdraw->created_at->format('Y-m-d H:i'))
                ->addColumn('status', function ($withdraw) {
                    return match ($withdraw->status) {
                        WITHDRAWAL_STATUS_PENDING => '<div class="zBadge zBadge-pending">' . __('Pending') . '</div>',
                        WITHDRAWAL_STATUS_COMPLETE => '<div class="zBadge zBadge-paid">' . __('Paid') . '</div>',
                        WITHDRAWAL_STATUS_REJECTED => '<div class="zBadge zBadge-cancelled">' . __('Rejected') . '</div>',
                        default => '<div class="zBadge zBadge-cancelled">' . __('Unknown') . '</div>',
                    };
                })
                ->addColumn('action', function ($withdraw) {
                    $buttons = '<div class="d-flex align-items-center g-10 justify-content-end">';

                    if ($withdraw->status != WITHDRAWAL_STATUS_PENDING) {
                        // View button (available for non-pending)
                        $buttons .= '<button onclick="getEditModal(\'' . route('admin.payouts.details', $withdraw->id) . '\', \'#payout-view-modal\')" type="button" class="align-items-center bd-c-stroke bd-one bg-transparent d-flex h-25 justify-content-center rounded-circle w-25" title="' . __('View') . '">' .
                            view('partials.icons.view')->render() .
                            '</button>';
                    }

                    if ($withdraw->status == WITHDRAWAL_STATUS_PENDING) {
                        $buttons .= '<button onclick="getEditModal(\'' . route('admin.payouts.action_modal', $withdraw->id) . '\', \'#payout-action-modal\')" class="align-items-center bd-c-stroke bd-one bg-transparent d-flex h-25 justify-content-center rounded-circle w-25" title="Edit">' .
                            view('partials.icons.edit')->render() .
                            '</button>';
                    }

                    $buttons .= '</div>';
                    return $buttons;
                })

                ->addColumn('narration', function ($withdraw) {
                    return $withdraw->narration ?? 'N/A';
                })
                ->rawColumns(['status', 'action', 'receipt'])
                ->make(true);
        }

        $status = $request->get('status', 'pending');
        $data['pageTitle'] = __('Manage Affiliate Payouts');
        $data['status'] = $status;
        $data['showManageAffiliate'] = 'show';
        $data['activePayout'] = 'active';

        return view('admin.payouts.index', $data);
    }

    public function statusChange(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:' . WITHDRAWAL_STATUS_COMPLETE . ',' . WITHDRAWAL_STATUS_REJECTED,
            'narration' => 'nullable|string|max:255'
        ]);

        $withdraw = Withdraw::findOrFail($id);

        DB::beginTransaction();
        try {
            $withdraw = Withdraw::lockForUpdate()->findOrFail($id);

            if ($withdraw->status != WITHDRAWAL_STATUS_PENDING) {
                DB::rollBack();
                return $this->error([], __('Withdraw already processed'));
            }

            $withdraw->update([
                'status' => $request->status,
                'narration' => $request->narration,
                'processed_by' => auth()->id()
            ]);

            if ($request->status == WITHDRAWAL_STATUS_COMPLETE) {
                $tnxId = 'TNX-' . strtoupper(uniqid());
                $withdraw->update(['transection_id' => $tnxId]);

                $affiliateService = new AffiliateService();
                $affiliateService->createTransaction([
                    'user_id' => $withdraw->user_id,
                    'amount' => $withdraw->amount,
                    'type' => TRANSACTION_WITHDRAWAL,
                    'purpose' => 'Withdrawal approved via beneficiary ' . $withdraw->beneficiary->beneficiary_name,
                    'beneficiary_id' => $withdraw->beneficiary_id,
                    'tnxId' => $tnxId,
                ]);

                // Log for approval
                \Log::info('Withdrawal approved', [
                    'withdraw_id' => $withdraw->id,
                    'amount' => $withdraw->amount,
                    'user_id' => $withdraw->user_id,
                    'transaction_id' => $tnxId,
                    'beneficiary' => $withdraw->beneficiary->beneficiary_name ?? 'N/A',
                    'processed_by' => auth()->id(),
                    'processed_at' => now()
                ]);

                setCommonNotification($withdraw->user_id, __('Withdraw Approved'), __('Your withdraw request has been approved'), route('affiliate.wallet.index'));

            } elseif ($request->status == WITHDRAWAL_STATUS_REJECTED) {
                $withdraw->user->increment('balance', decimal_to_int($withdraw->amount));

                // Log for rejection
                \Log::info('Withdrawal rejected', [
                    'withdraw_id' => $withdraw->id,
                    'amount' => $withdraw->amount,
                    'user_id' => $withdraw->user_id,
                    'narration' => $request->narration,
                    'balance_returned' => decimal_to_int($withdraw->amount),
                    'processed_by' => auth()->id(),
                    'processed_at' => now()
                ]);

                setCommonNotification($withdraw->user_id, __('Withdraw Rejected'), __('Your withdraw request has been rejected: ') . $request->narration, route('affiliate.wallet.index'));
            }

            DB::commit();
            return $this->success([], __('Status updated successfully'));

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Withdrawal status change failed', [
                'withdraw_id' => $id,
                'user_id' => auth()->id(),
                'requested_status' => $request->status,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]);

            return $this->error([], __('Something went wrong! Please try again'));
        }
    }

    public function actionModal($id)
    {
        $data['withdraw'] = Withdraw::with(['user', 'beneficiary'])->findOrFail($id);
        $data['gateways'] = Gateway::where('status', STATUS_ACTIVE)->get();
        return view('admin.payouts.action_modal', $data);
    }

    public function details($id)
    {
        $data['withdraw'] = Withdraw::with(['user', 'beneficiary'])->findOrFail($id);
        return view('admin.payouts.details_modal', $data);
    }

    public function print($id)
    {
        $data['title'] = __('Payout Print');
        $data['withdraw'] = Withdraw::with(['user', 'beneficiary'])->findOrFail($id);
        return view('admin.payouts.print', $data);
    }

    public function downloadReceipt($uuid)
    {
        $withdraw = Withdraw::whereUuid($uuid)->with(['user', 'beneficiary'])->firstOrFail();
        return view('instructor.finance.receipt-pdf', ['withdraw' => $withdraw]);
    }
}
