<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PriceProduct;
use App\ProductPriceSetting;
use App\ProductPrice;

use App\Account;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\Image;
use App\ShippingFee;
use App\Shipper;
use App\Province;
use App\CentreVille;
use App\Address;
use App\ShipperType;
use App\CustomerGroup;
use App\InvoiceScheme;
use App\SellingPriceGroup;
use App\TaxRate;
use App\Transaction;
use App\TransactionSellLine;
use App\TypesOfService;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Warranty;
use DB;
use App\Http\Requests\ProfilRequest;

use Yajra\DataTables\Facades\DataTables;
use App\Product;
use App\Media;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Validation\Rule;

class ShippingFeeController extends Controller
{

    /**
     * All Utils instance.
     *
     */
    protected $contactUtil;
    protected $businessUtil;
    protected $transactionUtil;
    protected $productUtil;


    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ContactUtil $contactUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil)
    {
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;

        $this->dummyPaymentLine = ['method' => '', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => ''];

        $this->shipping_status_colors = [
            'ordered' => 'bg-yellow',
            'packed' => 'bg-info',
            'shipped' => 'bg-navy',
            'delivered' => 'bg-green',
            'cancelled' => 'bg-red',
        ];
    }


    public function index(){
        //  $price_product=ProductPriceSetting::all();
        //   dd($price_product);
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (!$is_admin && !auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'so.view_all', 'so.view_own'])) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');
        $is_tables_enabled = $this->transactionUtil->isModuleEnabled('tables');
        $is_service_staff_enabled = $this->transactionUtil->isModuleEnabled('service_staff');
        $is_types_service_enabled = $this->moduleUtil->isModuleEnabled('types_of_service');

        if (request()->ajax()) {
            $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
            $with = [];
            $shipping_statuses = $this->transactionUtil->shipping_statuses();

            $sale_type = !empty(request()->input('sale_type')) ? request()->input('sale_type') : 'sell';

            $shipping_fee = $this->transactionUtil->getShippingFee();
           
            $only_shipments = request()->only_shipments == 'true' ? true : false;
     
            $datatable = Datatables::of( $shipping_fee)
                ->addColumn(
                    'action',
                    function ($row) use ($only_shipments, $is_admin, $sale_type) {
                        $html = '<div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                      
                        if (auth()->user()->can('product.update')) {
                            $html .=
                                '<li><a target="_blank" href="' . action('ShippingFeeController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        if (auth()->user()->can('sell.delete')) {
                            $html .=
                                '<li><a href="' . action('ShippingFeeController@delete', [$row->id]) . '" class="delete-sell"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }

                       

                        if (config('constants.enable_download_pdf') && auth()->user()->can("print_invoice") && $sale_type != 'sales_order') {
                            $html .= '<li><a href="' . route('sell.downloadPdf', [$row->id]) . '" target="_blank"><i class="fas fa-print" aria-hidden="true"></i> ' . __("lang_v1.download_pdf") . '</a></li>';

                            if (!empty($row->shipping_status)) {
                                $html .= '<li><a href="' . route('packing.downloadPdf', [$row->id]) . '" target="_blank"><i class="fas fa-print" aria-hidden="true"></i> ' . __("lang_v1.download_paking_pdf") . '</a></li>';
                            }
                        }

                        if (auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access")) {
                            if (!empty($row->document)) {
                                $document_name = !empty(explode("_", $row->document, 2)[1]) ? explode("_", $row->document, 2)[1] : $row->document;
                                $html .= '<li><a href="' . url('uploads/documents/' . $row->document) . '" download="' . $document_name . '"><i class="fas fa-download" aria-hidden="true"></i>' . __("purchase.download_document") . '</a></li>';
                                if (isFileImage($document_name)) {
                                    $html .= '<li><a href="#" data-href="' . url('uploads/documents/' . $row->document) . '" class="view_uploaded_document"><i class="fas fa-image" aria-hidden="true"></i>' . __("lang_v1.view_document") . '</a></li>';
                                }
                            }
                        }
                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->removeColumn('id')
        
             
                ->editColumn('price',
                        '<span class="cours_usd" data-orig-value="{{$price}}">@if(!empty($price)) {{$price}} @endif   </span>')
                        ->editColumn('type',
                        '<span class="cours_rmb" data-orig-value="{{$type}}">@if(!empty($type)) {{$type}} @endif   </span>')
                       
                ->editColumn(
                    'types_of_service_name',
                    '<span class="service-type-label" data-orig-value="" data-status-name=""></span>'
                )
                ->addColumn('total_remaining', function ($row) {
                    $total_remaining = '';

                    return $total_remaining;
                })
                ->addColumn('return_due', function ($row) {
                    $return_due_html = '';


                    return $return_due_html;
                })
                ->editColumn('invoice_no', function ($row) {
                    $invoice_no = '';


                    return $invoice_no;
                })
                ->editColumn('shipping_status', function ($row) use ($shipping_statuses) {
                    $status = '';
                    return $status;
                })
                ->addColumn('name', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('type', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('price', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('other_details', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('cours_usd', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('cours_rmb', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('frais_taxe_usd_bateau', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('frais_taxe_usd_avion', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('frais_usd_bateau', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('frais_compagnie_usd_bateau', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('constante_taxe', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('conatct_name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$name}}')
                ->editColumn('total_items', '')
              
     
                ->addColumn('payment_methods', function ($row) use ($payment_types) {
                    $html = '';

                    return $html;
                })
       
 
                          
                ->editColumn('so_qty_remaining', '')
           
                // ->setRowAttr([
                //     'data-href' => function ($row) {
                //         if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                //             return action('ShipperController@show', [$row->id]);
                //         } else {
                //             return '';
                //         }
                    // }])
                    ;
            $rawColumns = ['final_total','cours_usd','cours_rmb','frais_taxe_usd_avion','frais_taxe_usd_bateau','frais_compagnie_usd_bateau','constante_taxe','frais_usd_bateau', 'action', 'price', 'type', 'name', 'other_details', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status', 'types_of_service_name', 'payment_methods', 'return_due', 'conatct_name', 'status'];

            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $sales_representative = User::forDropdown($business_id, false, false, true);

        //Commission agent filter
        $is_cmsn_agent_enabled = request()->session()->get('business.sales_cmsn_agnt');
        $commission_agents = [];
        if (!empty($is_cmsn_agent_enabled)) {
            $commission_agents = User::forDropdown($business_id, false, true, true);
        }

        //Service staff filter
        $service_staffs = null;
        if ($this->productUtil->isModuleEnabled('service_staff')) {
            $service_staffs = $this->productUtil->serviceStaffDropdown($business_id);
        }

        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        return view('shipping_fee.index')
            ->with(compact('business_locations', 'customers', 'is_woocommerce', 'sales_representative', 'is_cmsn_agent_enabled', 'commission_agents', 'service_staffs', 'is_tables_enabled', 'is_service_staff_enabled', 'is_types_service_enabled', 'shipping_statuses'));

       
            }

    

     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create') && !auth()->user()->can('customer.view_own') && !auth()->user()->can('supplier.view_own')) {
            abort(403, 'Unauthorized action.');
        }
        //$product_price_setting = ProductPriceSetting::first();
        //dd($product_price_setting);
         

        return view('shipping_fee.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
 
            if($request->filled('type','price')){
                $type=$request->input('type');
                $price=$request->input('price');            
                $shipping_fee= ShippingFee::create(['type'=> $type,'price'=>$price]);
            return redirect()->route('Shipping_fee.index');
            
           
        }
        else{
            return redirect()->route('Shipping_fee.index');
        }
    }

    /**
     * formulaire d'edition
     * @param $id
     * @return view
     */
    public function edit($id)
    {
        $shipping_fee =  ShippingFee::findOrFail($id);
       // $shipper_types = ShipperType::pluck('type', 'id');


        return view('shipping_fee.edit', compact('shipping_fee'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $shipping_fee =  ShippingFee::findOrFail($id);
        $shipping_fee->update($request->all());
        return redirect()->route('Shipping_fee.index');
    }

    /**
     * gestion de supprimation
     *
     * @return redirect
     */
    public function delete($id)
    {

        $shipping_fee = ShippingFee::findOrFail($id);
        $shipping_fee->delete();
        return redirect()->route('ProductPriceSetting.index');

    }

}

