<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\PackageTransactionPaymentDeleted;
use App\Events\PackageTransactionPaymentUpdated;

class PackageTransactionPayment extends Model
{
    protected $guarded=["id"];
    
      /**
     * Get the transaction related to this payment.
     */
    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class, 'transaction_id');
    }


    public static function deletePayment($payment)
    {
        //Update parent payment if exists
        if (!empty($payment->parent_id)) {
            $parent_payment = PackageTransactionPayment::find($payment->parent_id);
            $parent_payment->amount -= $payment->amount;

            if ($parent_payment->amount <= 0) {
                $parent_payment->delete();
                event(new PackageTransactionPaymentDeleted($parent_payment));
            } else {
                $parent_payment->save();
                //Add event to update parent payment account transaction
                event(new PackageTransactionPaymentUpdated($parent_payment, null));
            }
        }

        $payment->delete();

        $transactionUtil = new \App\Utils\TransactionUtil();

        if(!empty($payment->transaction_id)) {
            //update payment status
            $transaction = $payment->load('transaction')->transaction;
            $transaction_before = $transaction->replicate();

            $payment_status = $transactionUtil->updatePaymentStatus($payment->transaction_id);

            $transaction->payment_status = $payment_status;
            
            $transactionUtil->activityLog($transaction, 'payment_edited', $transaction_before);
        }

        $log_properities = [
            'id' => $payment->id,
            'ref_no' => $payment->payment_ref_no
        ];
        $transactionUtil->activityLog($payment, 'payment_deleted', null, $log_properities);

        //Add event to delete account transaction
        event(new PackageTransactionPaymentDeleted($payment));
        
    }

}
