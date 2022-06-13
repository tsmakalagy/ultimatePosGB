<?php

namespace App\Exports;
use App\CustomPurchaseLine;
use App\PurchaseLine;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomPurchaseExport implements FromQuery, WithHeadings, ShouldAutoSize, WithMapping
{

    use Exportable;

    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function query()
    {
        $purchaseLine = CustomPurchaseLine::query()->where('transaction_id', $this->id);

        return $purchaseLine;
    }

    public function map($purchaseLine): array
    {
        $ret = array();
        for ($i = 0; $i < $purchaseLine->quantity; $i++) {
            array_push($ret, array(
                'barcode' => $purchaseLine->barcode[$i],
                'product_name' => $purchaseLine->product->name,
                'quantity' => 1,
                'weight' => $purchaseLine->product->weight,
                'L' => $purchaseLine->product->product_custom_field2,
                'W' => $purchaseLine->product->product_custom_field3,
                'H' => $purchaseLine->product->product_custom_field4,
            ));
        }
        return $ret;
    }

    public function headings(): array
    {
        return [
            'Barcode',
            'Name',
            'Quantity',
            'Weight',
            'Length',
            'Width',
            'Height',
        ];
    }
}
