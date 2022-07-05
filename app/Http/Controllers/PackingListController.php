<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PriceProduct;
use App\ProductPriceSetting;
use App\ProductPrice;
use App\Image;
use App\Exports\PackinglistsExport;
use Maatwebsite\Excel\Facades\Excel;

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
use App\Package;
use App\PackingListLine;
use App\ThePackage;
use App\PackingList;
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




use App\Exports\CustomPurchaseExport;

use App\Variation;

use App\PurchaseLine;
;
use App\AccountTransaction;

use App\Exports\PurchaseExport;

class packingListController extends Controller
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

            $package = $this->transactionUtil->getPackage();
            $the_package = $this->transactionUtil->getThePackage();
            $packing_list = $this->transactionUtil->getPackingList();


            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }


            $only_shipments = request()->only_shipments == 'true' ? true : false;
            if ($only_shipments) {
                $sells->whereNotNull('transactions.shipping_status');

                if (auth()->user()->hasAnyPermission(['access_pending_shipments_only'])) {
                    $sells->where('transactions.shipping_status', '!=', 'delivered');
                }
            }


            $datatable = Datatables::of($packing_list)
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
                                '<li><a href="' . action('packingListController@show', [$row->id]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
                        }
                       

                        if (auth()->user()->can('product.update')) {
                            $html .=
                                '<li><a target="_blank" href="' . action('packingListController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        if (auth()->user()->can('sell.delete')) {
                            $html .=
                                '<li><a href="' . action('packingListController@delete', [$row->id]) . '" class="delete-sell"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }
                        // $html .=
                        // '<li><a href="#"  data-href="' . route('packingList.printInvoice', [$row->id]) . '"><i class="fa fa-print"></i> '. __("messages.print") .'</a></li>';
                       
                        $html .= '<li><a href="' . action('packingListController@exportToExcel', [$row->id]) . '" class="export-purchase"><i class="fas fa-trash"></i>' . __("export_to excel") . '</a></li>';

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
                ->editColumn('mode_transport',
                    '<span class="size" data-orig-value="{{$mode_transport}}">@if(!empty($mode_transport)) {{$mode_transport}} @endif   </span>')
                ->editColumn('the_package',
                    function ($row) {
                        $arr = array();
                        $arr2 = array();
                        $pack_listlines = $row->packinglist_lines;
                        $val = '';

                        foreach ($pack_listlines as $packinglistlines) {
                            // return($packinglistlines);
                            $sku = $packinglistlines->thepackage->sku;
                            $qte = $packinglistlines->qte;
                            $product = $packinglistlines->thepackage->product;
                            $packing = $packinglistlines->thepackage->thepackage_package;
                            $val .= '<span class="date_paid_on">' . $sku . '(' . $qte . ')' . '</span><br>';
                            //  return $product.'('.$sku.')';
                            // return $packing;
                            // foreach($packing as $pack){
                            //      $prod=$pack->product;
                            //      array_push($arr,$prod);
                            //     //  return $packing;
                            // }
                            // $ar=implode(',', $arr);
                            // $new_arr=$sku.$product.$ar;
                            // array_push($arr2,$new_arr);
                            // $val=$sku.'('.$qte.')';
                            // array_push($arr,$val);
                            // return $packinglistlines->thepackage->product.','.$product.'('.$sku.')';
                        }


                        return $val;
                    })
                // '<span class="size" data-orig-value="">@if(!empty($the_package)) {{$the_package}} @endif   </span>')

                ->editColumn(
                    'date_envoi',
                    function ($row) {
                        //     $created_at = $row->date_envoi ? $row->date_envoi->format('Y-m-d') : '';
                        //     $data_order = $row->date_envoi ? $row->date_envoi->format('Y-m-d H:i', strtotime($row->date_envoi)) : '';
                        //     $format_date = preg_replace('/[\s]+/mu', '', $created_at);
                        return '<span data-order="" class="total-discount" data-orig-value="">' . $row->date_envoi . '</span>';
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
                        return '<button type="button" class="btn btn-block btn-xs btn-primary btn-modal" data-href="' . action('ThePackageController@uploadImg', [$row->id]) . '"  data-container=".uploadImg_modal">upload img</button>';

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
                ->addColumn('date_envoi', function ($row) {
                    $total_remaining = '';

                    return $total_remaining;
                })
                ->addColumn('mode_transport', function ($row) {
                    $total_remaining = '';

                    return $total_remaining;
                })
                ->addColumn('customer_tel', function ($row) {
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
                ->addColumn('longueur', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('the_package', function ($row) {
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
                            return action('packingListController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }]);

            $rawColumns = ['final_total', 'mode_transport', 'the_package', 'customer_tel', 'date_envoi', 'longueur', 'largeur', 'hauteur', 'weight', 'image', 'status', 'other_field1', 'other_field2', 'action', 'type', 'other_details', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status', 'types_of_service_name', 'payment_methods', 'return_due', 'conatct_name'];

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

        return view('packing_list.index')
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
        $carbon = \Carbon::now()->format('m/d/Y H:i:s');
        $now = \Carbon::today()->format('Y-m-d');
        // dd($now);
        $package = Package::pluck('bar_code', 'id');

        return view('packing_list.create_tmp', compact('package', 'now', 'carbon'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

// ->format('m/d/Y H:i:s')
        $date_env = $request->input('date_envoi');
        $date_envoi = date('Y-m-d H:i:s', strtotime($date_env));

        $mode_transport = $request->input('mode_transport');

        $package = PackingList::create(['date_envoi' => $date_envoi, 'mode_transport' => $mode_transport]);


        $packages = $request->input('packages');
        $arr = array();
        if ($request->has('packages')) {
            foreach ($packages as $packet) {

                $pl = PackingListLine::create(['packing_list_id' => $package->id, 'the_package_id' => $packet['id'], 'qte' => $packet['qte']]);

            }
        }
        // $created_at = $package->created_at->format('Y-m-d H:i');
        // $id = str_pad($package->id, 4, '0', STR_PAD_LEFT);
        // $barcode = 'pack-' . $id . '-' . $created_at;
        // $package->update(['bar_code' => $barcode]);

        return redirect()->route('packingList.index');


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

        $package = PackingList::findOrFail($id);
        $contact = Contact::pluck('name', 'id');
        // $other_product = $package->thepackage_package->implode('product', ',');

        return view('packing_list.view-modal')->with(compact('package'));
    }

    /**
     * formulaire d'edition
     * @param $id
     * @return view
     */
    public function edit($id)
    {

        $package = PackingList::findOrFail($id);
        // dd($package);
        // $id_tp=$package->packinglist_lines;
        // // dd($id_tp);
        // $arr=array();
        // foreach($id_tp as $id_tps){
        //     $result=$id_tps->thepackage->id;
        //     array_push($arr,$result);
        // }
        // // dd($arr);
        // $impl=implode(',', $arr);
        // // dd($impl);
        // $the_package = ThePackage::findOrFail($id);


        $date = date('m/d/Y H:i:s', strtotime($package->date_envoi));

        // $date_envoi = $package->date_envoi->format('m/d/Y H:i:s');

        return view('packing_list.edit', compact( 'package','date'));

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
        $package = PackingList::findOrFail($id);
        $packinglistline = PackingListLine::where('packing_list_lines.packing_list_id', $id);
        // dd($packinglistline);
        $packinglistline->delete();

        $date_env = $request->input('date_envoi');
        $date_envoi = date('Y-m-d H:i:s', strtotime($date_env));

        $mode_transport = $request->input('mode_transport');

        $package->update(['date_envoi' => $date_envoi, 'mode_transport' => $mode_transport]);

        $packages = $request->input('packages');
        $arr = array();
        if ($request->has('packages')) {
            foreach ($packages as $packet) {

                $pl = PackingListLine::create(['packing_list_id' => $package->id, 'the_package_id' => $packet['id'], 'qte' => $packet['qte']]);

            }
            //     foreach($packinglistline as $packinglistlines){
            //         $packinglistlines->update
            // //    foreach ($packages as $packet) {

            // //         $pl= $packinglistlines->update(['packing_list_id' => $package->id,'the_package_id' => $packet['id'],'qte' => $packet['qte'], ]);
            // //    }
            //    }
        }


        return redirect()->route('packingList.index');
    }

    /**
     * gestion de supprimation
     *
     * @return redirect
     */
    public function delete($id)
    {

        $package = ThePackage::findOrFail($id);
        $package->delete();
        return redirect()->route('packingList.index');

    }

    /**
     * formulaire d'edition
     * @param $id
     * @return view
     */
    public function scan()
    {

        $the_package = ThePackage::pluck('bar_code', 'id');
        return view('packing_list.scan-modal', compact('the_package'));

    }

    /**
     * formulaire d'edition
     * @param $id
     * @return view
     */
    public function uploadImg($id)
    {

        return view('packing_list.uploadImg-modal', compact('id'));

    }

    /**
     * formulaire d'edition
     * @param $id
     * @return view
     */
    public function saveImg(Request $request, $id)
    {

        $image_id = Image::where('product_id', $id)->first();

        $package = ThePackage::findOrFail($id);


        $destinationPath = 'uploads/img/';
        $array = array();

        if ($request->has('images')) {
            foreach ($request->file('images') as $image) {
                $original_name = 'the_packages' . $package->id . '-' . $image->getClientOriginalName();
                // $original_name = $package->product . '-' . $id . '-' . $image->getClientOriginalName();
                array_push($array, $original_name);
                $image->move($destinationPath, $original_name);
            }
            if (!empty($image_id)) {
                $create_image = $image_id->update(['product_id' => $id, 'image' => implode('|', $array)]);
            } else {
                $create_image = Image::create(['product_id' => $id, 'image' => implode('|', $array)]);

            }
        }
        return redirect()->route('packingList.index');
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

            $id = $request->get('id', false);
            // $package=  Package::findOrFail($val);
            $package = ThePackage::where('the_packages.id', $id)->select(

                'the_packages.longueur',
                'the_packages.largeur',
                'the_packages.hauteur',
                'the_packages.volume',
                'the_packages.weight',

                'the_packages.bar_code',
                'the_packages.id'
            )->first();
            $row = '<tr data-id="' . $package['id'] . '">';
            $row .= '<td>' . $package['bar_code'] . '</td>';

            $row .= '<td>' . $package['longueur'] . '</td>';
            $row .= '<td>' . $package['largeur'] . '</td>';
            $row .= '<td>' . $package['hauteur'] . '</td>';
            $row .= '<td>' . $package['volume'] . '</td>';
            $row .= '<td>' . $package['weight'] . '</td>';
            $row .= '<td ><input type="text" name="packages[' . $package['id'] . '][qte]" required style="width:50px;" /> </td>';
            $row .= '<td><button type="button" class="btn btn-danger btn-xs move_packages_row">-</button>
                    <input type="hidden" name="packages[' . $package['id'] . '][id]" class="package_row_index" value="' . $package['id'] . '"></td>';
            $row .= '</tr>';
            return $row;
        }
    }

    /**
     * ThePackage row
     * @param $request
     * @return view
     */
    public function getThePackageRow(Request $request)
    {
        //requete ajax
        if (request()->ajax()) {

            $value = $request->get('val', false);

            $packingLines = ThePackage::query()
                ->where('product', 'LIKE', '%' . $value . '%')
                ->orWhere('sku', 'LIKE', '%' . $value . '%')
                ->orWhereHas('thepackage_package', function ($q) use ($value) {
                    $q->where('bar_code', 'LIKE', '%' . $value . '%')
                        ->orWhere('product', 'LIKE', '%' . $value . '%');
                })->get();
            if (!empty($packingLines)) {
                $row = '<ul class="list">';
                foreach ($packingLines as $packingLine) {
                    var_dump($packingLine->thepackage_package[0]->bar_code);
                    $row .= '<button type="button"  class="btn btn-default btn-xs remove_package_row"><li class="move_package_row" style="list-style-type: none;" data-id="' . $packingLine->id . '">' . $packingLine->product . ' (' . $packingLine->sku . ')' . '<input type="hidden" name="my_input"  value="' . $packingLine->id . '"/></li></button><br>';


                }
                $row .= '</ul>';


                return $row;

            }


        }
    }

    /**
     * List of ThePackage
     * @return JSON
     */
    public function listThePackage()
    {
        //requete ajax
        if (request()->ajax()) {

            $search_term = request()->input('term', '');

            $packingLines = ThePackage::query()
                ->where('product', 'LIKE', '%' . $search_term . '%')
                ->orWhere('sku', 'LIKE', '%' . $search_term . '%')
                ->orWhereHas('thepackage_package', function ($q) use ($search_term) {
                    $q->where('bar_code', 'LIKE', '%' . $search_term . '%')
                        ->orWhere('product', 'LIKE', '%' . $search_term . '%');
                })->get();
            $array_of_box = array();
            if (!empty($packingLines)) {
                foreach ($packingLines as $packingLine) {
                    $product = $packingLine->product;
                    $sku = $packingLine->sku;
                    $packages = $packingLine->thepackage_package;

                    $length = number_format($packingLine->longueur);
                    $width = number_format($packingLine->largeur);
                    $height = number_format($packingLine->hauteur);
                    $dimension = "";
                    if ($length > 0 && $width > 0 && $height > 0) {
                        $dimension = '(' . $length . 'x' . $width . 'x' . $height . 'cm)';
                    }
                    $array_of_package = array();

                    $displayLine = $sku . ' - ' . $dimension;
                    if (!empty($product)) {
                        $displayLine .= ' - ' . $product;
                    }
                    if (!empty($packages)) {
                        foreach ($packages as $package) {
                            $p_product = $package->product;
                            $p_barcode = $package->bar_code;
                            $term_in_p_product = strpos($p_product, $search_term);
                            $term_in_p_barcode = strpos($p_barcode, $search_term);
                            $displayLine .= ' - ' . $p_product . '(' . $package->customer_name . ')';
                            array_push($array_of_package, array(
                                'p' => $p_product,
                                'bc' => $p_barcode,
                                'c_name' => $package->customer_name,
                                'c_contact' => $package->customer_tel
                            ));
                        }
                    }

                    array_push($array_of_box, array(
                        'id' => $packingLine->id,
                        'product' => $product,
                        'sku' => $sku,
                        'dimension' => $dimension,
                        'displayLine' => $displayLine,
                        'packages' => $array_of_package
                    ));
                }
            }
            return json_encode($array_of_box);
        }
    }

    /**
     * Checks if ref_number and supplier combination already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printInvoice($id)
    {
        try {
            $package = PackingList::findOrFail($id);

            $output = ['success' => 1, 'receipt' => []];
            $output['receipt']['html_content'] = view('packing_list.print',compact('package'))->render();
         
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }

        return $output;
    }

         /**
     * Export to Excel.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function exportToExcel($id)
    { 
        //return Excel::download(new PurchaseExport, 'purchases.xlsx');
        return (new PackinglistsExport($id))->download('packing_lists.xlsx');
    }

}
