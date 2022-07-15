<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class PackageTransactionPaymentDeleted
{
    use SerializesModels;

    public $packageTransactionPaymentId;

    public $accountId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($packageTransactionPayment)
    {
        $this->packageTransactionPayment = $packageTransactionPayment;
    }
}
