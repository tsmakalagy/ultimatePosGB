<?php

namespace App\Http\Controllers;

use App\Account;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\Shipper;
use App\Address;

use App\PackageTransactionPayment;
use App\PackageTransaction;
use App\PackageTransactionLine;
use App\CustomerGroup;
use App\InvoiceScheme;
use App\SellingPriceGroup;
use App\TaxRate;
use App\Transaction;
use App\ThePackage;
use App\Package;
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
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Product;
use App\Media;
use Spatie\Activitylog\Models\Activity;

class SellTransactionController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
            $payment_types = $this->transactionUtil->payment_types(null, true, null);
            $with = [];
            $shipping_statuses = $this->transactionUtil->shipping_statuses();

            $sale_type = !empty(request()->input('sale_type')) ? request()->input('sale_type') : 'sell';

     
            $id=request()->session()->get('user.id');
           
             $use=User::where('id',$id)->first();

            //if commission_agent connected
            if($use->is_cmmsn_agnt ==1){
            $sells = $this->transactionUtil->getListSellsCmmsnAgnt($business_id, $sale_type);
            }
            else{
            $sells = $this->transactionUtil->getListSells($business_id, $sale_type);
            }
            $package_transaction = $this->transactionUtil->getListPackageTransaction();

            
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
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


            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $package_transaction->where('contacts.id', $customer_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $package_transaction->whereDate('package_transactions.transaction_date', '>=', $start)
                    ->whereDate('package_transactions.transaction_date', '<=', $end);
            }
       


            //Add condition for commission_agent,used in sales representative sales with commission report
            if (request()->has('commission_agent')) {
                $commission_agent = request()->get('commission_agent');
                if (!empty($commission_agent)) {
                    $sells->where('transactions.commission_agent', $commission_agent);
                }
            }



    

            //$business_details = $this->businessUtil->getDetails($business_id);
            if ($this->businessUtil->isModuleEnabled('subscription')) {
                $sells->addSelect('transactions.is_recurring', 'transactions.recur_parent_id');
            }
            $sales_order_statuses = Transaction::sales_order_statuses();
            $datatable = Datatables::of($package_transaction)
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
                        $html .= '<li><a href="#" data-href="' . action("SellTransactionController@show", [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                    }

                   

                    if (auth()->user()->can('product.update')) {
                        $html .=
                            '<li><a target="_blank" href="' . action('SellTransactionController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                    }
                    if (auth()->user()->can('sell.delete')) {
                        $html .=
                            '<li><a href="' . action('SellTransactionController@delete', [$row->id]) . '" class="delete-sell"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                    }
                    // $html .=
                    // '<li><a href="#"  data-href="' . route('packingList.printInvoice', [$row->id]) . '"><i class="fa fa-print"></i> '. __("messages.print") .'</a></li>';
                   
                    // $html .= '<li><a href="' . action('packingListController@exportToExcel', [$row->id]) . '" class="export-purchase"><i class="fas fa-trash"></i>' . __("messages.export_to excel") . '</a></li>';

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
            ->editColumn('customer', function($row) {
                if ($row->customer_id !== null || !empty($row->customer_id)) {
                    $customer = Contact::select('name', 'mobile')->where('id', $row->customer_id)->first();
                    $name_and_mobile = $customer->name . ' (' . $customer->mobile . ')';
                    return $name_and_mobile;
                }
                return '';
            })
            ->editColumn(
                'final_total',
                '<span class="final-total" data-orig-value="{{$final_total}}">@format_currency($final_total)</span>'
            )
            ->editColumn(
                'total_paid',
                '<span class="total-paid" data-orig-value="{{$total_paid}}">@format_currency($total_paid)</span>'
            )
             ->editColumn(
                'commission_agent',
                '<span class="china_price" data-orig-value="{{$added_by}}">@if(!empty($added_by)) {{$added_by}} @endif   </span>'
            )
            ->editColumn(
                'payment_status',
                function ($row) {
                    $payment_status = PackageTransaction::getPaymentStatus($row);
                    return (string)view('sell_transaction.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id]);
                }
            )
                       ->editColumn(
                'ref_no',
                '<span class="china_price" data-orig-value="{{$ref_no}}">@if(!empty($ref_no)) {{$ref_no}} @endif   </span>'
            )
                            ->editColumn(
                'invoice_no',
                '<span class="china_price" data-orig-value="{{$invoice_no}}">@if(!empty($invoice_no)) {{$invoice_no}} @endif   </span>'
            )
                            ->editColumn(
                'return_due',
                '<span class="china_price" data-orig-value="{{$return_due}}">@if(!empty($return_due)) {{$return_due}} @endif   </span>'
            )
            ->editColumn(
                'transaction_date',
                '<span class="china_price" data-orig-value="{{$transaction_date}}">@if(!empty($transaction_date)) {{$transaction_date}} @endif   </span>'
            )
   
         
            ->editColumn(
                'discount_amount',
                function ($row) {
                   $discount ='<span class="china_price" data-orig-value="{{$discount_amount}}">@if(!empty($discount_amount)) {{$discount_amount}} @endif   </span>';

                    return $discount;
                }
            )
         
               
            ->addColumn('total_remaining', function ($row) {
                $total_remaining = $row->final_total - $row->total_paid;
                $total_remaining_html = '<span class="payment_due" data-orig-value="' . $total_remaining . '">' . $this->transactionUtil->num_f($total_remaining, true) . '</span>';


                return $total_remaining_html;
            })
             ->addColumn('sell_due', function ($row) {
                $total_remaining = '';

                return $total_remaining;
            })
              ->addColumn('customer', function ($row) {
                $total_remaining = '';

                return $total_remaining;
            })
            ->addColumn('payment_methods', function ($row) use ($payment_types) {
                $methods = array_unique($row->payment_lines->pluck('method')->toArray());
                $count = count($methods);
                $payment_method = '';
                if ($count == 1) {
                    $payment_method = $payment_types[$methods[0]];
                } elseif ($count > 1) {
                    $payment_method = __('lang_v1.checkout_multi_pay');
                }

                $html = !empty($payment_method) ? '<span class="payment-method" data-orig-value="' . $payment_method . '" data-status-name="' . $payment_method . '">' . $payment_method . '</span>' : '';

                return $html;
            })
            ->addColumn('transaction_date', function ($row) {
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
                 ->addColumn('package', function ($row) {
                $total_remaining = '';
                return $total_remaining;
            })

                 ->addColumn('commission_agent', function ($row) {
                $total_remaining = '';
                return $total_remaining;
            })
                 ->addColumn('total_paid', function ($row) {
                $total_remaining = '';
                return $total_remaining;
            })
                   ->addColumn('payment_status', function ($row) {
                $total_remaining = '';
                return $total_remaining;
            })
                   ->addColumn('invoice_no', function ($row) {
                $total_remaining = '';
                return $total_remaining;
            })
                   ->addColumn('ref_no', function ($row) {
                $total_remaining = '';
                return $total_remaining;
            })
                      ->addColumn('discount_amount', function ($row) {
                $total_remaining = '';
                return $total_remaining;
            })
                      ->addColumn('final_total', function ($row) {
                $total_remaining = '';
                return $total_remaining;
            })
            ->addColumn('return_due', function ($row) {
                $total_remaining = '';
                return $total_remaining;
            })
        
            ->addColumn('conatct_name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif ')
     
          
            // ->filterColumn('status', function ($query, $keyword) {
            //     $query->where(function ($q) use ($keyword) {
            //         $q->where(DB::raw(" IF(packages.status = 0, 'entrant', 'sortant')"), 'like', "%{$keyword}%");
            //     });
            // })
   
            ->setRowAttr([
                'data-href' => function ($row) {
                    if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                        return action('SellTransactionController@show', [$row->id]);
                    } else {
                        return '';
                    }
                }]);

        $rawColumns = ['final_total','sell_due', 'customer', 'transaction_date','total_remaining',  'status',  'action', 'total_paid',  'payment_status', 'invoice_no', 'commission_agent','discount_amount',  'payment_methods', 'return_due', 'conatct_name'];

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

        return view('sell_transaction.index')
            ->with(compact('business_locations', 'customers', 'is_woocommerce', 'sales_representative', 'is_cmsn_agent_enabled', 'commission_agents', 'service_staffs', 'is_tables_enabled', 'is_service_staff_enabled', 'is_types_service_enabled', 'shipping_statuses'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // $sell_lines=TransactionSellLine::All();
        // dd($sell_lines);
        $sale_type = request()->get('sale_type', '');

        if ($sale_type == 'sales_order') {
            if (!auth()->user()->can('so.create')) {
                abort(403, 'Unauthorized action.');
            }
        } else {
            if (!auth()->user()->can('direct_sell.access')) {
                abort(403, 'Unauthorized action.');
            }
        }


        $business_id = request()->session()->get('user.business_id');

        //requete ajax
        // if (request()->ajax()) {
        //     //$centre_villes= CentreVille::pluck('commune', 'id');
        //     $selectedbrand= $request->get('selectedbrand',false);
        //     if($selectedbrand==1){
        //         $addresses= Address::where('id_indication',1)->select(['id','nom'])->get();
        //         return $addresses;
        //     }  
        //     if($selectedbrand==2){
        //         $addresses= Address::where('id_indication',2)->select(['id','nom'])->get();
        //         return $addresses;
        //     }   
        //     else{
        //         return '';
        //     }
        // }



        $business_details = $this->businessUtil->getDetails($business_id);


        $business_locations = BusinessLocation::forDropdown($business_id, false, true);

        $business_locations = $business_locations['locations'];

        $default_location = null;
        foreach ($business_locations as $id => $name) {
            $default_location = BusinessLocation::findOrFail($id);
            break;
        }

        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;

            $commission_agent = User::saleCommissionAgentsDropdown($business_id);

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }

        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);


        $default_datetime = $this->businessUtil->format_date('now', true);

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        $status = request()->get('status', '');

        $statuses = Transaction::sell_statuses();

        if ($sale_type == 'sales_order') {
            $status = 'ordered';
        }
        
        $id=request()->session()->get('user.id');
        $use=User::where('id',$id)->first();
        $customer_groups = CustomerGroup::forDropdown($business_id);


        return view('sell_transaction.create')
            ->with(compact(
          
                'business_locations',
       
                'default_location',
                'commission_agent',
                'types',
                
                'payment_line',
                'payment_types',
            
                'default_datetime',
                'pos_settings',
                'customer_groups',

           
                'status',
                'sale_type',
                'statuses',
              
                'use'
            ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            // dd($request);
        try{
        $trans_date = $request->input('transaction_date');
        $trans_date = date('Y-m-d H:i:s', strtotime($trans_date));

        $commission_agent = $request->input('commission_agent');
        $status = $request->input('status');
        $final_total = $request->input('final_total');
        $customer=$request->input('customer');
        //  dd($request);

    
      
        if(!empty($request->input('packages'))){
        $package = PackageTransaction::create(['transaction_date' => $trans_date,'final_total' => $final_total,'status' => $status, 'created_by' => $commission_agent,'customer_id' => $customer]);
        // dd($package);
        $this->transactionUtil->createOrUpdatePaymentLines2($package, $request->input('payment'));
      //Update payment status
    //   $payment_status = $this->transactionUtil->updatePaymentStatus($package->id, $package->final_total);

    //   $package->payment_status = $payment_status;

        $packages = $request->input('packages');
        $arr = array();
        $somme_total=0;
        if ($request->has('packages')) {
            foreach ($packages as $packet) {
                $total=$packet['price'];
                $somme_total+=$total;
                $pl = PackageTransactionLine::create(['package_transaction_id' => $package->id, 'package_id' => $packet['id'],  'price' => $total]);

            }
        }
       
        $invoice_no=str_pad($package->id, 4, '0', STR_PAD_LEFT);
         $package->update(['invoice_no' => $invoice_no]);
        $output = ['success' => 1, 'msg' => trans("added_successfully")];
        // return redirect()->route('Sell_transaction.index');

    
    } else {
        $output = ['success' => 0,
                    'msg' => trans("messages.something_went_wrong")
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $msg = trans("messages.something_went_wrong");
                
            // if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
            //     $msg = $e->getMessage();
            // }
            // if (get_class($e) == \App\Exceptions\AdvanceBalanceNotAvailable::class) {
            //     $msg = $e->getMessage();
            // }

            $output = ['success' => 0,
                            'msg' => $msg
                        ];
        }
        return redirect('sell-transaction')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $package = PackageTransaction::findOrFail($id);
        $customer = Contact::select('name', 'mobile')->where('id', $package->customer_id)->first();
        $name_and_mobile = $customer->name . ' (' . $customer->mobile . ')';
        // dd($name_and_mobile);
            // dd($package);
            $commission_agent=$package->leftJoin('users as u', 'package_transactions.created_by', '=', 'u.id')
            ->select(
                DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")

            )->first();
            $added_by=$commission_agent->added_by;
         $packages = PackageTransaction::join(
            'package_transaction_lines as ptl',
            'ptl.package_transaction_id',
            '=',
            'package_transactions.id'
        )
            ->join(
                'packages as p',
                'ptl.package_id',
                '=',
                'p.id'
            )
            ->select(
                'package_transactions.id',
                // 'package_transactions.the_package_id',
                'package_transactions.status',
                // 'the_packages.client',
                'package_transactions.ref_no',
                'package_transactions.invoice_no',
                // 'package_transactions.transaction_date',
                'package_transactions.payment_status',
                'package_transactions.discount_amount',
                'package_transactions.final_total',
                'ptl.price',
                'ptl.qte',
                'p.bar_code',
                'p.product',
                'p.customer_name',
                'p.customer_tel')
                ->where('package_transactions.id', $id);
                $activities = Activity::forSubject($package)
                ->with(['causer', 'subject'])
                ->latest()
                ->get();
                // dd($activities);
        // $payment_types = $this->transactionUtil->payment_types($sell->location_id, true);

        return view('sell_transaction.view-modal')
        ->with(compact(
            'name_and_mobile',
            'package',
            'activities',
            'added_by'
        ));


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,Request $request)
    {

        $package = PackageTransaction::findOrFail($id);

          $id_tp=$package->package_transaction_line;
        $arr=array();
        foreach($id_tp as $id_tps){
            $result=$id_tps->package->id;
            array_push($arr,$result);
        }
        // dd($arr);
        $impl=implode(',', $arr);
        // dd($impl);
        $sale_type = request()->get('sale_type', '');

        if ($sale_type == 'sales_order') {
            if (!auth()->user()->can('so.create')) {
                abort(403, 'Unauthorized action.');
            }
        } else {
            if (!auth()->user()->can('direct_sell.access')) {
                abort(403, 'Unauthorized action.');
            }
        }

        $business_id = request()->session()->get('user.business_id');
        

        $business_details = $this->businessUtil->getDetails($business_id);


        $business_locations = BusinessLocation::forDropdown($business_id, false, true);

        $business_locations = $business_locations['locations'];

          //payment
          $payment_types = $this->transactionUtil->payment_types($package->location_id, false, $business_id);

          $payment_lines = $this->transactionUtil->getPaymentDetails2($id);
          //If no payment lines found then add dummy payment line.
          if (empty($payment_lines)) {
              $payment_lines[] = $this->dummyPaymentLine;
          }
  
          $change_return = $this->dummyPaymentLine;

        $default_location = null;
        foreach ($business_locations as $id => $name) {
            $default_location = BusinessLocation::findOrFail($id);
            break;
        }

        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;

            $commission_agent = User::saleCommissionAgentsDropdown($business_id);

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }

        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);


        $default_datetime = $this->businessUtil->format_date('now', true);

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        $status = request()->get('status', '');

        $statuses = Transaction::sell_statuses();

        if ($sale_type == 'sales_order') {
            $status = 'ordered';
        }
        $transaction_date = $this->transactionUtil->format_date($package->transaction_date, true);
        
        $the_id=request()->session()->get('user.id');
        $use=User::where('id',$the_id)->first();
        $customer_groups = CustomerGroup::forDropdown($business_id);

      


          return view('sell_transaction.edit')
        ->with(compact('package',
        'business_locations',
        'change_return',
        'payment_lines',
       
        'default_location',
        'commission_agent',
        'types',
        'impl',
        
        'payment_line',
        'payment_types',
    
        'default_datetime',
        'pos_settings',
        'customer_groups',

   
        'status',
        'sale_type',
        'statuses',
        'transaction_date',
      
        'use'));

       
    }


      /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {


        try{

        $package = PackageTransaction::findOrFail($id);
        $package_transaction_line = PackageTransactionLine::where('package_transaction_id', $id);
        // dd($packinglistline);
        $package_transaction_line->delete();

            $trans_date = $request->input('transaction_date');
            $trans_date = date('Y-m-d H:i:s', strtotime($trans_date));
    
            $commission_agent = $request->input('commission_agent');
            $status = $request->input('status');
            $final_total = $request->input('final_total');
            $customer=$request->input('customer');
            // dd($request);
    
        
          
            if(!empty($request->input('packages'))){
            $package->update(['transaction_date' => $trans_date,'final_total' => $final_total,'status' => $status, 'created_by' => $commission_agent,'customer_id' => $customer]);
            $this->transactionUtil->createOrUpdatePaymentLines2($package, $request->input('payment'));
          //Update payment status
        //   $payment_status = $this->transactionUtil->updatePaymentStatus($package->id, $package->final_total);
    
        //   $package->payment_status = $payment_status;
    
            $packages = $request->input('packages');
            $arr = array();
            $somme_total=0;
            if ($request->has('packages')) {
                foreach ($packages as $packet) {
                    $total=$packet['price'];
                    $somme_total+=$total;
                    $pl = PackageTransactionLine::create(['package_transaction_id' => $package->id, 'package_id' => $packet['id'],  'price' => $total]);
    
                }
            }
           
            $invoice_no=str_pad($package->id, 4, '0', STR_PAD_LEFT);
             $package->update(['invoice_no' => $invoice_no]);
            $output = ['success' => 1, 'msg' => trans("added_successfully")];
            // return redirect()->route('Sell_transaction.index');
    
        
        } else {
            $output = ['success' => 0,
                        'msg' => trans("messages.something_went_wrong")
                    ];
                }
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $msg = trans("messages.something_went_wrong");
                    
                // if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                //     $msg = $e->getMessage();
                // }
                // if (get_class($e) == \App\Exceptions\AdvanceBalanceNotAvailable::class) {
                //     $msg = $e->getMessage();
                // }
    
                $output = ['success' => 0,
                                'msg' => $msg
                            ];
            }
            return redirect('sell-transaction')->with('status', $output);

        // try {
        //     $input = $request->except('_token');
        //     //status is send as quotation from edit sales screen.
        //     $input['is_quotation'] = 0;
        //     if ($input['status'] == 'quotation') {
        //         $input['status'] = 'draft';
        //         $input['is_quotation'] = 1;
        //         $input['sub_status'] = 'quotation';
        //     } else if ($input['status'] == 'proforma') {
        //         $input['status'] = 'draft';
        //         $input['sub_status'] = 'proforma';
        //         $input['is_quotation'] = 0;
        //     } else {
        //         $input['sub_status'] = null;
        //         $input['is_quotation'] = 0;
        //     }

        //     $is_direct_sale = false;
        //     if (!empty($input['products'])) {
        //         //Get transaction value before updating.
        //         $transaction_before = Transaction::find($id);
        //         $status_before =  $transaction_before->status;
        //         $rp_earned_before = $transaction_before->rp_earned;
        //         $rp_redeemed_before = $transaction_before->rp_redeemed;

        //         if ($transaction_before->is_direct_sale == 1) {
        //             $is_direct_sale = true;
        //         }

        //         $sales_order_ids = $transaction_before->sales_order_ids ?? [];

        //         //Check Customer credit limit
        //         $is_credit_limit_exeeded = $transaction_before->type == 'sell' ? $this->transactionUtil->isCustomerCreditLimitExeeded($input, $id) : false;

        //         if ($is_credit_limit_exeeded !== false) {
        //             $credit_limit_amount = $this->transactionUtil->num_f($is_credit_limit_exeeded, true);
        //             $output = ['success' => 0,
        //                         'msg' => __('lang_v1.cutomer_credit_limit_exeeded', ['credit_limit' => $credit_limit_amount])
        //                     ];
        //             if (!$is_direct_sale) {
        //                 return $output;
        //             } else {
        //                 return redirect()
        //                     ->action('SellController@index')
        //                     ->with('status', $output);
        //             }
        //         }

        //         //Check if there is a open register, if no then redirect to Create Register screen.
        //         if (!$is_direct_sale && $this->cashRegisterUtil->countOpenedRegister() == 0) {
        //             return redirect()->action('CashRegisterController@create');
        //         }

        //         $business_id = $request->session()->get('user.business_id');
        //         $user_id = $request->session()->get('user.id');
        //         $commsn_agnt_setting = $request->session()->get('business.sales_cmsn_agnt');

        //         $discount = ['discount_type' => $input['discount_type'],
        //                         'discount_amount' => $input['discount_amount']
        //                     ];
        //         $invoice_total = $this->productUtil->calculateInvoiceTotal($input['products'], $input['tax_rate_id'], $discount);

        //         if (!empty($request->input('transaction_date'))) {
        //             $input['transaction_date'] = $this->productUtil->uf_date($request->input('transaction_date'), true);
        //         }

        //         $input['commission_agent'] = !empty($request->input('commission_agent')) ? $request->input('commission_agent') : null;
        //         if ($commsn_agnt_setting == 'logged_in_user') {
        //             $input['commission_agent'] = $user_id;
        //         }

        //         if (isset($input['exchange_rate']) && $this->transactionUtil->num_uf($input['exchange_rate']) == 0) {
        //             $input['exchange_rate'] = 1;
        //         }

        //         //Customer group details
        //         $contact_id = $request->get('contact_id', null);
        //         $cg = $this->contactUtil->getCustomerGroup($business_id, $contact_id);
        //         $input['customer_group_id'] = (empty($cg) || empty($cg->id)) ? null : $cg->id;
                
        //         //set selling price group id
        //         $price_group_id = $request->has('price_group') ? $request->input('price_group') : null;

        //         $input['is_suspend'] = isset($input['is_suspend']) && 1 == $input['is_suspend']  ? 1 : 0;
        //         if ($input['is_suspend']) {
        //             $input['sale_note'] = !empty($input['additional_notes']) ? $input['additional_notes'] : null;
        //         }

        //         if ($status_before == 'draft' && !empty($request->input('invoice_scheme_id'))) {
        //             $input['invoice_scheme_id'] = $request->input('invoice_scheme_id');
        //         }

        //         //Types of service
        //         if ($this->moduleUtil->isModuleEnabled('types_of_service')) {
        //             $input['types_of_service_id'] = $request->input('types_of_service_id');
        //             $price_group_id = !empty($request->input('types_of_service_price_group')) ? $request->input('types_of_service_price_group') : $price_group_id;
        //             $input['packing_charge'] = !empty($request->input('packing_charge')) ?
        //             $this->transactionUtil->num_uf($request->input('packing_charge')) : 0;
        //             $input['packing_charge_type'] = $request->input('packing_charge_type');
        //             $input['service_custom_field_1'] = !empty($request->input('service_custom_field_1')) ?
        //             $request->input('service_custom_field_1') : null;
        //             $input['service_custom_field_2'] = !empty($request->input('service_custom_field_2')) ?
        //             $request->input('service_custom_field_2') : null;
        //             $input['service_custom_field_3'] = !empty($request->input('service_custom_field_3')) ?
        //             $request->input('service_custom_field_3') : null;
        //             $input['service_custom_field_4'] = !empty($request->input('service_custom_field_4')) ?
        //             $request->input('service_custom_field_4') : null;
        //             $input['service_custom_field_5'] = !empty($request->input('service_custom_field_5')) ?
        //             $request->input('service_custom_field_5') : null;
        //             $input['service_custom_field_6'] = !empty($request->input('service_custom_field_6')) ?
        //             $request->input('service_custom_field_6') : null;
        //         }

        //         $input['selling_price_group_id'] = $price_group_id;

        //         if ($this->transactionUtil->isModuleEnabled('tables')) {
        //             $input['res_table_id'] = request()->get('res_table_id');
        //         }
        //         if ($this->transactionUtil->isModuleEnabled('service_staff')) {
        //             $input['res_waiter_id'] = request()->get('res_waiter_id');
        //         }

        //         //upload document
        //         $document_name = $this->transactionUtil->uploadFile($request, 'sell_document', 'documents');
        //         if (!empty($document_name)) {
        //             $input['document'] = $document_name;
        //         }

        //         //Begin transaction
        //         DB::beginTransaction();

        //         $transaction = $this->transactionUtil->updateSellTransaction($id, $business_id, $input, $invoice_total, $user_id);

        //         //Update Sell lines
        //         $deleted_lines = $this->transactionUtil->createOrUpdateSellLines($transaction, $input['products'], $input['location_id'], true, $status_before);

        //         //Update update lines
        //         $is_credit_sale = isset($input['is_credit_sale']) && $input['is_credit_sale'] == 1 ? true : false;

        //         $new_sales_order_ids = $transaction->sales_order_ids ?? [];
        //         $sales_order_ids =array_unique(array_merge($sales_order_ids, $new_sales_order_ids));
                
        //         if (!empty($sales_order_ids)) {
        //             $this->transactionUtil->updateSalesOrderStatus($sales_order_ids);
        //         }

        //         if (!$transaction->is_suspend && !$is_credit_sale) {
        //             //Add change return
        //             $change_return = $this->dummyPaymentLine;
        //             $change_return['amount'] = $input['change_return'];
        //             $change_return['is_return'] = 1;
        //             if (!empty($input['change_return_id'])) {
        //                 $change_return['id'] = $input['change_return_id'];
        //             }
        //             $input['payment'][] = $change_return;
        //             $this->transactionUtil->createOrUpdatePaymentLines($transaction, $input['payment']);

        //             //Update cash register
        //             if (!$is_direct_sale) {
        //                 $this->cashRegisterUtil->updateSellPayments($status_before, $transaction, $input['payment']);
        //             }
        //         }

        //         if ($request->session()->get('business.enable_rp') == 1) {
        //             $this->transactionUtil->updateCustomerRewardPoints($contact_id, $transaction->rp_earned, $rp_earned_before, $transaction->rp_redeemed, $rp_redeemed_before);
        //         }
                
        //         Media::uploadMedia($business_id, $transaction, $request, 'shipping_documents', false, 'shipping_document');

        //         if ($transaction->type == 'sell') {
                    
        //             //Update payment status
        //             $payment_status = $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
        //             $transaction->payment_status = $payment_status;

        //             //Update product stock
        //             $this->productUtil->adjustProductStockForInvoice($status_before, $transaction, $input);

        //             //Allocate the quantity from purchase and add mapping of
        //             //purchase & sell lines in
        //             //transaction_sell_lines_purchase_lines table
        //             $business_details = $this->businessUtil->getDetails($business_id);
        //             $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        //             $business = ['id' => $business_id,
        //                             'accounting_method' => $request->session()->get('business.accounting_method'),
        //                             'location_id' => $input['location_id'],
        //                             'pos_settings' => $pos_settings
        //                         ];
        //             $this->transactionUtil->adjustMappingPurchaseSell($status_before, $transaction, $business, $deleted_lines);
        //         }
                
        //         $log_properties = [];
        //         if (isset($input['repair_completed_on'])) {
        //             $completed_on = !empty($input['repair_completed_on']) ? $this->transactionUtil->uf_date($input['repair_completed_on'], true) : null;
        //             if ($transaction->repair_completed_on != $completed_on) {
        //                 $log_properties['completed_on_from'] = $transaction->repair_completed_on;
        //                 $log_properties['completed_on_to'] = $completed_on;
        //             }
        //         }

        //         //Set Module fields
        //         if (!empty($input['has_module_data'])) {
        //             $this->moduleUtil->getModuleData('after_sale_saved', ['transaction' => $transaction, 'input' => $input]);
        //         }

        //         Media::uploadMedia($business_id, $transaction, $request, 'documents');

        //         $this->transactionUtil->activityLog($transaction, 'edited', $transaction_before);

        //         DB::commit();

        //         if ($request->input('is_save_and_print') == 1) {
        //             $url = $this->transactionUtil->getInvoiceUrl($id, $business_id);
        //             return redirect()->to($url . '?print_on_load=true');
        //         }
                    
        //         $msg = '';
        //         $receipt = '';
        //         $can_print_invoice = auth()->user()->can("print_invoice");
        //         $invoice_layout_id = $request->input('invoice_layout_id');

        //         if ($input['status'] == 'draft' && $input['is_quotation'] == 0) {
        //             $msg = trans("sale.draft_added");
        //         } elseif ($input['status'] == 'draft' && $input['is_quotation'] == 1) {
        //             $msg = trans("lang_v1.quotation_updated");
        //             if (!$is_direct_sale && $can_print_invoice) {
        //                 $receipt = $this->receiptContent($business_id, $input['location_id'], $transaction->id, null, false, true, $invoice_layout_id);
        //             } else {
        //                 $receipt = '';
        //             }
        //         } elseif ($input['status'] == 'final') {
        //             $msg = trans("sale.pos_sale_updated");
        //             if (!$is_direct_sale && $can_print_invoice) {
        //                 $receipt = $this->receiptContent($business_id, $input['location_id'], $transaction->id, null, false, true, $invoice_layout_id);
        //             } else {
        //                 $receipt = '';
        //             }
        //         }

        //         $output = ['success' => 1, 'msg' => $msg, 'receipt' => $receipt ];
        //     } else {
        //         $output = ['success' => 0,
        //                     'msg' => trans("messages.something_went_wrong")
        //                 ];
        //     }
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        //     $output = ['success' => 0,
        //                     'msg' => __('messages.something_went_wrong')
        //                 ];
        // }
    }

    /**
     * Display a listing sell drafts.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDrafts()
    {
        if (!auth()->user()->can('draft.view_all') && !auth()->user()->can('draft.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);

        $sales_representative = User::forDropdown($business_id, false, false, true);


        return view('sale_pos.draft')
            ->with(compact('business_locations', 'customers', 'sales_representative'));
    }

    /**
     * Display a listing sell quotations.
     *
     * @return \Illuminate\Http\Response
     */
    public function getQuotations()
    {
        if (!auth()->user()->can('quotation.view_all') && !auth()->user()->can('quotation.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);

        $sales_representative = User::forDropdown($business_id, false, false, true);

        return view('sale_pos.quotations')
            ->with(compact('business_locations', 'customers', 'sales_representative'));
    }

    /**
     * Send the datatable response for draft or quotations.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDraftDatables()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $is_quotation = request()->input('is_quotation', 0);

            $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');

            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->leftJoin('transaction_sell_lines as tsl', function ($join) {
                    $join->on('transactions.id', '=', 'tsl.transaction_id')
                        ->whereNull('tsl.parent_sell_line_id');
                })
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'draft')
                ->select(
                    'transactions.id',
                    'transaction_date',
                    'invoice_no',
                    'contacts.name',
                    'contacts.mobile',
                    'contacts.supplier_business_name',
                    'bl.name as business_location',
                    'is_direct_sale',
                    'sub_status',
                    DB::raw('COUNT( DISTINCT tsl.id) as total_items'),
                    DB::raw('SUM(tsl.quantity) as total_quantity'),
                    DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as added_by"),
                    'transactions.is_export'
                );

            if ($is_quotation == 1) {
                $sells->where('transactions.sub_status', 'quotation');

                if (!auth()->user()->can('quotation.view_all') && auth()->user()->can('quotation.view_own')) {
                    $sells->where('transactions.created_by', request()->session()->get('user.id'));
                }

            } else {
                if (!auth()->user()->can('draft.view_all') && auth()->user()->can('draft.view_own')) {
                    $sells->where('transactions.created_by', request()->session()->get('user.id'));
                }
            }


            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $sells->whereDate('transaction_date', '>=', $start)
                    ->whereDate('transaction_date', '<=', $end);
            }
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (!empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }

            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }

            if ($is_woocommerce) {
                $sells->addSelect('transactions.woocommerce_order_id');
            }

            $sells->groupBy('transactions.id');

            return Datatables::of($sells)
                ->addColumn(
                    'action', function ($row) {
                    $html = '<div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                    data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                    </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li>
                                    <a href="#" data-href="' . action('SellController@show', [$row->id]) . '" class="btn-modal" data-container=".view_modal">
                                        <i class="fas fa-eye" aria-hidden="true"></i>' . __("messages.view") . '
                                    </a>
                                    </li>';

                    if (auth()->user()->can('draft.update') || auth()->user()->can('quotation.update')) {
                        if ($row->is_direct_sale == 1) {
                            $html .= '<li>
                                            <a target="_blank" href="' . action('SellController@edit', [$row->id]) . '">
                                                <i class="fas fa-edit"></i>' . __("messages.edit") . '
                                            </a>
                                        </li>';
                        } else {
                            $html .= '<li>
                                            <a target="_blank" href="' . action('SellPosController@edit', [$row->id]) . '">
                                                <i class="fas fa-edit"></i>' . __("messages.edit") . '
                                            </a>
                                        </li>';
                        }
                    }

                    $html .= '<li>
                                    <a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '"><i class="fas fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a>
                                </li>';


                    if (config("constants.enable_download_pdf")) {
                        $sub_status = $row->sub_status == 'proforma' ? 'proforma' : '';
                        $html .= '<li>
                                        <a href="' . route('quotation.downloadPdf', ['id' => $row->id, 'sub_status' => $sub_status]) . '" target="_blank">
                                            <i class="fas fa-print" aria-hidden="true"></i>' . __("lang_v1.download_pdf") . '
                                        </a>
                                    </li>';
                    }

                    if ((auth()->user()->can("sell.create") || auth()->user()->can("direct_sell.access")) && config("constants.enable_convert_draft_to_invoice")) {
                        $html .= '<li>
                                        <a href="' . action('SellPosController@convertToInvoice', [$row->id]) . '" class="convert-draft"><i class="fas fa-sync-alt"></i>' . __("lang_v1.convert_to_invoice") . '</a>
                                    </li>';
                    }

                    if ($row->sub_status != "proforma") {
                        $html .= '<li>
                                        <a href="' . action('SellPosController@convertToProforma', [$row->id]) . '" class="convert-to-proforma"><i class="fas fa-sync-alt"></i>' . __("lang_v1.convert_to_proforma") . '</a>
                                    </li>';
                    }

                    if (auth()->user()->can('draft.delete') || auth()->user()->can('quotation.delete')) {
                        $html .= '<li>
                                <a href="' . action('SellPosController@destroy', [$row->id]) . '" class="delete-sale"><i class="fas fa-trash"></i>' . __("messages.delete") . '</a>
                                </li>';
                    }

                    if ($row->sub_status == "quotation") {
                        $html .= '<li>
                                        <a href="' . action("SellPosController@showInvoiceUrl", [$row->id]) . '" class="view_invoice_url"><i class="fas fa-eye"></i>' . __("lang_v1.view_quote_url") . '</a>
                                    </li>
                                    <li>
                                        <a href="#" data-href="' . action("NotificationController@getTemplate", ["transaction_id" => $row->id, "template_for" => "new_quotation"]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i>' . __("lang_v1.new_quotation_notification") . '
                                        </a>
                                    </li>';
                    }

                    $html .= '</ul></div>';

                    return $html;

                })
                ->removeColumn('id')
                ->editColumn('invoice_no', function ($row) {
                    $invoice_no = $row->invoice_no;
                    if (!empty($row->woocommerce_order_id)) {
                        $invoice_no .= ' <i class="fab fa-wordpress text-primary no-print" title="' . __('lang_v1.synced_from_woocommerce') . '"></i>';
                    }

                    if ($row->sub_status == 'proforma') {
                        $invoice_no .= '<br><span class="label bg-gray">' . __('lang_v1.proforma_invoice') . '</span>';
                    }

                    if (!empty($row->is_export)) {
                        $invoice_no .= '</br><small class="label label-default no-print" title="' . __('lang_v1.export') . '">' . __('lang_v1.export') . '</small>';
                    }

                    return $invoice_no;
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('total_items', '{{@format_quantity($total_items)}}')
                ->editColumn('total_quantity', '{{@format_quantity($total_quantity)}}')
                ->addColumn('conatct_name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br>@endif {{$name}}')
                ->filterColumn('conatct_name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('contacts.name', 'like', "%{$keyword}%")
                            ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('added_by', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view")) {
                            return action('SellController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }])
                ->rawColumns(['action', 'invoice_no', 'transaction_date', 'conatct_name'])
                ->make(true);
        }
    }

    /**
     * Creates copy of the requested sale.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function duplicateSell($id)
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $transaction = Transaction::where('business_id', $business_id)
                ->where('type', 'sell')
                ->findorfail($id);
            $duplicate_transaction_data = [];
            foreach ($transaction->toArray() as $key => $value) {
                if (!in_array($key, ['id', 'created_at', 'updated_at'])) {
                    $duplicate_transaction_data[$key] = $value;
                }
            }
            $duplicate_transaction_data['status'] = 'draft';
            $duplicate_transaction_data['payment_status'] = null;
            $duplicate_transaction_data['transaction_date'] = \Carbon::now();
            $duplicate_transaction_data['created_by'] = $user_id;
            $duplicate_transaction_data['invoice_token'] = null;

            DB::beginTransaction();
            $duplicate_transaction_data['invoice_no'] = $this->transactionUtil->getInvoiceNumber($business_id, 'draft', $duplicate_transaction_data['location_id']);

            //Create duplicate transaction
            $duplicate_transaction = Transaction::create($duplicate_transaction_data);

            //Create duplicate transaction sell lines
            $duplicate_sell_lines_data = [];

            foreach ($transaction->sell_lines as $sell_line) {
                $new_sell_line = [];
                foreach ($sell_line->toArray() as $key => $value) {
                    if (!in_array($key, ['id', 'transaction_id', 'created_at', 'updated_at', 'lot_no_line_id'])) {
                        $new_sell_line[$key] = $value;
                    }
                }

                $duplicate_sell_lines_data[] = $new_sell_line;
            }

            $duplicate_transaction->sell_lines()->createMany($duplicate_sell_lines_data);

            DB::commit();

            $output = ['success' => 0,
                'msg' => trans("lang_v1.duplicate_sell_created_successfully")
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = ['success' => 0,
                'msg' => trans("messages.something_went_wrong")
            ];
        }

        if (!empty($duplicate_transaction)) {
            if ($duplicate_transaction->is_direct_sale == 1) {
                return redirect()->action('SellController@edit', [$duplicate_transaction->id])->with(['status', $output]);
            } else {
                return redirect()->action('SellPosController@edit', [$duplicate_transaction->id])->with(['status', $output]);
            }
        } else {
            abort(404, 'Not Found.');
        }
    }

    /**
     * Shows modal to edit shipping details.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function editShipping($id,Request $request)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (!$is_admin && !auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
            abort(403, 'Unauthorized action.');
        }

        $type_id=$id;
        $address = Address::join(
            'transactions',
            'addresses.id',
            '=',
            'transactions.address_id'
        )->where('transactions.id', $type_id)->first();    

        $all_address= Address::All();
        //requete ajax
        if (request()->ajax()) {
            //$centre_villes= CentreVille::pluck('commune', 'id');
            $selectedbrand= $request->get('selectedbrand',false);
            if($selectedbrand==1){
                $addresses= Address::where('id_indication',1)->select(['id','nom'])->get();
                return $addresses;
            }

           if($selectedbrand==2){
                $addresses= Address::where('id_indication',2)->select(['id','nom'])->get();
                return $addresses;
            }
            if($selectedbrand==3){
               
                return $all_address;
            }
      
          
        }

        $business_id = request()->session()->get('user.business_id');

        $transaction = Transaction::where('business_id', $business_id)
            ->with(['media', 'media.uploaded_by_user'])
            ->findorfail($id);
        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        $shipper = Shipper::join(
            'transactions',
            'shippers.id',
            '=',
            'transactions.shipper_id'
        )->where('transactions.id', $id)->first();

//dd($shipper->shipper_id);

        $shippers = Shipper::pluck('shipper_name', 'id');

        $activities = Activity::forSubject($transaction)
            ->with(['causer', 'subject'])
            ->where('activity_log.description', 'shipping_edited')
            ->latest()
            ->get();
        $carbon = \Carbon::now();
        return view('sell.partials.edit_shipping')
            ->with(compact('transaction', 'shippers', 'shipper', 'shipping_statuses', 'activities', 'carbon','all_address','address','type_id'));
    }

    /**
     * Update shipping.
     *
     * @param Request $request , int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateShipping(Request $request, $id)
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (!$is_admin && !auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only([
                'shipping_details', 'shipping_date', 'shipping_charges', 'address_id', 'shipper_id',
                'shipping_status', 'delivered_to', 'shipping_custom_field_1', 'shipping_custom_field_2', 'shipping_custom_field_3', 'shipping_custom_field_4', 'shipping_custom_field_5'
            ]);

            $business_id = $request->session()->get('user.business_id');


            $transaction = Transaction::where('business_id', $business_id)
                ->findOrFail($id);

            $transaction_before = $transaction->replicate();

            $transaction->update($input);

            $activity_property = ['update_note' => $request->input('shipping_note', '')];
            $this->transactionUtil->activityLog($transaction, 'shipping_edited', $transaction_before, $activity_property);

            $output = ['success' => 1,
                'msg' => trans("lang_v1.updated_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = ['success' => 0,
                'msg' => trans("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Display list of shipments.
     *
     * @return \Illuminate\Http\Response
     */
    public function shipments()
    {
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (!$is_admin && !auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
            abort(403, 'Unauthorized action.');
        }

        $shipping_statuses = $this->transactionUtil->shipping_statuses();

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);

        $sales_representative = User::forDropdown($business_id, false, false, true);

        $is_service_staff_enabled = $this->transactionUtil->isModuleEnabled('service_staff');

        //Service staff filter
        $service_staffs = null;
        if ($this->productUtil->isModuleEnabled('service_staff')) {
            $service_staffs = $this->productUtil->serviceStaffDropdown($business_id);
        }

        $shippers = Shipper::pluck('shipper_name', 'id');

        return view('sell.shipments')->with(compact('shipping_statuses'))
            ->with(compact('business_locations', 'customers', 'sales_representative', 'is_service_staff_enabled', 'service_staffs', 'shippers'));
    }

    public function viewMedia($model_id)
    {
        if (request()->ajax()) {
            $model_type = request()->input('model_type');
            $business_id = request()->session()->get('user.business_id');

            $query = Media::where('business_id', $business_id)
                ->where('model_id', $model_id)
                ->where('model_type', $model_type);

            $title = __('lang_v1.attachments');
            if (!empty(request()->input('model_media_type'))) {
                $query->where('model_media_type', request()->input('model_media_type'));
                $title = __('lang_v1.shipping_documents');
            }

            $medias = $query->get();

            return view('sell.view_media')->with(compact('medias', 'title'));
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

            $array_of_box = array();
            $search_term = request()->input('term', '');
            $customer = request()->input('customer', '');
            if(!empty($customer)){
            $packingLines = Package::query()
                ->where('bar_code', 'LIKE', '%' . $search_term . '%')
                ->orWhere('product', 'LIKE', '%' . $search_term . '%')
                
                // ->orWhereHas('thepackage_package', function ($q) use ($search_term) {
                //     $q->where('bar_code', 'LIKE', '%' . $search_term . '%')
                //         ->orWhere('product', 'LIKE', '%' . $search_term . '%');
                // })
                ->get();
                $pack=$packingLines->Where('customer_id',  $customer);
            
            if (!empty($pack)) {
                foreach ($pack as $packingLine) {
                    $product = $packingLine->product;
                    $barcode = $packingLine->bar_code;
                    $commission_agent = $packingLine->commission_agent;
                    $mode_transport = $packingLine->mode_transport;
                    $price = $packingLine->price;
                    // $customer_tel = $packingLine->customer_tel;
                    // $customer_name = $packingLine->customer_name;

                    $length = number_format($packingLine->longueur);
                    $width = number_format($packingLine->largeur);
                    $height = number_format($packingLine->hauteur);
                    $dimension = "";
                    if ($length > 0 && $width > 0 && $height > 0) {
                        $dimension = '(' . $length . 'x' . $width . 'x' . $height . 'cm)';
                    }
                    $array_of_package = array();

                    $displayLine = $barcode . ' - ' . $dimension;
                    if (!empty($product)) {
                        $displayLine .= ' - ' . $product;
                    }
                    // if (!empty($packages)) {
                    //     foreach ($packages as $package) {
                    //         $p_product = $package->product;
                    //         $p_barcode = $package->bar_code;
                    //         $term_in_p_product = strpos($p_product, $search_term);
                    //         $term_in_p_barcode = strpos($p_barcode, $search_term);
                    //         $displayLine .= ' - ' . $p_product . '(' . $package->customer_name . ')';
                    //         array_push($array_of_package, array(
                    //             'p' => $p_product,
                    //             'bc' => $p_barcode,
                    //             'c_name' => $package->customer_name,
                    //             'c_contact' => $package->customer_tel
                    //         ));
                    //     }
                    // }

                    array_push($array_of_box, array(
                        'id' => $packingLine->id,
                        'product' => $product,
                        'barcode' => $barcode,
                        'price' => $price,
                        'commission_agent' => $commission_agent,
                        'mode_transport' => $mode_transport,
                        // 'customer_tel' => $customer_tel,
                        // 'customer_name' => $customer_name,
                        'dimension' => $dimension,
                        'displayLine' => $displayLine,
                        'packages' => $array_of_package
                    ));
                }
            }
        }
            return json_encode($array_of_box);
        }
    }

     /**
     * List of ThePackage
     * @return JSON
     */
    public function editListThePackage(Request $request)
    {
        //requete ajax
        if (request()->ajax()) {

            $array_of_box = array();
            $id = $request->get('term', false);
            // $customer = $request()->input('customer', '');
            
            $packingLines = Package::findOrFail($id);
        
            return $packingLines;

        }
        }
            

}
