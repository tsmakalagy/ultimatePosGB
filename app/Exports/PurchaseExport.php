<?php

namespace App\Exports;
use App\PurchaseLine;

use Maatwebsite\Excel\Concerns\FromCollection;

class PurchaseExport implements FromCollection
{
   
    public function collection()
    {
        return PurchaseLine::all();
    }
}
