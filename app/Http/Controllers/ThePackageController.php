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

            $package = $this->transactionUtil->getPackage();
            $the_package = $this->transactionUtil->getThePackage();


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
                                '<li><a href="' . action('ThePackageController@show', [$row->id]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
                        }


                        if (auth()->user()->can('product.update')) {
                            $html .=
                                '<li><a target="_blank" href="' . action('ThePackageController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        if (auth()->user()->can('sell.delete')) {
                            $html .=
                                '<li><a href="' . action('ThePackageController@delete', [$row->id]) . '" class="delete-sell"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
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
                    // '<span class="product_name" data-orig-value="{{$product}}">@if(!empty($product)) {{$product}} @endif   </span>')
                    function ($row) {
                        $span = '<span class="tel"> ' . $row->thepackage_package->implode('product', ',') . '</span><br><span class="tel"> ' . $row->product . '</span><br>';
                        return $span;
                    })
              
                ->editColumn('longueur',
                    '<span class="china_price" data-orig-value="{{$longueur}}">@if(!empty($longueur)) {{$longueur}} @endif   </span>')
                ->editColumn('largeur',
                    '<span class="china_price" data-orig-value="{{$largeur}}">@if(!empty($largeur)) {{$largeur}} @endif   </span>')
                ->editColumn('hauteur',
                    '<span class="china_price" data-orig-value="{{$hauteur}}">@if(!empty($hauteur)) {{$hauteur}} @endif   </span>')
                ->editColumn('weight',
                    '<span class="size" data-orig-value="{{$weight}}">@if(!empty($weight)) {{$weight}} @endif   </span>')
                ->editColumn('sku',
                    '<span class="size" data-orig-value="{{$sku}}">@if(!empty($sku)) {{$sku}} @endif   </span>')
 
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
                ->addColumn('sku', function ($row) {
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
                            return action('ThePackageController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }]);

            $rawColumns = ['final_total', 'product', 'sku','longueur', 'largeur',  'hauteur', 'weight', 'image', 'status', 'other_field1', 'other_field2', 'action', 'type', 'other_details', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status', 'types_of_service_name', 'payment_methods', 'return_due', 'conatct_name'];

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


        $package = ThePackage::firstOrCreate(['product' => $product, 'bar_code' => $bar_code, 'longueur' => $longueur, 'largeur' => $largeur, 'hauteur' => $hauteur, 'weight' => $weight, 'image' => $image, 'volume' => $volume, 'status' => $status, 'other_field1' => $other_field1, 'other_field2' => $other_field2]);


        $destinationPath = 'uploads/img/';
        $array = array();
        if ($request->has('images')) {
            foreach ($request->file('images') as $image) {
                $original_name = 'the_packages' . $package->id . '-' . $image->getClientOriginalName();
                // $original_name = $package->product . '-' . $package->id . '-' . $image->getClientOriginalName();
                array_push($array, $original_name);
                $image->move($destinationPath, $original_name);

            }
            $create_image = Image::create(['product_id' => $package->id, 'image' => implode('|', $array)]);

        }
        $packages = $request->input('packages');
        if (!empty($packages)) {
            $package->thepackage_package()->sync($packages);
        }
        $created_at = $package->created_at->format('ymd');
        $id = str_pad($package->id, 3, '0', STR_PAD_LEFT);
        $sku =  $created_at.$id ;
        $package->update(['sku' => $sku]);

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

        $package = ThePackage::where('the_packages.id', $id)->first();
        $contact = Contact::pluck('name', 'id');
        $other_product = $package->thepackage_package->implode('product', ',');
        // dd($other_product);
        // $span='<span class="tel"> '. $row->thepackage_package->implode('product', ',').'</span><br><span class="tel"> '. $row->product.'</span><br>';

        $image_url = Image::where('product_id', $id)->first();

        return view('the_package.view-modal')->with(compact('package', 'other_product', 'contact', 'image_url'));
    }

    /**
     * formulaire d'edition
     * @param $id
     * @return view
     */
    public function edit($id)
    {

        $package = Package::findOrFail($id);
        $the_package = ThePackage::findOrFail($id);
        $contact = Contact::pluck('name', 'id');

        return view('the_package.edit', compact('the_package', 'contact'));

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

        $package = ThePackage::findOrFail($id);

        $image_id = Image::where('product_id', $id)->first();

        $product = $request->input('product');
        $bar_code = $request->input('bar_code');;

        $longueur = $request->input('longueur');
        $largeur = $request->input('largeur');
        $hauteur = $request->input('heuteur');
        $image = 'image';
        $status = $request->input('status');
        $weight = $request->input('weight');
        $other_field1 = $request->input('other_field1');
        $other_field2 = $request->input('other_field2');

        $package->update(['product' => $product, 'bar_code' => $bar_code, 'longueur' => $longueur, 'largeur' => $largeur, 'hauteur' => $hauteur, 'weight' => $weight, 'image' => $image, 'status' => $status, 'other_field1' => $other_field1, 'other_field2' => $other_field2]);
        $destinationPath = 'uploads/img/';
        $array = array();
        if ($request->has('images')) {
            foreach ($request->file('images') as $image) {
                $original_name = 'the_package' . md5(microtime()) . '_' . $package->id . '.' . $image->getClientOriginalExtension();
                // $original_name = $package->product . '-' . $package->id . '-' . $image->getClientOriginalName();
                array_push($array, $original_name);
                $image->move($destinationPath, $original_name);
            }
            if (!empty($image_id)) {
                $create_image = $image_id->update(['product_id' => $package->id, 'image' => implode('|', $array)]);
            } else {
                $create_image = Image::create(['product_id' => $package->id, 'image' => implode('|', $array)]);

            }
        }

        $packages = !empty($request->input('packages')) ?
            $request->input('packages') : [];
        $package->thepackage_package()->sync($packages);

        return redirect()->route('ThePackage.index');
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
            $row .= '<td>' . $package['bar_code'] . '</td>';
            $row .= '<td>' . $package['customer_name'] . '</td>';
            $row .= '<td>' . $package['customer_tel'] . '</td>';
            $row .= '<td>' . $package['product'] . '</td>';
            $row .= '<td>' . $package['longueur'] . '</td>';
            $row .= '<td>' . $package['largeur'] . '</td>';
            $row .= '<td>' . $package['hauteur'] . '</td>';
            $row .= '<td>' . $package['volume'] . '</td>';
            $row .= '<td>' . $package['weight'] . '</td>';
            $row .= '<td><button type="button" class="btn btn-danger btn-xs remove_package_row">-</button>
                    <input type="hidden" name="packages[]" class="package_row_index" value="' . $package['id'] . '"></td>';
            $row .= '</tr>';
            return $row;
        }
    }
}
