<?php

namespace App\Http\Controllers;

use App\Barcode;
use App\CustomPurchaseLine;
use App\Product;
use App\PurchaseLine;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabelsController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $transactionUtil;
    protected $productUtil;

    /**
     * Constructor
     *
     * @param TransactionUtil $TransactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
    }

    /**
     * Display labels
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $purchase_id = $request->get('purchase_id', false);
        $product_id = $request->get('product_id', false);

        //Get products for the business
        $products = [];
        if ($purchase_id) {
            $products = $this->transactionUtil->getPurchaseProducts($business_id, $purchase_id);
        } elseif ($product_id) {
            $products = $this->productUtil->getDetailsFromProduct($business_id, $product_id);
        }

        $barcode_settings = Barcode::where('business_id', $business_id)
            ->orWhereNull('business_id')
            ->select(DB::raw('CONCAT(name, ", ", COALESCE(description, "")) as name, id, is_default'))
            ->get();
        $default = $barcode_settings->where('is_default', 1)->first();
        $barcode_settings = $barcode_settings->pluck('name', 'id');

        return view('labels.show')
            ->with(compact('products', 'purchase_id', 'barcode_settings', 'default'));
    }

    /**
     * Returns the html for product row
     *
     * @return \Illuminate\Http\Response
     */
    public function addProductRow(Request $request)
    {
        if ($request->ajax()) {
            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $business_id = $request->session()->get('user.business_id');

            if (!empty($product_id)) {
                $index = $request->input('row_count');
                $products = $this->productUtil->getDetailsFromProduct($business_id, $product_id, $variation_id);

                return view('labels.partials.show_table_rows')
                    ->with(compact('products', 'index'));
            }
        }
    }

    // public function map($id): array
    // {
    //     // $purchaseLine = CustomPurchaseLine::query()->where('transaction_id', $id)->first();
    //     // $ret = array();
    //     // for ($i = 0; $i < $purchaseLine->quantity; $i++) {
    //     //     array_push($ret, array(
    //     //         'barcode' => $purchaseLine->barcode[$i],
    //     //         'product_name' => $purchaseLine->product->name,
    //     //         'quantity' => 1,
    //     //         'weight' => $purchaseLine->product->weight,
    //     //         'L' => $purchaseLine->product->product_custom_field2,
    //     //         'W' => $purchaseLine->product->product_custom_field3,
    //     //         'H' => $purchaseLine->product->product_custom_field4,
    //     //     ));
    //     // }
    //     // return $ret;
    // }


    /**
     * Returns the html for labels preview
     *
     * @return \Illuminate\Http\Response
     */
    public function preview(Request $request)
    {
        try {
            $products = $request->get('products');
            
            $purchase = $request->get('purchase_id') ;
            $created = PurchaseLine::where('transaction_id', $purchase)->select('created_at', 'id')->first();
            $created_at = $created ? $created->created_at->format('Y-m-d H:i') :'';
            $format_date = preg_replace('/[\s]+/mu', '', $created_at);
            $cd = array();
            $j=0;
           

            $print = $request->get('print');
            $barcode_setting = $request->get('barcode_setting');
            $business_id = $request->session()->get('user.business_id');          

            $barcode_details = Barcode::find($barcode_setting);
            $barcode_details->stickers_in_one_sheet = $barcode_details->is_continuous ? $barcode_details->stickers_in_one_row : $barcode_details->stickers_in_one_sheet;
            $barcode_details->paper_height = $barcode_details->is_continuous ? $barcode_details->height : $barcode_details->paper_height;

            if ($barcode_details->stickers_in_one_row == 1) {

                $barcode_details->col_distance = 0;
                $barcode_details->row_distance = 0;
            }

            $business_name = $request->session()->get('business.name');

            $product_details_page_wise = [];
            $total_qty = 0;

            $barcodes = array();

            foreach ($products as $value) {
                
                $details = $this->productUtil->getDetailsFromVariation($value['variation_id'], $business_id, null, false);
                if (!empty($value['exp_date'])) {
                    $details->exp_date = $value['exp_date'];
                }
                if (!empty($value['packing_date'])) {
                    $details->packing_date = $value['packing_date'];
                }
                if (!empty($value['lot_number'])) {
                    $details->lot_number = $value['lot_number'];
                }


                for ($kk = 0; $kk < $value['quantity']; $kk++) {

                    $j = $kk + 1;

                    $bc = $created_at ? $details->sku . '-' . $j . '-' . $created_at : $details->sku . '-' . $j;
                    $bc = preg_replace('/[\s]+/mu', '', $bc);
                    array_push($barcodes, $bc);


                    // for($j = 0; $j < 20; $j++){
                    //     echo($i.$j);
                    // }

                    $page = intdiv($total_qty, $barcode_details->stickers_in_one_sheet);
                    // $page = $total_qty;

                    if ($total_qty % $barcode_details->stickers_in_one_sheet == 0) {
                        $product_details_page_wise[$page] = [];
                    }
                                 
                    $product_details_page_wise[$page][] = $details;
                   $total_qty++;
                    
                }

            }
 
            // dd($auth);
            $margin_top = $barcode_details->is_continuous ? 0 : $barcode_details->top_margin * 1;
            $margin_left = $barcode_details->is_continuous ? 0 : $barcode_details->left_margin * 1;
            $paper_width = $barcode_details->paper_width * 1;
            $paper_height = $barcode_details->paper_height * 1;


            $i = 0;
            $len = count($product_details_page_wise);
            $is_first = false;
            $is_last = false;
            $a=0;

            //$original_aspect_ratio = 4;//(w/h)
            $factor = (($barcode_details->width / $barcode_details->height)) / ($barcode_details->is_continuous ? 2 : 4);
            $html = '';
            $pl = 0;
            foreach ($product_details_page_wise as $page => $page_products) {
            
                // foreach ($products as $prod){
                //     $qty=$prod['quantity'];
                //     for($i=0;$i<$qty;$i++){
                //         array_push($cd,$prod->sku.'-'.$i.'-'.$format_date);
                //     }
                // }

                if ($i == 0) {
                    $is_first = true;
                }

                if ($i == $len - 1) {
                    $is_last = true;
                }
                // dd($product_details_page[]);
                $count = count($page_products);

                $output = view('labels.partials.preview_2')

                    ->with(compact('print', 'count', 'barcodes', 'pl', 'format_date', 'page_products', 'i', 'business_name', 'barcode_details', 'margin_top', 'margin_left', 'paper_width', 'paper_height', 'is_first', 'is_last', 'factor'))->render();
                print_r($output);

                $i++;
                $pl = $pl + $count;
            }

            print_r('<script>window.print()</script>');
            exit;
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = __('lang_v1.barcode_label_error');
        }

        //return $output;
    }

}
