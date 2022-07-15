<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

use App\PackageTransactionPayment;

class PackageTransactionPaymentUpdated
{
    use SerializesModels;

    public $packageTransactionPayment;

    public $transactionType;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PackageTransactionPayment $packageTransactionPayment, $transactionType)
    {
        $this->packageTransactionPayment = $packageTransactionPayment;
        $this->transactionType = $transactionType;
    }
}
