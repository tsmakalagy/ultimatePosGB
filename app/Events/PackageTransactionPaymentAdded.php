<?php

namespace App\Events;

use App\PackageTransactionPayment;
use Illuminate\Queue\SerializesModels;

class PackageTransactionPaymentAdded
{
    use SerializesModels;

    public $packageTransactionPayment;
    public $formInput;

    /**
     * Create a new event instance.
     *
     * @param  Order  $order
     * @param  array $formInput = []
     * @return void
     */
    public function __construct(PackageTransactionPayment $packageTransactionPayment, $formInput = [])
    {
        $this->packageTransactionPayment = $packageTransactionPayment;
        $this->formInput = $formInput;
    }
}
