<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageTransaction extends Model
{
    protected $guarded=["id"];

    public function payment_lines()
    {
        return $this->hasMany(\App\PackageTransactionPayment::class, 'transaction_id');
    }
    public function package_transaction_line()
    {
        return $this->hasMany(\App\PackageTransactionLine::class);
    }

        public function customer()
    {
        return $this->belongsTo(\App\Contact::class, 'customer_id');
    }

    public static function getPaymentStatus($transaction)
    {
        $payment_status = $transaction->payment_status;

        if (in_array($payment_status, ['partial', 'due']) && !empty($transaction->pay_term_number) && !empty($transaction->pay_term_type)) {
            $transaction_date = \Carbon::parse($transaction->transaction_date);
            $due_date = $transaction->pay_term_type == 'days' ? $transaction_date->addDays($transaction->pay_term_number) : $transaction_date->addMonths($transaction->pay_term_number);
            $now = \Carbon::now();
            if ($now->gt($due_date)) {
                $payment_status = $payment_status == 'due' ? 'overdue' : 'partial-overdue';
            }
        }

        return $payment_status;
    }
}
