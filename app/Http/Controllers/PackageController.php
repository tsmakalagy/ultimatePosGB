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

class PackageController extends Controller
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

        if (request()->ajax()) {
            $with = [];
            $package = $this->transactionUtil->getPackage();

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $package->whereDate('packages.created_at', '>=', $start)
                    ->whereDate('packages.created_at', '<=', $end);
            }
            $datatable = Datatables::of($package)
                ->addColumn(
                    'action',
                    function ($row) use ($is_admin) {
                        $html = '<div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $html .=
                            '<li><a href="' . action('PackageController@show', [$row->id]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
                        $html .=
                            '<li><a target="_blank" href="' . action('PackageController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
//                        $html .=
//                            '<li><a href="' . action('PackageController@delete', [$row->id]) . '" class="delete-sell"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->removeColumn('id')
                ->editColumn('tel',
                    '<span class="tel">   </span>')
                ->editColumn('product',
                    '<span class="product_name" data-orig-value="{{$product}}">@if(!empty($product)) {{$product}} @endif   </span>')
                ->editColumn('bar_code',
                    '<span class="product_spec" data-orig-value="{{$bar_code}}">@if(!empty($bar_code)) {{$bar_code}} @endif   </span>')
                ->editColumn('customer_tel',
                    '<span class="china_price" data-orig-value="{{$customer_tel}}">@if(!empty($customer_tel)) {{$customer_tel}} @endif   </span>')
                ->editColumn('longueur',
                    '<span class="china_price" data-orig-value="{{$longueur}}">@if(!empty($longueur)) {{$longueur}} @endif   </span>')
                ->editColumn('largeur',
                    '<span class="china_price" data-orig-value="{{$largeur}}">@if(!empty($largeur)) {{$largeur}} @endif   </span>')
                ->editColumn('hauteur',
                    '<span class="china_price" data-orig-value="{{$hauteur}}">@if(!empty($hauteur)) {{$hauteur}} @endif   </span>')
                ->editColumn(
                    'volume',
                    function ($row) {
                        $la = $row->largeur;
                        $Lo = $row->longueur;
                        $h = $row->hauteur;
                        $v = $row->volume;
                        if (empty($v)) {
                            if ($la != 0 && $Lo != 0 && $h != 0) {
                                $v = $la * $Lo * $h * 0.000001;
                            }
                        }
                        return '<span class="china_price" data-orig-value="' . $v . '">' . number_format($v, 4) . '</span>';
                    }
                )
                ->editColumn('weight',
                    '<span class="size" data-orig-value="{{$weight}}">@if(!empty($weight)) {{$weight}} @endif   </span>')
                ->editColumn(
                    'created_at',
                    function ($row) {
                        $created_at = $row->created_at ? $row->created_at->format('Y-m-d') : '';
                        $data_order = $row->created_at ? $row->created_at->format('Y-m-d H:i', strtotime($row->created_at)) : '';
                        $format_date = preg_replace('/[\s]+/mu', '', $created_at);
                        return '<span data-order="' . $data_order  . '" class="total-discount" data-orig-value="">' . $created_at . '</span>';
                    }
                )
                ->editColumn('image', function ($row) {
                    $image_url = Image::where('product_id', $row->id)->where('type','package' )->first();

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
//                ->editColumn('status',
//                    '<span class="weight" data-orig-value="{{$status}}">@if(!empty($status)) {{$status}} @endif  </span>')
                ->editColumn('other_field1',
                    '<span class="other_field1" data-orig-value="{{$other_field1}}">@if(!empty($other_field1)) {{$other_field1}} @endif   </span>')
                ->editColumn('other_field2',
                    '<span class="other_field2" data-orig-value="{{$other_field2}}">@if(!empty($other_field2)) {{$other_field2}} @endif   </span>')
                ->addColumn('product', function ($row) {
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
                ->addColumn('created_at', function ($row) {
                    $total_remaining = '';
                    return $total_remaining;
                })
                ->addColumn('hauteur', function ($row) {
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
//                ->addColumn('status', function ($row) {
//                    $total_remaining = '';
//                    return $total_remaining;
//                })
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
//                ->filterColumn('status', function ($query, $keyword) {
//                    $query->where(function ($q) use ($keyword) {
//                        $q->where(DB::raw(" IF(packages.status = 0, 'entrant', 'sortant')"), 'like', "%{$keyword}%");
//                    });
//                })
                ->filterColumn('created_at', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('created_at', 'like', "%{$keyword}%");
                    });
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                            return action('PackageController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }]);

            $rawColumns = ['created_at', 'product', 'customer_tel', 'longueur', 'volume', 'largeur', 'bar_code', 'hauteur', 'weight', 'image', 'status', 'other_field1', 'other_field2', 'action', 'tel'];

            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }
        return view('packages.index');


    }

    public function indexApi()
    {
        $package = Package::all();
        return response()->json($package->toArray());
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
        $barcode = $request->get('barcode');
        if (!empty($barcode)) {
            $contact = Contact::pluck('name', 'id');
            $product_price_setting = ProductPriceSetting::first();


            return view('packages.create', compact('product_price_setting', 'barcode'));
        } else {
            return redirect()->route('Package.index');
        }
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
        $bar_code = $request->input('bar_code');
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

        $package = Package::firstOrCreate(['product' => $product, 'bar_code' => $bar_code, 'volume' => $volume, 'customer_tel' => $customer_tel, 'customer_name' => $customer_name, 'longueur' => $longueur, 'largeur' => $largeur, 'hauteur' => $hauteur, 'weight' => $weight, 'image' => $image, 'status' => $status, 'other_field1' => $other_field1, 'other_field2' => $other_field2]);

        $destinationPath = 'uploads/img/';
        $array = array();
        if ($request->has('images')) {
            foreach ($request->file('images') as $image) {
                $original_name = $package->id . '-' . $image->getClientOriginalName();
                array_push($array, $original_name);
                $image->move($destinationPath, $original_name);

            }
            $create_image = Image::create(['product_id' => $package->id,'type' =>'package', 'image' => implode('|', $array)]);

        }

        return redirect()->route('Package.index');


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

        $image_url = Image::where('product_id', $id)->where('type', 'package')->first();

        return view('packages.view-modal')->with(compact('package', 'contact', 'image_url'));
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

        return view('packages.edit', compact('package', 'product_price_setting', 'contact'));

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

        $image_id = Image::where('product_id', $id)->where('type', 'package')->first();

        $product = $request->input('product');
        $bar_code = $request->input('bar_code');
        $customer_name = $request->input('customer_name');
        $customer_tel = $request->input('customer_tel');
        $longueur = $request->input('longueur');
        $largeur = $request->input('largeur');
        $hauteur = $request->input('hauteur');
        $image = 'image';
        $status = $request->input('status');
        $weight = $request->input('weight');
        $volume = $request->input('volume');
        $other_field1 = $request->input('other_field1');
        $other_field2 = $request->input('other_field2');

        $package->update(['product' => $product, 'bar_code' => $bar_code, 'volume' => $volume, 'customer_name' => $customer_name, 'customer_tel' => $customer_tel, 'longueur' => $longueur, 'largeur' => $largeur, 'hauteur' => $hauteur, 'weight' => $weight, 'image' => $image, 'status' => $status, 'other_field1' => $other_field1, 'other_field2' => $other_field2]);
        $destinationPath = 'uploads/img/';
        $array = array();
        if ($request->has('images')) {
            foreach ($request->file('images') as $image) {
                $original_name = md5(microtime()) . '_' . $package->id . '.' . $image->getClientOriginalExtension();
                array_push($array, $original_name);
                $image->move($destinationPath, $original_name);
            }
            if (!empty($image_id)) {
                $create_image = $image_id->update(['product_id' => $package->id,'type' => 'package', 'image' => implode('|', $array)]);
            } else {
                $create_image = Image::create(['product_id' => $package->id ,'type' => 'package', 'image' => implode('|', $array)]);

            }
        }


        return redirect()->route('Package.index');
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
        return redirect()->route('Package.index');

    }

    /**
     * formulaire d'edition
     * @param $id
     * @return view
     */
    public function scan()
    {

        return view('packages.scan-modal');

    }

    /**
     * formulaire d'edition
     * @param $id
     * @return view
     */
    public function uploadImg($id)
    {

        return view('packages.uploadImg-modal', compact('id'));

    }

    /**
     * formulaire d'edition
     * @param $id
     * @return view
     */
    public function saveImg(Request $request, $id)
    {

        $image_id = Image::where('product_id', $id)->where('type', 'package')->first();

        $package = Package::findOrFail($id);


        $destinationPath = 'uploads/img/';
        $array = array();

        if ($request->has('images')) {
            foreach ($request->file('images') as $image) {
                $original_name = md5(microtime()) . '_' . $id . '-' . $image->getClientOriginalName();
                array_push($array, $original_name);
                $image->move($destinationPath, $original_name);
            }
            if (!empty($image_id)) {
                $create_image = $image_id->update(['product_id' => $id,'type' => 'package', 'image' => implode('|', $array)]);
            } else {
                $create_image = Image::create(['product_id' => $id, 'type' => 'package', 'image' => implode('|', $array)]);

            }
        }
        return redirect()->route('Package.index');
    }

}
