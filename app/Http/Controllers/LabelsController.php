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
        // $pl= CustomPurchaseLine->getBarcodeAttribute->get();
        // $pl= CustomPurchaseLine::where('id',120)->get();
    // dd($pl);        // for($i=0;$i < 4; $i++){
        //     $a=$i;
        // }
        // dd($a);

    //    $map=$this->map('128');
    // dd($map);
  

        try {
            $products = $request->get('products');
            $purchase = $request->get('purchase_id');
            $created = PurchaseLine::where('transaction_id', $purchase)->select('created_at', 'id')->first();
            $created_at = $created->created_at->format('Y-m-d H:i');
            $format_date = preg_replace('/[\s]+/mu', '', $created_at);
            $cd = array();
            $j=0;
           

            $print = $request->get('print');
            $barcode_setting = $request->get('barcode_setting');
            $business_id = $request->session()->get('user.business_id');

            
//            $format_date = date('Y/m/d H:i', strtotime($created_at));
            // dd($format_date);
            $barcode_details = Barcode::find($barcode_setting);
            $barcode_details->stickers_in_one_sheet = $barcode_details->is_continuous ? $barcode_details->stickers_in_one_row : $barcode_details->stickers_in_one_sheet;
            $barcode_details->paper_height = $barcode_details->is_continuous ? $barcode_details->height : $barcode_details->paper_height;

            if ($barcode_details->stickers_in_one_row == 1) {

                $barcode_details->col_distance = 0;
                $barcode_details->row_distance = 0;
            }
            // if($barcode_details->is_continuous){
            //     $barcode_details->row_distance = 0;
            // }

            $business_name = $request->session()->get('business.name');

            $product_details_page_wise = [];
            $total_qty = 0;
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
                // $qty=$value['quantity'];
                // for ($i=0; $i < $value['quantity']; $i++) {
                //  $details->sub_sku=$details->sub_sku.$i;
                $purchaseLine = CustomPurchaseLine::query()->where('transaction_id', $purchase)->where('product_id',$value['product_id'])->get();
                $auth = CustomPurchaseLine::where('transaction_id', $purchase)->where('product_id',$value['product_id'])->get();
                 $authe=$auth[0]->getBarcodeAttribute();
                // dd($authe);
                // echo($authe);
                    //  dd($authe[0]);
                for ($i = 0; $i < $value['quantity']; $i++) {

                    // for($j = 0; $j < 20; $j++){
                    //     echo($i.$j);
                    // }

                    $page = intdiv($total_qty, $barcode_details->stickers_in_one_sheet);
                    // $page = $total_qty;

                    if ($total_qty % $barcode_details->stickers_in_one_sheet == 0) {
                        $product_details_page_wise[$page] = [];
                    }
                    // $details->sub_sku+=$i;
                //  $bar=$purchaseLine->barcode[$i];
                //  echo($bar);
                //  dd($bar);
                    //  $auths=$authe[$i];
                    //  $details->sub_sku='holljj';
                    $details->sub_sku=$details->sub_sku.$i;
                    $product_details_page_wise[$page][] = $details;
                    // $product_details_page_wise[$page][]->sub_sku=$product_details_page_wise[$page][]->sub_sku;
                    // $sub_sku=$details->sub_sku.$i;
                    
                    $total_qty++;
                    
                }

            }
 
            // dd($auth);
            $margin_top = $barcode_details->is_continuous ? 0 : $barcode_details->top_margin * 1;
            $margin_left = $barcode_details->is_continuous ? 0 : $barcode_details->left_margin * 1;
            $paper_width = $barcode_details->paper_width * 1;
            $paper_height = $barcode_details->paper_height * 1;

            // print_r($paper_height);
            // echo "==";
            // print_r($margin_left);exit;

            // $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 
            //             'format' => [$paper_width, $paper_height],
            //             'margin_top' => $margin_top,
            //             'margin_bottom' => $margin_top,
            //             'margin_left' => $margin_left,
            //             'margin_right' => $margin_left,
            //             'autoScriptToLang' => true,
            //             // 'disablePrintCSS' => true,
            // 'autoLangToFont' => true,
            // 'autoVietnamese' => true,
            // 'autoArabic' => true
            //             ]
            //         );
            //print_r($mpdf);exit;

            $i = 0;
            $len = count($product_details_page_wise);
            $is_first = false;
            $is_last = false;
            $a=0;

            //$original_aspect_ratio = 4;//(w/h)
            $factor = (($barcode_details->width / $barcode_details->height)) / ($barcode_details->is_continuous ? 2 : 4);
            $html = '';
            // dd($product_details_page_wise);
            // dd($quantity);
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
                    ->with(compact('print', 'count','cd' ,'authe','format_date', 'page_products', 'i', 'business_name', 'barcode_details', 'margin_top', 'margin_left', 'paper_width', 'paper_height', 'is_first', 'is_last', 'factor'))->render();
                print_r($output);
                //$mpdf->WriteHTML($output);

                // if($i < $len - 1){
                //     // '', '', '', '', '', '', $margin_left, $margin_left, $margin_top, $margin_top, '', '', '', '', '', '', 0, 0, 0, 0, '', [$barcode_details->paper_width*1, $barcode_details->paper_height*1]
                //     $mpdf->AddPage();
                // }

                $i++;
            }

            print_r('<script>window.print()</script>');
            exit;
            //return $output;

            //$mpdf->Output();

            // $page_height = null;
            // if ($barcode_details->is_continuous) {
            //     $rows = ceil($total_qty/$barcode_details->stickers_in_one_row) + 0.4;
            //     $barcode_details->paper_height = $barcode_details->top_margin + ($rows*$barcode_details->height) + ($rows*$barcode_details->row_distance);
            // }

            // $output = view('labels.partials.preview')
            //     ->with(compact('print', 'product_details', 'business_name', 'barcode_details', 'product_details_page_wise'))->render();

            // $output = ['html' => $html,
            //                 'success' => true,
            //                 'msg' => ''
            //             ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = __('lang_v1.barcode_label_error');
        }

        //return $output;
    }


    /**
     * Returns the html for labels preview
     *
     * @return \Illuminate\Http\Response
     */
    public function mypreview(Request $request)
    {
        try {
            $products = $request->get('products');
            $purchase = $request->get('purchase_id');

            $print = $request->get('print');
            $barcode_setting = $request->get('barcode_setting');
            $business_id = $request->session()->get('user.business_id');

            $created = PurchaseLine::where('transaction_id', $purchase)->select('created_at', 'id')->first();
            $created_at = $created->created_at;
            $format_date = date('Y/m/d H:i', strtotime($created_at));
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

                for ($i = 0; $i < $value['quantity']; $i++) {

                    $page = intdiv($total_qty, $barcode_details->stickers_in_one_sheet);
                    // $page = $total_qty;

                    if ($total_qty % $barcode_details->stickers_in_one_sheet == 0) {
                        $product_details_page_wise[$page] = [];
                    }

                    $product_details_page_wise[$page][] = $details;
                    $total_qty++;

                }
            }
            $margin_top = $barcode_details->is_continuous ? 0 : $barcode_details->top_margin * 1;
            $margin_left = $barcode_details->is_continuous ? 0 : $barcode_details->left_margin * 1;
            $paper_width = $barcode_details->paper_width * 1;
            $paper_height = $barcode_details->paper_height * 1;

            $i = 0;
            $len = count($product_details_page_wise);
            $is_first = false;
            $is_last = false;

            //$original_aspect_ratio = 4;//(w/h)
            $factor = (($barcode_details->width / $barcode_details->height)) / ($barcode_details->is_continuous ? 2 : 4);
            $html = '';
            foreach ($product_details_page_wise as $page => $page_products) {

                if ($i == 0) {
                    $is_first = true;
                }

                if ($i == $len - 1) {
                    $is_last = true;
                }
                $count = count($page_products);

                $output = view('labels.partials.preview_2')
                    ->with(compact('print', 'count', 'format_date', 'page_products', 'i', 'business_name', 'barcode_details', 'margin_top', 'margin_left', 'paper_width', 'paper_height', 'is_first', 'is_last', 'factor'))->render();


                $i++;
            }

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = __('lang_v1.barcode_label_error');
        }

        //return $output;
    }
}
