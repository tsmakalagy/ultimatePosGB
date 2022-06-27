<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PriceProduct;
use App\ProductPriceSetting;
use App\ProductPrice;
use App\Image;

use App\Account;
use App\Business;
use App\BusinessLocation;
use App\Contact;
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
use App\package;
use App\ThePackage;
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

class ThePackageController extends Controller
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

    public function index()
    {

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
            $package = $this->transactionUtil->getPackage();
            $the_package = $this->transactionUtil->getThePackage();


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
            $datatable = Datatables::of($the_package)
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

                        if (auth()->user()->can('sell.delete')) {
                            $html .=
                                '<li><a href="' . action('PackageController@show', [$row->id]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
                        }


                        if (auth()->user()->can('product.update')) {
                            $html .=
                                '<li><a target="_blank" href="' . action('PackageController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        if (auth()->user()->can('sell.delete')) {
                            $html .=
                                '<li><a href="' . action('PackageController@delete', [$row->id]) . '" class="delete-sell"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
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
                ->editColumn('product',
                    '<span class="product_name" data-orig-value="{{$product}}">@if(!empty($product)) {{$product}} @endif   </span>')
                ->editColumn('bar_code',
                    '<span class="product_spec" data-orig-value="{{$bar_code}}">@if(!empty($bar_code)) {{$bar_code}} @endif   </span>')
                // ->editColumn('customer_name',
                // '<span class="china_price" data-orig-value="{{customer_name}}">@if(!empty($customer_name)) {{$customer_name}} @endif   </span>')
                ->editColumn('customer_tel',
                    '<span class="china_price" data-orig-value="{{$customer_tel}}">@if(!empty($customer_tel)) {{$customer_tel}} @endif   </span>')
                ->editColumn('longueur',
                    '<span class="china_price" data-orig-value="{{$longueur}}">@if(!empty($longueur)) {{$longueur}} @endif   </span>')
                ->editColumn('largeur',
                    '<span class="china_price" data-orig-value="{{$largeur}}">@if(!empty($largeur)) {{$largeur}} @endif   </span>')
                ->editColumn('hauteur',
                    '<span class="china_price" data-orig-value="{{$hauteur}}">@if(!empty($hauteur)) {{$hauteur}} @endif   </span>')
                ->editColumn('weight',
                    '<span class="size" data-orig-value="{{$weight}}">@if(!empty($weight)) {{$weight}} @endif   </span>')
                ->editColumn(
                    'created_at',
                    function ($row) {
                        $created_at = $row->created_at ? $row->created_at->format('Y-m-d') : '';
                        $format_date = preg_replace('/[\s]+/mu', '', $created_at);
                        return '<span class="total-discount" data-orig-value="">' . $created_at . '</span>';
                    }
                )
                ->editColumn('image', function ($row) {
                    $image_url = Image::where('product_id', $row->id)->first();

                    if (!empty($image_url)) {
                        $img_src = $image_url->image;
                        $img_expl = explode('|', $img_src);
                        $img = asset('/uploads/img/' . rawurlencode($img_expl[0]));
                        return '<div style="display: flex;"><img src="' . $img . '"  class="product-thumbnail-small" ></div>';
                    } else {
                        // $img = asset('/img/default.png');
                        return '<button type="button" class="btn btn-block btn-xs btn-primary btn-modal" data-href="' . action('PackageController@uploadImg', [$row->id]) . '"  data-container=".uploadImg_modal">upload img</button>';

                    }

                })

                // ->editColumn('status',
                // '<span class="weight" data-orig-value="{{$status}}">@if(!empty($status)) {{$status}} @endif  </span>')
                ->editColumn('other_field1',
                    '<span class="other_field1" data-orig-value="{{$other_field1}}">@if(!empty($other_field1)) {{$other_field1}} @endif   </span>')
                ->editColumn('other_field2',
                    '<span class="other_field2" data-orig-value="{{$other_field2}}">@if(!empty($other_field2)) {{$other_field2}} @endif   </span>')
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
                ->addColumn('product', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('created_at', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('customer_tel', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('longueur', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('largeur', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('hauteur', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('weight', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('status', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('other_field1', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('other_field2', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('bar_code', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('conatct_name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif ')
                ->editColumn('total_items', '')
                ->addColumn('payment_methods', function ($row) use ($payment_types) {
                    $html = '';

                    return $html;
                })
                ->filterColumn('status', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where(DB::raw(" IF(packages.status = 0, 'entrant', 'sortant')"), 'like', "%{$keyword}%");
                    });
                })
                ->editColumn('so_qty_remaining', '')
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                            return action('PackageController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }]);

            $rawColumns = ['final_total', 'product', 'created_at', 'customer_tel', 'longueur', 'largeur', 'bar_code', 'hauteur', 'weight', 'image', 'status', 'other_field1', 'other_field2', 'action', 'tel', 'type', 'other_details', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status', 'types_of_service_name', 'payment_methods', 'return_due', 'conatct_name'];

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

        return view('the_package.index')
            ->with(compact('business_locations', 'customers', 'is_woocommerce', 'sales_representative', 'is_cmsn_agent_enabled', 'commission_agents', 'service_staffs', 'is_tables_enabled', 'is_service_staff_enabled', 'is_types_service_enabled', 'shipping_statuses'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        if (request()->ajax()) {

            $contact = Contact::pluck('name', 'id');

            return response()->json(['url' => url('/my-package/create'), 'package' => $contact]);
        }

        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create') && !auth()->user()->can('customer.view_own') && !auth()->user()->can('supplier.view_own')) {
            abort(403, 'Unauthorized action.');
        }
        //   $barcode=$request->get('barcode');
        //   if(!empty($barcode)){
        //  $contact = Contact::pluck('name', 'id');
        //   $product_price_setting = ProductPriceSetting::first();


        // return view('the_package.create', compact('product_price_setting','barcode'));
        //   }
        //   else{
        //     return redirect()->route('ThePackage.index');
        //   }
        $package = Package::pluck('bar_code', 'id');

        return view('the_package.create_tmp', compact('package'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = $request->input('product');
        $product = $request->input('product');
        $packages = $request->input('packages');
        $arr = array();
        if ($request->has('packages')) {
            foreach ($packages as $package) {
                $impl = implode(':', $package);
                array_push($arr, $impl);
                // dd($package);

            }
        }
        $implod = implode(',', $arr);

        $patterns = '/\r\n/';
        $replacements = ',';
        $pr = preg_replace($patterns, $replacements, $product);
        $concat_product = $implod . ',' . $pr;
        // dd($concat);
        // $bar_code=$request->input('bar_code');
        $customer_name = $request->input('customer_name');
        $customer_tel = $request->input('customer_tel');
        $longueur = $request->input('longueur');
        $largeur = $request->input('largeur');
        $hauteur = $request->input('hauteur');
        $volume = $request->input('volume');
        $image = "image";
        $status = $request->input('status');
        $weight = $request->input('weight');
        $other_field1 = $request->input('other_field1');
        $other_field2 = $request->input('other_field2');
        $bar_code = '234444';


        $package = ThePackage::firstOrCreate(['product' => $concat_product, 'bar_code' => $bar_code, 'customer_tel' => $customer_tel, 'customer_name' => $customer_name, 'longueur' => $longueur, 'largeur' => $largeur, 'hauteur' => $hauteur, 'weight' => $weight, 'image' => $image, 'volume' => $volume, 'status' => $status, 'other_field1' => $other_field1, 'other_field2' => $other_field2]);


        $destinationPath = 'uploads/img/';
        $array = array();
        if ($request->has('images')) {
            foreach ($request->file('images') as $image) {
                $original_name = $package->product . '-' . $package->id . '-' . $image->getClientOriginalName();
                array_push($array, $original_name);
                $image->move($destinationPath, $original_name);

            }
            $create_image = Image::create(['product_id' => $package->id, 'image' => implode('|', $array)]);

        }

        return redirect()->route('ThePackage.index');


    }


    /**
     * Display the specified resource.
     *
     * @param \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }

        $package = Package::where('packages.id', $id)->first();
        $contact = Contact::pluck('name', 'id');

        $image_url = Image::where('product_id', $id)->first();

        return view('the_package.view-modal')->with(compact('package', 'contact', 'image_url'));
    }

    /**
     * formulaire d'edition
     * @param $id
     * @return view
     */
    public function edit($id)
    {
        $product_price_setting = ProductPriceSetting::first();
        $package = Package::findOrFail($id);
        $contact = Contact::pluck('name', 'id');

        return view('the_package.edit', compact('package', 'product_price_setting', 'contact'));

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

        $package = Package::findOrFail($id);

        $image_id = Image::where('product_id', $id)->first();

        $product = $request->input('product');
        $bar_code = $request->input('bar_code');
        $customer_name = $request->input('customer_name');
        $customer_tel = $request->input('customer_tel');
        $longueur = $request->input('longueur');
        $largeur = $request->input('largeur');
        $hauteur = $request->input('heuteur');
        $image = 'image';
        $status = $request->input('status');
        $weight = $request->input('weight');
        $other_field1 = $request->input('other_field1');
        $other_field2 = $request->input('other_field2');

        $package->update(['product' => $product, 'bar_code' => $bar_code, 'customer_name' => $customer_name, 'customer_tel' => $customer_tel, 'longueur' => $longueur, 'largeur' => $largeur, 'hauteur' => $hauteur, 'weight' => $weight, 'image' => $image, 'status' => $status, 'other_field1' => $other_field1, 'other_field2' => $other_field2]);
        $destinationPath = 'uploads/img/';
        $array = array();
        if ($request->has('images')) {
            foreach ($request->file('images') as $image) {
                $original_name = $package->product . '-' . $package->id . '-' . $image->getClientOriginalName();
                array_push($array, $original_name);
                $image->move($destinationPath, $original_name);
            }
            if (!empty($image_id)) {
                $create_image = $image_id->update(['product_id' => $package->id, 'image' => implode('|', $array)]);
            } else {
                $create_image = Image::create(['product_id' => $package->id, 'image' => implode('|', $array)]);

            }
        }


        return redirect()->route('ThePackage.index');
    }

    /**
     * gestion de supprimation
     *
     * @return redirect
     */
    public function delete($id)
    {

        $package = Package::findOrFail($id);
        $package->delete();
        return redirect()->route('ThePackage.index');

    }

    /**
     * formulaire d'edition
     * @param $id
     * @return view
     */
    public function scan()
    {

        $package = Package::pluck('bar_code', 'id');
        return view('the_package.scan-modal', compact('package'));

    }

    /**
     * formulaire d'edition
     * @param $id
     * @return view
     */
    public function uploadImg($id)
    {

        return view('the_package.uploadImg-modal', compact('id'));

    }

    /**
     * formulaire d'edition
     * @param $id
     * @return view
     */
    public function saveImg(Request $request, $id)
    {

        $image_id = Image::where('product_id', $id)->first();

        $package = Package::findOrFail($id);


        $destinationPath = 'uploads/img/';
        $array = array();

        if ($request->has('images')) {
            foreach ($request->file('images') as $image) {
                $original_name = $package->product . '-' . $id . '-' . $image->getClientOriginalName();
                array_push($array, $original_name);
                $image->move($destinationPath, $original_name);
            }
            if (!empty($image_id)) {
                $create_image = $image_id->update(['product_id' => $id, 'image' => implode('|', $array)]);
            } else {
                $create_image = Image::create(['product_id' => $id, 'image' => implode('|', $array)]);

            }
        }
        return redirect()->route('ThePackage.index');
    }


    /**
     * formulaire d'edition
     * @param $id
     * @return view
     */
    public function getPackage(Request $request)
    {
        //requete ajax
        if (request()->ajax()) {

            $barcode = $request->get('barcode', false);
            // $package=  Package::findOrFail($val);
            $package = Package::where('packages.bar_code', $barcode)->select(
                'packages.customer_name',
                'packages.customer_tel',
                'packages.longueur',
                'packages.largeur',
                'packages.hauteur',
                'packages.volume',
                'packages.weight',
                'packages.product',
                'packages.bar_code',
                'packages.id'
            )->first();
            return $package;
        }
    }

    /**
     * Package row
     * @param $request
     * @return view
     */
    public function getPackageRow(Request $request)
    {
        //requete ajax
        if (request()->ajax()) {

            $barcode = $request->get('barcode', false);
            // $package=  Package::findOrFail($val);
            $package = Package::where('packages.bar_code', $barcode)->select(
                'packages.customer_name',
                'packages.customer_tel',
                'packages.longueur',
                'packages.largeur',
                'packages.hauteur',
                'packages.volume',
                'packages.weight',
                'packages.product',
                'packages.bar_code',
                'packages.id'
            )->first();
            $row = '<tr data-id="' . $package['id'] . '">';
            $row .= '<td>' . $package['bar_code']. '</td>';
            $row .= '<td>' . $package['customer_name'] . '</td>';
            $row .= '<td>' . $package['customer_tel']. '</td>';
            $row .= '<td>' . $package['product']. '</td>';
            $row .= '<td>' . $package['longueur']. '</td>';
            $row .= '<td>' . $package['largeur']. '</td>';
            $row .= '<td>' . $package['hauteur']. '</td>';
            $row .= '<td>' . $package['volume']. '</td>';
            $row .= '<td>' . $package['weight']. '</td>';
            $row .= '<td><button type="button" class="btn btn-danger btn-xs remove_package_row">-</button>
                    <input type="hidden" class="package_row_index" value="' . $package['id'] . '"></td>';
            $row .= '</tr>';
            return $row;
        }
    }
}
