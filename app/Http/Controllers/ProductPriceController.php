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

class ProductPriceController extends Controller
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

            $sells = $this->transactionUtil->getListSells($business_id, $sale_type);

            $shippers = $this->transactionUtil->getListShippers();
            $price_product = $this->transactionUtil->getProductPriceSettings();
            $product_price = $this->transactionUtil->getProductPrice();



            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            //Add condition for created_by,used in sales representative sales report
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (!empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }

            $partial_permissions = ['view_own_sell_only', 'view_commission_agent_sell', 'access_own_shipping', 'access_commission_agent_shipping'];
            if (!auth()->user()->can('direct_sell.view')) {
                $sells->where(function ($q) {
                    if (auth()->user()->hasAnyPermission(['view_own_sell_only', 'access_own_shipping'])) {
                        $q->where('transactions.created_by', request()->session()->get('user.id'));
                    }

                    //if user is commission agent display only assigned sells
                    if (auth()->user()->hasAnyPermission(['view_commission_agent_sell', 'access_commission_agent_shipping'])) {
                        $q->orWhere('transactions.commission_agent', request()->session()->get('user.id'));
                    }
                });
            }

            $only_shipments = request()->only_shipments == 'true' ? true : false;
            if ($only_shipments) {
                $sells->whereNotNull('transactions.shipping_status');

                if (auth()->user()->hasAnyPermission(['access_pending_shipments_only'])) {
                    $sells->where('transactions.shipping_status', '!=', 'delivered');
                }
            }

            if (!$is_admin && !$only_shipments && $sale_type != 'sales_order') {
                $payment_status_arr = [];
                if (auth()->user()->can('view_paid_sells_only')) {
                    $payment_status_arr[] = 'paid';
                }

                if (auth()->user()->can('view_due_sells_only')) {
                    $payment_status_arr[] = 'due';
                }

                if (auth()->user()->can('view_partial_sells_only')) {
                    $payment_status_arr[] = 'partial';
                }

                if (empty($payment_status_arr)) {
                    if (auth()->user()->can('view_overdue_sells_only')) {
                        $sells->OverDue();
                    }
                } else {
                    if (auth()->user()->can('view_overdue_sells_only')) {
                        $sells->where(function ($q) use ($payment_status_arr) {
                            $q->whereIn('transactions.payment_status', $payment_status_arr)
                                ->orWhere(function ($qr) {
                                    $qr->OverDue();
                                });

                        });
                    } else {
                        $sells->whereIn('transactions.payment_status', $payment_status_arr);
                    }
                }
            }


            if (!empty(request()->input('payment_status')) && request()->input('payment_status') != 'overdue') {
                $sells->where('transactions.payment_status', request()->input('payment_status'));
            } elseif (request()->input('payment_status') == 'overdue') {
                $sells->whereIn('transactions.payment_status', ['due', 'partial'])
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
            }


            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (!empty(request()->input('rewards_only')) && request()->input('rewards_only') == true) {
                $sells->where(function ($q) {
                    $q->whereNotNull('transactions.rp_earned')
                        ->orWhere('transactions.rp_redeemed', '>', 0);
                });
            }

            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $sells->whereDate('transactions.transaction_date', '>=', $start)
                    ->whereDate('transactions.transaction_date', '<=', $end);
            }

            //Check is_direct sell
            if (request()->has('is_direct_sale')) {
                $is_direct_sale = request()->is_direct_sale;
                if ($is_direct_sale == 0) {
                    $sells->where('transactions.is_direct_sale', 0);
                    $sells->whereNull('transactions.sub_type');
                }
            }

            //Add condition for commission_agent,used in sales representative sales with commission report
            if (request()->has('commission_agent')) {
                $commission_agent = request()->get('commission_agent');
                if (!empty($commission_agent)) {
                    $sells->where('transactions.commission_agent', $commission_agent);
                }
            }

            if ($is_woocommerce) {
                $sells->addSelect('transactions.woocommerce_order_id');
                if (request()->only_woocommerce_sells) {
                    $sells->whereNotNull('transactions.woocommerce_order_id');
                }
            }

            if (request()->only_subscriptions) {
                $sells->where(function ($q) {
                    $q->whereNotNull('transactions.recur_parent_id')
                        ->orWhere('transactions.is_recurring', 1);
                });
            }

            if (!empty(request()->list_for) && request()->list_for == 'service_staff_report') {
                $sells->whereNotNull('transactions.res_waiter_id');
            }

            if (!empty(request()->res_waiter_id)) {
                $sells->where('transactions.res_waiter_id', request()->res_waiter_id);
            }

            if (!empty(request()->input('sub_type'))) {
                $sells->where('transactions.sub_type', request()->input('sub_type'));
            }

            if (!empty(request()->input('created_by'))) {
                $sells->where('transactions.created_by', request()->input('created_by'));
            }

            if (!empty(request()->input('status'))) {
                $sells->where('transactions.status', request()->input('status'));
            }

            if (!empty(request()->input('sales_cmsn_agnt'))) {
                $sells->where('transactions.commission_agent', request()->input('sales_cmsn_agnt'));
            }

            if (!empty(request()->input('service_staffs'))) {
                $sells->where('transactions.res_waiter_id', request()->input('service_staffs'));
            }

            if (!empty(request()->input('shipping_status'))) {
                $sells->where('transactions.shipping_status', request()->input('shipping_status'));
            }

            if (!empty(request()->input('for_dashboard_sales_order'))) {
                $sells->whereIn('transactions.status', ['partial', 'ordered'])
                    ->orHavingRaw('so_qty_remaining > 0');
            }

            if ($sale_type == 'sales_order') {
                if (!auth()->user()->can('so.view_all') && auth()->user()->can('so.view_own')) {
                    $sells->where('transactions.created_by', request()->session()->get('user.id'));
                }
            }

            $sells->groupBy('transactions.id');

            if (!empty(request()->suspended)) {
                $transaction_sub_type = request()->get('transaction_sub_type');
                if (!empty($transaction_sub_type)) {
                    $sells->where('transactions.sub_type', $transaction_sub_type);
                } else {
                    $sells->where('transactions.sub_type', null);
                }

                $with = ['sell_lines'];

                if ($is_tables_enabled) {
                    $with[] = 'table';
                }

                if ($is_service_staff_enabled) {
                    $with[] = 'service_staff';
                }

                $sales = $sells->where('transactions.is_suspend', 1)
                    ->with($with)
                    ->addSelect('transactions.is_suspend', 'transactions.res_table_id', 'transactions.res_waiter_id', 'transactions.additional_notes')
                    ->get();

                return view('sale_pos.partials.suspended_sales_modal')->with(compact('sales', 'is_tables_enabled', 'is_service_staff_enabled', 'transaction_sub_type'));
            }

            $with[] = 'payment_lines';
            if (!empty($with)) {
                $sells->with($with);
            }

            //$business_details = $this->businessUtil->getDetails($business_id);
            if ($this->businessUtil->isModuleEnabled('subscription')) {
                $sells->addSelect('transactions.is_recurring', 'transactions.recur_parent_id');
            }
            $sales_order_statuses = Transaction::sales_order_statuses();
            $datatable = Datatables::of( $product_price)
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
                                '<li><a target="_blank" href="' . action('ProductPriceController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        if (auth()->user()->can('sell.delete')) {
                            $html .=
                                '<li><a href="' . action('ProductPriceController@delete', [$row->id]) . '" class="delete-sell"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
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
                ->editColumn(
                    'final_total',
                    '<span class="final-total" data-orig-value=""></span>'
                )
                ->editColumn(
                    'tax_amount',
                    '<span class="total-tax" data-orig-value=""></span>'
                )
                ->editColumn(
                    'total_paid',
                    '<span class="total-paid" data-orig-value=""></span>'
                )
                ->editColumn(
                    'total_before_tax',
                    '<span class="total_before_tax" data-orig-value=""></span>'
                )
                ->editColumn(
                    'discount_amount',
                    function ($row) {


                        return '<span class="total-discount" data-orig-value=""> </span>';
                    }
                )
                ->editColumn('name',
                    '<span class="name" data-orig-value=""> </span>')
                ->editColumn('type',
                    '<span class="type" data-orig-value=""> </span>')
                ->editColumn('tel',
                    '<span class="tel">   </span>')
                ->editColumn('product_name',
                        '<span class="product_name" data-orig-value="{{$product_name}}">@if(!empty($product_name)) {{$product_name}} @endif   </span>')
                        ->editColumn('product_spec',
                        '<span class="product_spec" data-orig-value="{{$product_spec}}">@if(!empty($product_spec)) {{$product_spec}} @endif   </span>')
                        ->editColumn('china_price',
                        '<span class="china_price" data-orig-value="{{$china_price}}">@if(!empty($china_price)) {{$china_price}} @endif   </span>')
                        ->editColumn('kuaidi',
                        '<span class="kuaidi" data-orig-value="{{$kuaidi}}">@if(!empty($kuaidi)) {{$kuaidi}} @endif   </span>')
                        ->editColumn('size',
                        '<span class="size" data-orig-value="{{$size}}">@if(!empty($size)) {{$size}} @endif   </span>')
                        ->editColumn('volume',
                        '<span class="volume" data-orig-value="{{$volume}}">@if(!empty($volume)) {{$volume}} @endif   </span>')
                        ->editColumn('weight',
                        '<span class="weight" data-orig-value="{{$weight}}">@if(!empty($weight)) {{$weight}} @endif   </span>')
                        ->editColumn('other_field1',
                        '<span class="other_field1" data-orig-value="{{$other_field1}}">@if(!empty($other_field1)) {{$other_field1}} @endif   </span>')
                        ->editColumn('suggested_price',
                        '<span class="suggested_price" data-orig-value="{{$suggested_price}}">@if(!empty($suggested_price)) {{$suggested_price}} @endif   </span>')
                        ->editColumn('byship_price',
                        '<span class="byship_price" data-orig-value="{{$byship_price}}">@if(!empty($byship_price)) {{$byship_price}} @endif   </span>')
                        ->editColumn('byplane_price',
                        '<span class="byplane_price" data-orig-value="{{$byplane_price}}">@if(!empty($byplane_price)) {{$byplane_price}} @endif   </span>')
                        ->editColumn('link',
                        '<span class="link" data-orig-value="{{$link}}">@if(!empty($link)) {{$link}} @endif   </span>')
                       
                ->editColumn(
                    'payment_status',
                    function ($row) {

                        return '';
                    }
                )
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
                ->addColumn('tel', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('other_details', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('product_name', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('product_spec', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('china_price', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('kuaidi', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('size', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('volume', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('weight', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('link', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('other_field1', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('suggested_price', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('byship_price', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('byplane_price', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('conatct_name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$name}}')
                ->editColumn('total_items', '')
              
     
                ->addColumn('payment_methods', function ($row) use ($payment_types) {
                    $html = '';

                    return $html;
                })
                ->editColumn('status', function ($row) use ($sales_order_statuses, $is_admin) {
                    $status = '';
                    return $status;
                })
                ->filterColumn('product_name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('product_prices.product_name', 'like', "%{$keyword}%");
                    });
                })
                          
                ->editColumn('so_qty_remaining', '')
           
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                            return action('ShipperController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }]);

            $rawColumns = ['final_total','product_name','product_spec','china_price','kuaidi','size','volume','weight','link','other_field1','suggested_price','byship_price','byplane_price', 'action', 'tel', 'type', 'name', 'other_details', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status', 'types_of_service_name', 'payment_methods', 'return_due', 'conatct_name', 'status'];

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

        return view('product_price.index')
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
        //$shipper_types = ShipperType::pluck('type', 'id');
         $product_price_setting = ProductPriceSetting::first();
         

        return view('product_price.create', compact('product_price_setting'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
   
        if($request->filled('product_name')){
            $product_name=$request->input('product_name');
            $product_spec=$request->input('product_spec');
            $china_price=$request->input('china_price');
            $kuaidi=$request->input('kuaidi');
            $size=$request->input('size');
            $volume=$request->input('volume');
            $weight=$request->input('weight');
            $link=$request->input('link');
            $suggested_price=$request->input('reviens');
            $byship_price=$request->input('byship_price');
            $byplane_price=$request->input('byplane_price');
            $price_product= ProductPrice::firstOrCreate(['product_name'=> $product_name,'product_spec'=>$product_spec,'china_price'=>$china_price,'kuaidi'=>$kuaidi,'size'=>$size,'volume'=>  $volume,'weight'=> $weight,'link'=> $link,'suggested_price'=> $suggested_price,'byship_price'=> $byship_price,'byplane_price'=> $byplane_price]);
           
            return redirect()->route('ProductPrice.index');
            
           
        }
        else{
            return redirect()->route('ProductPrice.index');
        }
    }

    /**
     * formulaire d'edition
     * @param $id
     * @return view
     */
    public function edit($id)
    {
        $product_price_setting = ProductPriceSetting::first();
        $price_product =  ProductPrice::findOrFail($id);
       // $shipper_types = ShipperType::pluck('type', 'id');


        return view('product_price.edit', compact('price_product','product_price_setting'));

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

        $price_product =  ProductPrice::findOrFail($id);

        $product_name=$request->input('product_name');
        $product_spec=$request->input('product_spec');
        $china_price=$request->input('china_price');
        $kuaidi=$request->input('kuaidi');
        $size=$request->input('size');
        $volume=$request->input('volume');
        $weight=$request->input('weight');
        $link=$request->input('link');
        $suggested_price=$request->input('reviens');
        $byship_price=$request->input('byship_price');
        $byplane_price=$request->input('byplane_price');

        $price_product->update(['product_name'=> $product_name,'product_spec'=>$product_spec,'china_price'=>$china_price,'kuaidi'=>$kuaidi,'size'=>$size,'volume'=>  $volume,'weight'=> $weight,'link'=> $link,'suggested_price'=> $suggested_price,'byship_price'=> $byship_price,'byplane_price'=> $byplane_price]);
        return redirect()->route('ProductPrice.index');
    }

    /**
     * gestion de supprimation
     *
     * @return redirect
     */
    public function delete($id)
    {

        $price_product = ProductPrice::findOrFail($id);
        $price_product->delete();
        return redirect()->route('ProductPrice.index');

    }

}

