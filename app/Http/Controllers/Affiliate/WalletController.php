<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Affiliate\SaveBeneficiaryRequest;
use App\Http\Services\Affiliate\AffiliateService;
use App\Models\AffiliateHistory;
use App\Models\Beneficiary;
use App\Models\User;
use App\Models\CashTransaction;
use App\Models\User_paypal;
use App\Models\Withdraw;
use App\Traits\General;
use App\Traits\SendNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    use General, SendNotification;
    public function index()
    {
        if (Auth::user()->role == USER_ROLE_AFFILIATE) {
            $data['pageTitle'] = 'Wallet';
            $rawBalance = Auth::user()->balance;
            $data['myBalance'] = int_to_decimal($rawBalance);
            $data['beneficiaries'] = Beneficiary::where(['user_id' => Auth::id()])->whereStatus(STATUS_ACTIVE)->get();
            return view('frontend.wallet.my-wallet', $data);
        }
        $this->showToastrMessage('warning', __('You are not an affiliator'));
        return redirect()->back();
    }
    public function transactionHistory(Request $request)
    {
        if ($request->ajax()) {
            $aff = CashTransaction::where(['user_id' => Auth::id()])->where('type', '!=', TRANSACTION_WITHDRAWAL)->orderBy('cash_transactions.id', 'DESC');
            $search = $request->input('search.value');
            if ($search) {

                $aff->where(function ($query) use ($search) {
                    $query->where('type', 'like', '%' . $search . '%')
                        ->orWhereRaw("CAST(amount as CHAR) LIKE ?", ['%' . $search . '%'])
                        ->orWhere('tnxId', 'like', '%' . $search . '%')
                        ->orWhere('purpose', 'like', '%' . $search . '%')
                        ->orWhereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') LIKE ?", ['%' . $search . '%']);
                });
            }

            $finalCount = $aff->count();
            return datatables($aff)
                ->addColumn('type', function ($item) {
                    $affiliateService = new AffiliateService();
                    return $affiliateService->getTransactionTypeText($item->type);
                })->addColumn('commission_percentage', function ($item) {
                    if ($item->type == TRANSACTION_AFFILIATE) {
                        $history = AffiliateHistory::where('order_id', $item->payment_id)->where('user_id', $item->user_id)->first();
                        return $history ? $history->commission_percentage . '%' : 'N/A';
                    }
                    return 'N/A';
                })->addColumn('amount', function ($item) {
                    if (get_currency_placement() == 'after') {
                        return getNumberFormat($item->amount) . ' ' . get_currency_symbol();
                    } else {
                        return get_currency_symbol() . ' ' . getNumberFormat($item->amount);
                    }
                })->addColumn('date', function ($item) {
                    return $item->created_at->format('Y-m-d H:i:s');
                })->rawColumns(['tnxId', 'purpose'])->make(true);
        }
    }
    public function WithdrawalHistory(Request $request)
    {
        if ($request->ajax()) {
            $aff = Withdraw::where(['user_id' => Auth::id()])->orderBy('withdraws.created_at', 'DESC')->with('beneficiary');

            $search = $request->input('search.value');
            if ($search) {
                $aff->where(function ($query) use ($search) {
                    $query->where('amount', 'like', '%' . $search . '%')
                        ->orWhere('transection_id', 'like', '%' . $search . '%')
                        ->orWhere('note', 'like', '%' . $search . '%')
                        ->orWhereHas('beneficiary', function ($q) use ($search) {
                            $q->where('beneficiary_name', 'like', '%' . $search . '%')
                                ->orWhere('bank_account_number', 'like', '%' . $search . '%')
                                ->orWhere('card_number', 'like', '%' . $search . '%')
                                ->orWhere('paypal_email', 'like', '%' . $search . '%')
                                ->orWhere('bank_name', 'like', '%' . $search . '%')
                                ->orWhereRaw("CASE
                                    WHEN type = 1 THEN 'Bank'
                                    WHEN type = 2 THEN 'PayPal'
                                    WHEN type = 3 THEN 'Card'
                                    ELSE 'Unknown'
                                END LIKE ?", ['%' . $search . '%']);
                        })
                        ->orWhereRaw("CASE
                            WHEN status = 0 THEN 'Pending'
                            WHEN status = 1 THEN 'Complete'
                            WHEN status = 2 THEN 'Rejected'
                            ELSE 'Unknown'
                        END LIKE ?", ['%' . $search . '%'])
                        ->orWhereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') LIKE ?", ['%' . $search . '%']);
                });
            }

            return datatables($aff)
                ->addColumn('date', function ($item) {
                    return $item->created_at->format('Y-m-d H:i:s');
                })
                ->addColumn('transection_id', function ($item) {
                    return $item->status == WITHDRAWAL_STATUS_COMPLETE ? $item->transection_id : '';
                })
                ->addColumn('note', function ($item) {
                    return $item->note ?? ('Withdrawal via ' . ($item->beneficiary->beneficiary_name ?? 'Beneficiary'));
                })
                ->addColumn('gateway', function ($item) {
                    $affiliateService = new AffiliateService();
                    return $affiliateService->getBeneficiaryName($item->beneficiary?->type) ?? 'N/A';
                })
                ->addColumn('amount', function ($item) {
                    if (get_currency_placement() == 'after') {
                        return getNumberFormat($item->amount) . ' ' . get_currency_symbol();
                    } else {
                        return get_currency_symbol() . ' ' . getNumberFormat($item->amount);
                    }
                })
                ->addColumn('status', function ($item) {
                    $affiliateService = new AffiliateService();
                    $statusText = $affiliateService->getWithdrawalStatusText($item->status);
                    if ($item->status == WITHDRAWAL_STATUS_PENDING) {
                        return '<span class="text-info">' . $statusText . '</span>';
                    } elseif ($item->status == WITHDRAWAL_STATUS_REJECTED) {
                        return '<span class="text-danger">' . $statusText . '</span>';
                    } elseif ($item->status == WITHDRAWAL_STATUS_COMPLETE) {
                        return '<span class="color-green">' . $statusText . '</span>' .
                            '<a target="_blank" href="' . route('affiliate.wallet.download-receipt', $item->uuid) . '">' .
                            '<span class="iconify" data-icon="bxs:file-pdf"></span>' .
                            '</a>';
                    } else {
                        return '<span class="text-muted">' . $statusText . '</span>';
                    }
                })
                ->rawColumns(['status', 'beneficiary'])
                ->make(true);
        }
    }

    // public function withdrawProcess(Request $request)
    // {
    //     $request->validate([
    //         'amount' => 'required|numeric|min:1',
    //         'uuid' => 'required|uuid|exists:beneficiaries,uuid'
    //     ]);

    //     $user = Auth::user();
    //     $userBalance = int_to_decimal($user->balance);


    //     if ($userBalance < $request->amount) {
    //         return redirect()->back()->withErrors(['amount' => __('Insufficient balance. Your current balance is ') . showPrice($userBalance)]);
    //     }

    //     DB::beginTransaction();
    //     try {

    //         $beneficiary = Beneficiary::where('uuid', $request->uuid)->where('user_id', Auth::id())->first();
    //         if (!$beneficiary) {
    //             return redirect()->back()->withErrors(['uuid' => __('Invalid beneficiary selected.')]);
    //         }
    //         $withdrow = new Withdraw();
    //         $withdrow->transection_id = ''; // Will be set on approval
    //         $withdrow->amount = $request->amount;
    //         $withdrow->beneficiary_id = $beneficiary->id;
    //         $withdrow->user_id = Auth::id();
    //         $withdrow->status = WITHDRAWAL_STATUS_PENDING;
    //         $withdrow->save();

    //         // Deduct balance immediately on request
    //         DB::table('users')->where('id', $user->id)->decrement('balance', decimal_to_int($request->amount));

    //         // Send notification to admin
    //         $text = __("New Withdraw Request Received");
    //         $target_url = route('admin.payouts.index');
    //         $this->send($text, 1, $target_url, null);

    //         // Email sending disabled - EmailSendService not found
    //         // $sendEmail = new EmailSendService();
    //         // $sendEmail->sendWithdrawalRequestToAdmin(null);
    //         // $sendEmail->sendWithdrawalRequestToUser(auth()->user(), null);



    //         DB::commit();
    //         return redirect()->route('affiliate.wallet.withdrawal-success')->with(['success' => true, 'message' => __('Withdraw request has been saved successfully! Wait for approval.')]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         // \Log::error('Withdrawal failed', ['error' => $e->getMessage(), 'user_id' => Auth::id(), 'request' => $request->all()]);
    //         return redirect()->route('affiliate.wallet.withdrawal-success')->with(['success' => false, 'message' => __('Something Went Wrong')]);
    //     }
    // }


    public function withdrawProcess(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'uuid' => 'required|uuid|exists:beneficiaries,uuid'
        ]);

        $user = Auth::user();
        $userBalance = int_to_decimal($user->balance);

        if ($userBalance < $request->amount) {
            return redirect()->back()->withErrors(['amount' => __('Insufficient balance. Your current balance is ') . showPrice($userBalance)]);
        }

        DB::beginTransaction();
        try {
        
            $user = User::lockForUpdate()->findOrFail(Auth::id());

            $userBalance = int_to_decimal($user->balance);
            if ($userBalance < $request->amount) {
                DB::rollBack();
                return redirect()->back()->withErrors(['amount' => __('Insufficient balance. Your current balance is ') . showPrice($userBalance)]);
            }

            $beneficiary = Beneficiary::where('uuid', $request->uuid)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            
            $withdraw = Withdraw::create([
                'transection_id' => '', 
                'amount' => $request->amount,
                'beneficiary_id' => $beneficiary->id,
                'user_id' => Auth::id(),
                'status' => WITHDRAWAL_STATUS_PENDING
            ]);

            // Deduct balance using Model (not DB::table)
            $user->decrement('balance', decimal_to_int($request->amount));

            // Log the withdrawal request
            \Log::info('Withdrawal request created', [
                'withdraw_id' => $withdraw->id,
                'user_id' => $user->id,
                'amount' => $request->amount,
                'beneficiary_id' => $beneficiary->id,
                'created_at' => now()
            ]);

            // Send notification to admin
            $text = __("New Withdraw Request Received");
            $target_url = route('admin.payouts.index');
            $this->send($text, 1, $target_url, null);

            DB::commit();


            return redirect()->route('affiliate.wallet.withdrawal-success')
                ->with(['success' => true, 'message' => __('Withdraw request has been saved successfully! Wait for approval.')]);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Withdrawal request failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'amount' => $request->amount,
                'trace' => $e->getTraceAsString()
            ]);


            return redirect()->route('affiliate.wallet.withdrawal-success')
                ->with(['success' => false, 'message' => __('Something Went Wrong')]);
        }
    }


    public function withdrawalSuccess()
    {
        $data['pageTitle'] = __('Withdrawal Request');
        $data['success'] = session('success', true);
        $data['message'] = session('message', __('Withdraw request submitted successfully.'));
        return view('affiliate.wallet.withdrawal-success', $data);
    }

    public function beneficiaryHistory(Request $request)
    {
        if ($request->ajax()) {

            $beneficiaries = Beneficiary::select(
                'id',
                'uuid',
                'beneficiary_name',
                'type',
                'bank_name',
                'bank_account_number',
                'bank_routing_number',
                'bank_account_name',
                'card_number',
                'card_holder_name',
                'paypal_email',
                'status',
                'created_at'
            )
                ->where('user_id', Auth::id())
                ->orderBy('id', 'DESC');

            if ($search = $request->input('search.value')) {

                $beneficiaries->where(function ($q) use ($search) {

                    $q->where('beneficiary_name', 'like', "%$search%")
                        ->orWhere('type', 'like', "%$search%")
                        ->orWhere('bank_name', 'like', "%$search%")
                        ->orWhere('card_number', 'like', "%$search%")
                        ->orWhere('paypal_email', 'like', "%$search%")
                        ->orWhere('bank_account_number', 'like', "%$search%")
                        ->orWhere('bank_routing_number', 'like', "%$search%")
                        ->orWhere('bank_account_name', 'like', "%$search%")
                        ->orWhere('card_holder_name', 'like', "%$search%")
                        ->orWhereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') LIKE ?", ['%' . $search . '%']);
                });
            }

            return datatables()->of($beneficiaries)
                ->addColumn('name', fn($item) => $item->beneficiary_name)
                ->addColumn('type', function ($item) {
                    $affiliateService = new AffiliateService();
                    return $affiliateService->getBeneficiaryName($item->type);
                })
                ->addColumn('details', function ($item) {
                    return getBeneficiaryAccountDetails($item);
                })
                ->addColumn('status', function ($item) {
                    return $item->status == STATUS_ACTIVE
                        ? "<p class='zBadge zBadge-active'>" . __('Active') . "</p>"
                        : "<p class='zBadge zBadge-inactive'>" . __('Deactivate') . "</p>";
                })
                ->addColumn('action', function ($item) {
                    return '<div class="d-flex align-items-center g-10 justify-content-end">
                                <button onclick="changeBeneficiaryStatusModal(\'' . $item->uuid . '\', ' . $item->status . ')" class="align-items-center bd-c-stroke bd-one bg-transparent d-flex h-25 justify-content-center rounded-circle w-25" title="Edit">
                                    ' . view('partials.icons.edit')->render() . '
                                </button>
                            </div>';
                })
                ->rawColumns(['details', 'status', 'action'])
                ->make(true);
        }
    }


    public function myBeneficiary(Request $request)
    {
        $data['pageTitle'] = __('My Beneficiary');
        $data['navPaymentActiveClass'] = 'active';
        $data['beneficiaries'] = Beneficiary::where(['user_id' => Auth::id()])->get();
        $rawBalance = Auth::user()->balance;
        $data['myBalance'] = int_to_decimal($rawBalance);
        return view('affiliate.wallet.beneficiary', $data);
    }

    public function saveBeneficiary(SaveBeneficiaryRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['status'] = STATUS_ACTIVE;

        if ($this->isBeneficiaryDuplicate($request)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('A beneficiary with these details already exists.')
                ], 422);
            }

            $this->showToastrMessage('warning', __('A beneficiary with these details already exists.'));
            return redirect()->back();
        }

        try {
            $beneficiary = Beneficiary::create($data);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Beneficiary added successfully'),
                    'beneficiary' => $beneficiary
                ]);
            }

            $this->showToastrMessage('success', __('Beneficiary added successfully'));
            return redirect()->back();
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Something went wrong. Please try again.')
                ], 500);
            }

            $this->showToastrMessage('error', __('Something went wrong. Please try again.'));
            return redirect()->back();
        }
    }

    private function isBeneficiaryDuplicate($request)
    {
        $existingQuery = Beneficiary::where('user_id', Auth::id())
            ->where('type', $request->type)
            ->where('beneficiary_name', $request->beneficiary_name);

        if ($request->type == BENEFICIARY_CARD && $request->card_number) {
            $existingQuery->where('card_number', $request->card_number);
        } elseif ($request->type == BENEFICIARY_BANK && $request->bank_account_number) {
            $existingQuery->where('bank_account_number', $request->bank_account_number);
        } elseif ($request->type == BENEFICIARY_PAYPAL && $request->paypal_email) {
            $existingQuery->where('paypal_email', $request->paypal_email);
        }

        return $existingQuery->exists();
    }

    public function statusChangeBeneficiary(Request $request, Beneficiary $beneficiary)
    {
        $request->validate([
            'status' => 'required|in:0,1'
        ]);

        $beneficiary->update(['status' => $request->status]);

        $this->showToastrMessage('success', __('Update Successfully'));
        return redirect()->back();
    }

    public function savePaypal(Request $request)
    {
        $request->validate([
            'email' => 'required'
        ]);

        User_paypal::updateOrCreate([
            'user_id' => Auth::id()
        ], [
            'user_id' => Auth::id(),
            'email' => $request->email
        ]);

        $this->showToastrMessage('success', __('Update Successfully'));
        return redirect()->back();
    }

    public function downloadReceipt($uuid)
    {
        $withdraw = Withdraw::whereUuid($uuid)->first();
        //        $invoice_name = 'receipt-' . $withdraw->transection_id. '.pdf';
        // make sure email invoice is checked.
        //        $customPaper = array(0,0,612,792);
        //        $pdf = PDF::loadView('instructor.finance.receipt-pdf', ['withdraw' => $withdraw])->setPaper($customPaper, 'portrait');
        //        $pdf->save(public_path() . '/uploads/receipt/' . $invoice_name);
        // return $pdf->stream($invoice_name);
        //return $pdf->download($invoice_name);
        return view('instructor.finance.receipt-pdf', ['withdraw' => $withdraw]);
    }
}
