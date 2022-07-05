<?php

namespace App\Exports;
use App\CustomPurchaseLine;
use App\PackingList;
use App\PurchaseLine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PackinglistsExport implements FromQuery, WithHeadings, ShouldAutoSize, WithMapping
{
    use Exportable;

    private $id;

    public function __construct($id)
    {
        $this->id = $id;
        
    }


    public function query()
    {
        //$package = PackingList::findOrFail($this->id);
         $package = PackingList::query()->where('packing_lists.id', $this->id);

        return $package;
    }

    public function map($package): array
    {
        $ret = array();
        foreach ($package->packinglist_lines as $packinglistlines){
            for ($i = 0; $i < $packinglistlines->qte; $i++){
                $id = str_pad($i, 2, '0', STR_PAD_LEFT);
                $barcode = $packinglistlines->thepackage->sku . $id;
                $arr = [];
                $arr2 = [];
                $arr3 = [];
                $arr4 = [];
                $product = $packinglistlines->thepackage->product;
                $packing = $packinglistlines->thepackage->thepackage_package;
                
                foreach ($packing as $pack) {
                    $prod = $pack->product;
                    $prod2 = $pack->customer_name;
                    $prod3 = $pack->customer_tel;
                    array_push($arr, $prod);
                    array_push($arr3, $prod2);
                    array_push($arr4, $prod3);
                    //  return $packing;
                }
                $ar = implode(',', $arr);
                $new_arr = $product . ' ' . $ar;
                array_push($arr2, $new_arr);
                $length = count($arr3);
                $result = implode(',', $arr2);
                if($length > 1 )
                {
                $result2 = implode(', ', $arr3);
                $result3 = implode(', ', $arr4);
                }
                else{
                    $result2 =implode( '',$arr3);
                    $result3 = implode( '',$arr4);  
                }
             array_push($ret, array(
                'customer' => $result2,
                'contact' => $result3,
                'barcode' => $barcode,
                'product' => $result,
                'quantity' => 1,
               
                'L' => $packinglistlines->thepackage->longueur,
                'W' =>$packinglistlines->thepackage->largeur,
                'H' => $packinglistlines->thepackage->hauteur,
                 'volume' => $packinglistlines->thepackage->volume,
                  'weight' => $packinglistlines->thepackage->weight
                  ));
                  }
        }
        // $ret = array();
        // for ($i = 0; $i < $purchaseLine->quantity; $i++) {
        //     array_push($ret, array(
        //         'barcode' => $purchaseLine->barcode[$i],
        //         'product_name' => $purchaseLine->product->name,
        //         'quantity' => 1,
        //         'weight' => $purchaseLine->product->weight,
        //         'L' => $purchaseLine->product->product_custom_field2,
        //         'W' => $purchaseLine->product->product_custom_field3,
        //         'H' => $purchaseLine->product->product_custom_field4,
        //     ));
        // }
        return $ret;
    }

    public function headings(): array
    {
        return [
            'Customer',
            'Contact',
            'Barcode',
            'Product',
            'Qte',
            'Length',
            'Width',
            'Height',
            'Volume',
            'Weight',
        ];
    }
}
