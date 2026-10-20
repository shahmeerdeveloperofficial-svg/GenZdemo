<?php

namespace App\Http\Services\Affiliate;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\{AffiliateHistory, Payment, User, CashTransaction};
use App\Http\Services\Affiliate\AffiliateService;

class AffiliateCommissionService
{

    public function calculateCommission(float $price, int $affiliateId, string $type): float
    {

        if (!User::where('id', $affiliateId)->exists()) {
            return 0;
        }

        if (!getOption("affiliate_commission_on_{$type}", 0)) {
            return 0;
        }

        $rate = getOption("affiliate_commission_{$type}_rate");
        if ($rate <= 0) {
            return 0;
        }

        return round($price * $rate * 0.01, 2);
    }


    public function recordCommission(Payment $payment): bool
    {
        $buyer = $payment->user;

        if ($payment->paymentable) {
            $type = $this->getType($payment->paymentable);

            if ($type) {
                if (getOption("affiliate_commission_on_{$type}")) {

                    if (!AffiliateHistory::where('order_id', $payment->id)->exists()) {

                        $levels = getOption("affiliate_commission_{$type}_levels", 1);
                        $rate = getOption("affiliate_commission_{$type}_rate", 10);

                        
                        $currentAffiliate = $buyer;
                        $commissionsRecorded = false;

                        for ($level = 1; $level <= $levels; $level++) {
                            if ($currentAffiliate && $currentAffiliate->parent_id) {
                                $affiliateId = $currentAffiliate->parent_id;

                                $commissionCountFromThisBuyer = AffiliateHistory::where('buyer_id', $buyer->id)
                                    ->where('user_id', $affiliateId)
                                    ->where(function($query) use ($type) {
                                        switch ($type) {
                                            case 'course':
                                                $query->whereNotNull('course_id')->orWhereNotNull('bundle_id');
                                                break;
                                            case 'consultation':
                                                $query->whereNotNull('consultation_slot_id');
                                                break;
                                            case 'service':
                                                $query->whereNotNull('service_id');
                                                break;
                                        }
                                    })
                                    ->count();

                            
                                if ($commissionCountFromThisBuyer < $levels) {
                                    $commission = round($payment->sub_total * $rate * 0.01, 2);

                                    if ($commission > 0) {
                                        $this->saveCommission($payment, $affiliateId, $commission, $rate, $level);
                                        $commissionsRecorded = true;
                                    }
                                }

                                
                                $currentAffiliate = User::find($affiliateId);
                            } else {
                                break; // No more parents in chain
                            }
                        }

                        return $commissionsRecorded;
                    }
                }
            }
        }

        return false;
    }


    // public function recordCommission(Payment $payment): bool
    // {
    //     $buyer = $payment->user;
    //     $affiliateId = $buyer?->parent_id;

    //     if ($affiliateId) {
    //         if ($payment->paymentable) {
    //             $type = $this->getType($payment->paymentable);

    //             if ($type) {
    //                 if (getOption("affiliate_commission_on_{$type}")) {

    //                     if (!AffiliateHistory::where('order_id', $payment->id)->exists()) {

    //                         $rate = getOption("affiliate_commission_{$type}_rate", 10);
    //                         $commission = round($payment->sub_total * $rate * 0.01, 2);

    //                         if ($commission > 0) {
    //                             $this->saveCommission($payment, $affiliateId, $commission, $rate);
    //                             return true;
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     return false;
    // }



    private function getType($reference): ?string
    {
        $class = class_basename($reference);

        return match ($class) {
            'Event', 'Course' => 'course',
            'Appointment' => 'consultation',
            'StudentServiceOrderInvoice' => 'service',
            default => null,
        };
    }


    private function saveCommission($payment, $affiliateId, $commission, $rate, $level = 1): void
    {
        DB::beginTransaction();

        try {
            $reference = $payment->paymentable;
            $class = class_basename($reference);

            $data = [
                'hash' => Str::uuid(),
                'user_id' => $affiliateId,
                'buyer_id' => $payment->user_id,
                'order_id' => $payment->id,
                'actual_price' => $payment->sub_total,
                'commission' => $commission,
                'commission_percentage' => $rate,
                'level' => $level,
                'status' => AFFILIATE_HISTORY_STATUS_PAID,
            ];

            if ($class === 'Event') {
                $data['bundle_id'] = $reference->id;
            }

            if ($class === 'Course') {
                $data['course_id'] = $reference->id;
            }

            if ($class === 'Appointment') {
                $data['consultation_slot_id'] = $reference->consultation_slot_id;
            }

            if ($class === 'StudentServiceOrderInvoice') {
                $data['service_id'] = $reference->service_id;
            }

            AffiliateHistory::create($data);

            User::where('id', $affiliateId)
                ->increment('balance', decimal_to_int($commission));

            CashTransaction::create([
                'user_id' => $affiliateId,
                'payment_id' => $payment->id,
                'amount' => $commission,
                'type' => TRANSACTION_AFFILIATE,
                'purpose' => 'Affiliate commission',
                'payment_time' => now(),
                'payment_method' => 'Affiliate',
                'tnxId' => 'TNX-' . strtoupper(uniqid()),
                'status' => 1,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        
            throw $e;
        }
    }
}
