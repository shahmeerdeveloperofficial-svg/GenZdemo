<?php

namespace App\Http\Services\Affiliate;

use Illuminate\Support\Facades\DB;
use App\Models\CashTransaction;

class AffiliateService
{
    /**
     * Create a transaction record
     */
public function createTransaction(array $data)
{
    DB::beginTransaction();

    try {
        
        $tnxId = $data['tnxId'] ?? 'TNX-' . strtoupper(uniqid());

        $transaction = CashTransaction::create([
            'user_id' => $data['user_id'],
            'beneficiary_id' => $data['beneficiary_id'] ?? null,
            'payment_id' => $data['payment_id'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
            'tnxId' => $tnxId,
            'amount' => $data['amount'],
            'type' => $data['type'],
            'purpose' => $data['purpose'] ?? null,
            'payment_time' => now(),
            'payment_method' => $data['payment_method'] ?? 'Affiliate',
            'status' => $data['status'] ?? 1,
        ]);

    
        \Log::info('Transaction created', [
            'transaction_id' => $transaction->id,
            'tnx_id' => $tnxId,
            'user_id' => $data['user_id'],
            'amount' => $data['amount'],
            'type' => $data['type']
        ]);

        DB::commit();
        return $transaction;
        
    } catch (\Exception $e) {
        DB::rollback();
        
        
        \Log::error('Transaction creation failed', [
            'user_id' => $data['user_id'] ?? null,
            'amount' => $data['amount'] ?? null,
            'type' => $data['type'] ?? null,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        throw $e;
    }
}

    /**
     * Get user balance in decimal format
     */
    public function getUserBalance()
    {
        $balance = auth()->user()->balance ?? 0;
        return int_to_decimal($balance);
    }

    /**
     * Get transaction type text
     */
    public function getTransactionTypeText($type)
    {
        $types = [
            TRANSACTION_EVENT => __('Event'),
            TRANSACTION_COURSE => __('Course'),
            TRANSACTION_CONSULTATION => __('Consultation'),
            TRANSACTION_SERVICE => __('Service'),
            TRANSACTION_INVOICE => __('Invoice'),
            TRANSACTION_AFFILIATE => __('Affiliate'),
            TRANSACTION_WITHDRAWAL => __('Withdrawal'),
            TRANSACTION_OTHERS => __('Others'),
        ];
        return $types[$type] ?? __('Unknown');
    }

    /**
     * Get withdrawal status text
     */
    public function getWithdrawalStatusText($status)
    {
        $statuses = [
            WITHDRAWAL_STATUS_PENDING => __('Pending'),
            WITHDRAWAL_STATUS_COMPLETE => __('Approved'),
            WITHDRAWAL_STATUS_REJECTED => __('Rejected'),
        ];
        return $statuses[$status] ?? __('Unknown');
    }

    /**
     * Get beneficiary details
     */
    public function getBeneficiaryDetails($beneficiary)
    {
        if (!$beneficiary)
            return 'N/A';

        switch ($beneficiary->type) {
            case BENEFICIARY_CARD:
                return $beneficiary->card_holder_name . ' (' . substr($beneficiary->card_number, -4) . ')';
            case BENEFICIARY_BANK:
                return $beneficiary->bank_account_name . ' (' . $beneficiary->bank_name . ')';
            case BENEFICIARY_PAYPAL:
                return $beneficiary->paypal_email;
            default:
                return 'N/A';
        }
    }

    /**
     * Get beneficiary name by type
     */
    public function getBeneficiaryName($type)
    {
        $types = [
            BENEFICIARY_CARD => 'Card',
            BENEFICIARY_BANK => __('Bank Account'),
            BENEFICIARY_PAYPAL => __('PayPal'),
        ];
        return $types[$type] ?? __('Unknown');
    }

    /**
     * Get beneficiary account details
     */
    public function getBeneficiaryAccountDetails($beneficiary)
    {
        if (!$beneficiary)
            return 'N/A';

        switch ($beneficiary->type) {
            case BENEFICIARY_CARD:
                return $beneficiary->card_number . ' , ' . __('Holder Name') . ': ' . $beneficiary->card_holder_name;
            case BENEFICIARY_BANK:
                return __('Bank Name') . ': ' . $beneficiary->bank_name . ' | ' . __('Account Number') . ': ' . $beneficiary->bank_account_number . '<br>' . __('Routing Number') . ': ' . $beneficiary->bank_routing_number . ' | ' . __('Account Name') . ': ' . $beneficiary->bank_account_name;
            case BENEFICIARY_PAYPAL:
                return $beneficiary->paypal_email;
            default:
                return 'N/A';
        }
    }
}
