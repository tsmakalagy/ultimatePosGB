<?php

namespace App\Http\Controllers;

use App\Account;
use App\Business;
use App\Shipper;

use App\Address;
use App\CustomerGroup;
use App\InvoiceScheme;
use App\SellingPriceGroup;
use App\TaxRate;
use App\TransactionSellLine;
use App\TypesOfService;
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



use App\BusinessLocation;
use App\Contact;

use App\Charts\CommonChart;
use App\Currency;
use App\Transaction;

use App\TransactionPayment;
use App\VariationLocationDetails;
use App\Utils\Util;
use App\Utils\RestaurantUtil;
use App\User;

use Illuminate\Notifications\DatabaseNotification;


class HomeController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $contactUtil;
    protected $businessUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    protected $commonUtil;
    protected $restUtil;
    protected $productUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        ContactUtil $contactUtil,
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        ModuleUtil $moduleUtil,
        Util $commonUtil,
        RestaurantUtil $restUtil,
        ProductUtil $productUtil
    ) {
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->commonUtil = $commonUtil;
        $this->restUtil = $restUtil;
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
   { 
       $id=request()->session()->get('user.id');
        $business_id = request()->session()->get('user.business_id');
        $use=User::where('id',$id)->first();
      //  $sell_details = $this->transactionUtil->getListSells($business_id, null,null,null,null);
        //dd($sell_details->get());

        $is_admin = $this->businessUtil->is_admin(auth()->user());

        if (!auth()->user()->can('dashboard.data')) {
            return view('home.index');
        }

        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));

        $currency = Currency::where('id', request()->session()->get('business.currency_id'))->first();
        
        //Chart for sells last 30 days
        $sells_last_30_days = $this->transactionUtil->getSellsLast30Days($business_id);
        $labels = [];
        $all_sell_values = [];
        $dates = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = \Carbon::now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;

            $labels[] = date('j M Y', strtotime($date));

            if (!empty($sells_last_30_days[$date])) {
                $all_sell_values[] = (float) $sells_last_30_days[$date];
            } else {
                $all_sell_values[] = 0;
            }
        }

        //Get sell for indivisual locations
        $all_locations = BusinessLocation::forDropdown($business_id)->toArray();
        $location_sells = [];
        $sells_by_location = $this->transactionUtil->getSellsLast30Days($business_id, true);
        foreach ($all_locations as $loc_id => $loc_name) {
            $values = [];
            foreach ($dates as $date) {
                $sell = $sells_by_location->first(function ($item) use ($loc_id, $date) {
                    return $item->date == $date &&
                        $item->location_id == $loc_id;
                });
                
                if (!empty($sell)) {
                    $values[] = (float) $sell->total_sells;
                } else {
                    $values[] = 0;
                }
            }
            $location_sells[$loc_id]['loc_label'] = $loc_name;
            $location_sells[$loc_id]['values'] = $values;
        }

        $sells_chart_1 = new CommonChart;

        $sells_chart_1->labels($labels)
                        ->options($this->__chartOptions(__(
                            'home.total_sells',
                            ['currency' => $currency->code]
                            )));

        if (!empty($location_sells)) {
            foreach ($location_sells as $location_sell) {
                $sells_chart_1->dataset($location_sell['loc_label'], 'line', $location_sell['values']);
            }
        }

        if (count($all_locations) > 1) {
            $sells_chart_1->dataset(__('report.all_locations'), 'line', $all_sell_values);
        }

        //Chart for sells this financial year
        $sells_this_fy = $this->transactionUtil->getSellsCurrentFy($business_id, $fy['start'], $fy['end']);

        $labels = [];
        $values = [];

        $months = [];
        $date = strtotime($fy['start']);
        $last   = date('m-Y', strtotime($fy['end']));

        $fy_months = [];
        do {
            $month_year = date('m-Y', $date);
            $fy_months[] = $month_year;

            $month_number = date('m', $date);

            $labels[] = \Carbon::createFromFormat('m-Y', $month_year)
                            ->format('M-Y');
            $date = strtotime('+1 month', $date);

            if (!empty($sells_this_fy[$month_year])) {
                $values[] = (float) $sells_this_fy[$month_year];
            } else {
                $values[] = 0;
            }
        } while ($month_year != $last);

        $fy_sells_by_location = $this->transactionUtil->getSellsCurrentFy($business_id, $fy['start'], $fy['end'], true);
        $fy_sells_by_location_data = [];

        foreach ($all_locations as $loc_id => $loc_name) {
            $values_data = [];
            foreach ($fy_months as $month) {
                $sell = $fy_sells_by_location->first(function ($item) use ($loc_id, $month) {
                    return $item->yearmonth == $month &&
                        $item->location_id == $loc_id;
                });
                
                if (!empty($sell)) {
                    $values_data[] = (float) $sell->total_sells;
                } else {
                    $values_data[] = 0;
                }
            }
            $fy_sells_by_location_data[$loc_id]['loc_label'] = $loc_name;
            $fy_sells_by_location_data[$loc_id]['values'] = $values_data;
        }

        $sells_chart_2 = new CommonChart;
        $sells_chart_2->labels($labels)
                    ->options($this->__chartOptions(__(
                        'home.total_sells',
                        ['currency' => $currency->code]
                            )));
        if (!empty($fy_sells_by_location_data)) {
            foreach ($fy_sells_by_location_data as $location_sell) {
                $sells_chart_2->dataset($location_sell['loc_label'], 'line', $location_sell['values']);
            }
        }
        if (count($all_locations) > 1) {
            $sells_chart_2->dataset(__('report.all_locations'), 'line', $values);
        }

        //Get Dashboard widgets from module
        $module_widgets = $this->moduleUtil->getModuleData('dashboard_widget');

        $widgets = [];
        

        foreach ($module_widgets as $widget_array) {
            if (!empty($widget_array['position'])) {
                $widgets[$widget_array['position']][] = $widget_array['widget'];
            }
        }

        $common_settings = !empty(session('business.common_settings')) ? session('business.common_settings') : [];


//sell index
$business_id = request()->session()->get('user.business_id');
$is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');
$is_tables_enabled = $this->transactionUtil->isModuleEnabled('tables');
$is_service_staff_enabled = $this->transactionUtil->isModuleEnabled('service_staff');
$is_types_service_enabled = $this->moduleUtil->isModuleEnabled('types_of_service');

if (request()->ajax()) {

    //$start = request()->start;
    //$end = request()->end;
    $payment_types = $this->transactionUtil->payment_types(null, true, $business_id);
    $with = [];
    $shipping_statuses = $this->transactionUtil->shipping_statuses();

    $sale_type = !empty(request()->input('sale_type')) ? request()->input('sale_type') : 'sell';


    $id=request()->session()->get('user.id');
   
     $use=User::where('id',$id)->first();
     $user = request()->user;

    //if commission_agent connected
    if($use->is_cmmsn_agnt ==1){
    $sells = $this->transactionUtil->getListSellsCmmsnAgnt($business_id, $sale_type);
    }
    else{
    $sells = $this->transactionUtil->getListSells($business_id, $sale_type);
    }
    
    $permitted_locations = auth()->user()->permitted_locations();
    if ($permitted_locations != 'all') {
        $sells->whereIn('transactions.location_id', $permitted_locations);
    }

    //Add condition for created_by,used in sales representative sales report
   /* if (request()->has('created_by')) {
        $created_by = request()->get('created_by');
        if (!empty($created_by)) {
            $sells->where('transactions.created_by', $created_by);
        }
    }
*/

//condition for  commission_agent
    if ($is_admin){
    $created_by=null;
        if(empty($user)){
            $cmmsn_agnt=null;
        }
        else{
            $cmmsn_agnt=$user;
        
        }
    }
    else{
        $created_by=$id;
        $cmmsn_agnt=null;
    }
    if (!empty($created_by)) {
        $sells->where('transactions.created_by', $created_by);
        
    }
    if (!empty($cmmsn_agnt)) {
        $sells->where('u.is_cmmsn_agnt', $cmmsn_agnt);
        
    }

    $partial_permissions = ['view_own_sell_only', 'view_commission_agent_sell', 'access_own_shipping', 'access_commission_agent_shipping'];
    if (!auth()->user()->can('direct_sell.view')) {
        $sells->where(function ($q) {
            if (auth()->user()->hasAnyPermission(['view_own_sell_only', 'access_own_shipping'])) {
               // $q->where('transactions.created_by', request()->session()->get('user.id'));
            }

            //if user is commission agent display only assigned sells
            if (auth()->user()->hasAnyPermission(['view_commission_agent_sell', 'access_commission_agent_shipping'])) {
                //$q->orWhere('transactions.commission_agent', request()->session()->get('user.id'));
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

   /* if (empty(request()->start_date) && empty(request()->end_date)) {
    $today = \Carbon::now()->format("YYYY-MM-dd");
    
        $sells->whereDate('tp.paid_on', $today);
           
    }*/

    if (!empty(request()->start_date) && !empty(request()->end_date)) {
        $start = request()->start_date;
        $end = request()->end_date;
        $sells->whereDate('tp.paid_on', '>=', $start)
            ->whereDate('tp.paid_on', '<=', $end);
    }
    if (empty(request()->start_date) && !empty(request()->end_date)) {
        $start = request()->start_date;
        $end = request()->end_date;
        $sells->whereDate('tp.paid_on', '<=', $end);
    }
    // ADD FILTER FOR SHIPPING DATE
   /* if (!empty(request()->shipment_start_date) && !empty(request()->shipment_start_date)) {
        $start = request()->shipment_start_date;
        $end = request()->shipment_end_date;
        $sells->whereDate('transactions.shipping_date', '>=', $start)
            ->whereDate('transactions.shipping_date', '<=', $end);
    }
*/
    // ADD CONDITION FOR SHIPPER
    if (!empty(request()->input('shippers'))) {
        $sells->where('shippers.id', request()->input('shippers'));
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
    //$transaction_payments=TransactionPayment::all(); 
    $sales_order_statuses = Transaction::sales_order_statuses();
    $datatable = Datatables::of($sells)
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

                if (auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.view") || auth()->user()->can("view_own_sell_only")) {
                    $html .= '<li><a href="#" data-href="' . action("SellController@show", [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                }
                if (!$only_shipments) {
                    if ($row->is_direct_sale == 0) {
                        if (auth()->user()->can("sell.update")) {
                            $html .= '<li><a target="_blank" href="' . action('SellPosController@edit', [$row->id]) . '"><i class="fas fa-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                    } elseif ($row->type == 'sales_order') {
                        if (auth()->user()->can("so.update")) {
                            $html .= '<li><a target="_blank" href="' . action('SellController@edit', [$row->id]) . '"><i class="fas fa-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                    } else {
                        if (auth()->user()->can("direct_sell.update")) {
                            $html .= '<li><a target="_blank" href="' . action('SellController@edit', [$row->id]) . '"><i class="fas fa-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                    }

                    $delete_link = '<li><a href="' . action('SellPosController@destroy', [$row->id]) . '" class="delete-sale"><i class="fas fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                    if ($row->is_direct_sale == 0) {
                        if (auth()->user()->can("sell.delete")) {
                            $html .= $delete_link;
                        }
                    } elseif ($row->type == 'sales_order') {
                        if (auth()->user()->can("so.delete")) {
                            $html .= $delete_link;
                        }
                    } else {
                        if (auth()->user()->can("direct_sell.delete")) {
                            $html .= $delete_link;
                        }
                    }
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

                if ($is_admin || auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
                    $html .= '<li><a href="#" data-href="' . action('SellController@editShipping', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-truck" aria-hidden="true"></i>' . __("lang_v1.edit_shipping") . '</a></li>';
                }

                if ($row->type == 'sell') {
                    if (auth()->user()->can("print_invoice")) {
                        $html .= '<li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '"><i class="fas fa-print" aria-hidden="true"></i> ' . __("lang_v1.print_invoice") . '</a></li>
                            <li><a href="#" class="print-invoice" data-href="' . route('sell.printInvoice', [$row->id]) . '?package_slip=true"><i class="fas fa-file-alt" aria-hidden="true"></i> ' . __("lang_v1.packing_slip") . '</a></li>';
                    }
                    $html .= '<li class="divider"></li>';
                    if (!$only_shipments) {
                        if ($row->payment_status != "paid" && auth()->user()->can("sell.payments")) {
                            $html .= '<li><a href="' . action('TransactionPaymentController@addPayment', [$row->id]) . '" class="add_payment_modal"><i class="fas fa-money-bill-alt"></i> ' . __("purchase.add_payment") . '</a></li>';
                        }

                        $html .= '<li><a href="' . action('TransactionPaymentController@show', [$row->id]) . '" class="view_payment_modal"><i class="fas fa-money-bill-alt"></i> ' . __("purchase.view_payments") . '</a></li>';

                        if (auth()->user()->can("sell.create")) {
                            $html .= '<li><a href="' . action('SellController@duplicateSell', [$row->id]) . '"><i class="fas fa-copy"></i> ' . __("lang_v1.duplicate_sell") . '</a></li>

                            <li><a href="' . action('SellReturnController@add', [$row->id]) . '"><i class="fas fa-undo"></i> ' . __("lang_v1.sell_return") . '</a></li>

                            <li><a href="' . action('SellPosController@showInvoiceUrl', [$row->id]) . '" class="view_invoice_url"><i class="fas fa-eye"></i> ' . __("lang_v1.view_invoice_url") . '</a></li>';
                        }
                    }

                    $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id, "template_for" => "new_sale"]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i>' . __("lang_v1.new_sale_notification") . '</a></li>';
                } else {
                    $html .= '<li><a href="#" data-href="' . action('SellController@viewMedia', ["model_id" => $row->id, "model_type" => "App\Transaction", 'model_media_type' => 'shipping_document']) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-paperclip" aria-hidden="true"></i>' . __("lang_v1.shipping_documents") . '</a></li>';
                }

                $html .= '</ul></div>';

                return $html;
            }
        )
        ->removeColumn('id')
        ->editColumn(
            'final_total',
            '<span class="final-total" data-orig-value="{{$final_total}}">@format_currency($final_total)</span>'
        )
        ->editColumn(
            'tax_amount',
            '<span class="total-tax" data-orig-value="{{$tax_amount}}">@format_currency($tax_amount)</span>'
        )
        ->editColumn(
            'total_paid',
          //  '<span class="total-paid" data-orig-value="{{$total_paid}}">@format_currency($total_paid)</span>'
       // )
       function ($row) {
        $transaction_payments =TransactionPayment::where('transaction_id',$row->id)->get();
      //  $date_format=date_format($transaction_payments'Y-m-d',);
     //   $number=number_format($transaction_payments);
      $test='';
        foreach($transaction_payments as $transaction_payment){
            $test.='<span class="date_paid_on">'.number_format($transaction_payment->amount,2).'</span><br>';
        }
        return $test;


    })
        ->addColumn('date_paid_on',
        function ($row) {
            $transaction_payments =TransactionPayment::where('transaction_id',$row->id)->select( DB::raw("DATE_FORMAT(paid_on, '%Y/%m/%d') as paid_on"))->get();
          //  $date_format=date_format($transaction_payments'Y-m-d',);
            
          $test='';
            foreach($transaction_payments as $transaction_payment){
                $test.='<span class="date_paid_on">'.$transaction_payment->paid_on.'</span><br>';
            }
            return $test;
    

        }) 
         
        ->editColumn(
            'total_before_tax',
            '<span class="total_before_tax" data-orig-value="{{$total_before_tax}}">{{@format_date($date_paid_on)}}</span>'
        )
        ->editColumn(
            'discount_amount',
            function ($row) {
                $discount = !empty($row->discount_amount) ? $row->discount_amount : 0;

                if (!empty($discount) && $row->discount_type == 'percentage') {
                    $discount = $row->total_before_tax * ($discount / 100);
                }

                return '<span class="total-discount" data-orig-value="' . $discount . '">' . $this->transactionUtil->num_f($discount, true) . '</span>';
            }
        )
        ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')

            //Au cas Où
            /* ->editColumn('added_by',function ($row) use ($use) {
            if($use->is_cmmsn_agnt ==1){
            // $discount = !empty($row->discoun) ? $row->discount_amount : 0;
            $added_by= '<span class="added_by" data-orig-value="">@if(!empty($c_added_by)) {{$c_added_by}} @endif {{$u_added_by}} </span>';
             return $added_by ; }
            else{
            '<span class="added_by" data-orig-value="">@if(!empty($added_by)) {{$added_by}} @endif </span>';
            }
            })*/

        ->editColumn('shipper_name',
            '<span class="shipper_name" data-orig-value="{{$shipper_name}}">@if(!empty($shipper_name)) {{$shipper_name}} @endif </span>')
            ->editColumn('delivered_to',
            '<span class="delivered_to" data-orig-value="{{$nom}}">@if(!empty($nom)) {{$nom}} @endif </span>')
            ->editColumn('location',
            '<span class="location" data-orig-value="{{$lieu}}">@if(!empty($lieu)) {{$lieu}} @endif </span>')
            ->editColumn('shipping_address',
            '<span class="shipping_address" data-orig-value="{{$shipping_address}}">@if(!empty($shipping_address)) {{$shipping_address}} @endif </span>')
        ->editColumn('shipping_date',
            '<span class="shipping_date"> {{@format_date($shipping_date)}} </span>')
        ->editColumn('shipping_charges',
            '<span class="shipping_charges" data-orig-value=""> @format_currency($shipping_charges)  </span>')
        ->editColumn(
            'payment_status',
            function ($row) {
                $payment_status = Transaction::getPaymentStatus($row);
                return (string)view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id]);
            }
        )
        ->editColumn(
            'types_of_service_name',
            '<span class="service-type-label" data-orig-value="{{$types_of_service_name}}" data-status-name="{{$types_of_service_name}}">{{$types_of_service_name}}</span>'
        )
        ->addColumn('total_remaining', function ($row) {
            $total_remaining = $row->final_total - $row->total_paid;
            $total_remaining_html = '<span class="payment_due" data-orig-value="' . $total_remaining . '">' . $this->transactionUtil->num_f($total_remaining, true) . '</span>';


            return $total_remaining_html;
        })
        ->addColumn('return_due', function ($row) {
            $return_due_html = '';
            if (!empty($row->return_exists)) {
                $return_due = $row->amount_return - $row->return_paid;
                $return_due_html .= '<a href="' . action("TransactionPaymentController@show", [$row->return_transaction_id]) . '" class="view_purchase_return_payment_modal"><span class="sell_return_due" data-orig-value="' . $return_due . '">' . $this->transactionUtil->num_f($return_due, true) . '</span></a>';
            }

            return $return_due_html;
        })
        ->editColumn('invoice_no', function ($row) {
            $invoice_no = $row->invoice_no;
            if (!empty($row->woocommerce_order_id)) {
                $invoice_no .= ' <i class="fab fa-wordpress text-primary no-print" title="' . __('lang_v1.synced_from_woocommerce') . '"></i>';
            }
            if (!empty($row->return_exists)) {
                $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.some_qty_returned_from_sell') . '"><i class="fas fa-undo"></i></small>';
            }
            if (!empty($row->is_recurring)) {
                $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.subscribed_invoice') . '"><i class="fas fa-recycle"></i></small>';
            }

            if (!empty($row->recur_parent_id)) {
                $invoice_no .= ' &nbsp;<small class="label bg-info label-round no-print" title="' . __('lang_v1.subscription_invoice') . '"><i class="fas fa-recycle"></i></small>';
            }

            if (!empty($row->is_export)) {
                $invoice_no .= '</br><small class="label label-default no-print" title="' . __('lang_v1.export') . '">' . __('lang_v1.export') . '</small>';
            }

            return $invoice_no;
        })
        ->editColumn('shipping_status', function ($row) use ($shipping_statuses) {
            $status_color = !empty($this->shipping_status_colors[$row->shipping_status]) ? $this->shipping_status_colors[$row->shipping_status] : 'bg-gray';
            $status = !empty($row->shipping_status) ? '<a href="#" class="btn-modal" data-href="' . action('SellController@editShipping', [$row->id]) . '" data-container=".view_modal"><span class="label ' . $status_color . '">' . $shipping_statuses[$row->shipping_status] . '</span></a>' : '';

            return $status;
        })
        ->addColumn('shipper_name', function ($row) {
            $total_remaining = '';
            return $total_remaining;
        })
        ->addColumn('delivered_to', function ($row) {
            $total_remaining = '';
            return $total_remaining;
        })
        ->addColumn('location', function ($row) {
            $total_remaining = '';
            return $total_remaining;
        })
        ->addColumn('shipping_address', function ($row) {
            $total_remaining = '';
            return $total_remaining;
        })
        ->addColumn('shipping_date', function ($row) {
            $total_remaining = '';
            return $total_remaining;
        })
    
        ->addColumn('shipping_charges', function ($row) {
            $total_remaining = '';
            return $total_remaining;
        })
        ->addColumn('conatct_name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$name}}')
        ->editColumn('total_items', '{{@format_quantity($total_items)}}')
        ->filterColumn('conatct_name', function ($query, $keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('contacts.name', 'like', "%{$keyword}%")
                    ->orWhere('contacts.supplier_business_name', 'like', "%{$keyword}%");
            });
        })
        ->filterColumn('shipper_name', function ($query, $keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('shippers.shipper_name', 'like', "%{$keyword}%");
            });
        })
   
        ->filterColumn('shipping_address', function ($query, $keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('transactions.shipping_address', 'like', "%{$keyword}%");
                
            });
        })
        ->filterColumn('date_paid_on', function ($query, $keyword) {
            $query->where(function ($q) use ($keyword) {
               $q->whereDate(DB::raw('paid_on'), 'like', "%{$keyword}%")
                ;
            });
        })
        /*->filterColumn('shipping_charges', function ($query, $keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('transactions.shipping_charges', 'like', "%{$keyword}%");
            });
        })
        ->filterColumn('shipping_date', function ($query, $keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('transactions.shipping_date', 'like', "%{$keyword}%");
            });
        })
        */
        ->filterColumn('shipping_details', function ($query, $keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('transactions.shipping_details', 'like', "%{$keyword}%");
            });
        })

     
       
        ->addColumn('payment_methods', function ($row) {
           

            $transaction_payments =TransactionPayment::where('transaction_id',$row->id)->get();
        
            
          $test='';
            foreach($transaction_payments as $transaction_payment){
                $test.=!empty($transaction_payment->method) ? '<span class="payment-method" data-orig-value="' . $transaction_payment->method . '" data-status-name="' . $transaction_payment->method . '">' . $transaction_payment->method . '</span><br>' : '';
            }
            return $test;
    

            
        })
        ->editColumn('status', function ($row) use ($sales_order_statuses, $is_admin) {
            $status = '';

            if ($row->type == 'sales_order') {
                if ($is_admin && $row->status != 'completed') {
                    $status = '<span class="edit-so-status label ' . $sales_order_statuses[$row->status]['class'] . '" data-href="' . action("SalesOrderController@getEditSalesOrderStatus", ['id' => $row->id]) . '">' . $sales_order_statuses[$row->status]['label'] . '</span>';
                } else {
                    $status = '<span class="label ' . $sales_order_statuses[$row->status]['class'] . '" >' . $sales_order_statuses[$row->status]['label'] . '</span>';
                }
            }

            return $status;
        })
        ->editColumn('so_qty_remaining', '{{@format_quantity($so_qty_remaining)}}')
        ->setRowAttr([
            'data-href' => function ($row) {
                if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                    return action('SellController@show', [$row->id]);
                } else {
                    return '';
                }
            }]);

    $rawColumns = ['final_total','date_paid_on', 'action', 'shipping_date', 'shipping_charges', 'shipper_name', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status', 'shipping_address', 'delivered_to','types_of_service_name', 'payment_methods', 'return_due', 'conatct_name', 'location','status'];

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


return view('home.index', compact('date_filters', 'sells_chart_1', 'sells_chart_2', 'widgets', 'all_locations', 'common_settings', 'is_admin','use','business_locations', 'customers', 'is_woocommerce', 'sales_representative', 'is_cmsn_agent_enabled', 'commission_agents', 'service_staffs', 'is_tables_enabled', 'is_service_staff_enabled', 'is_types_service_enabled', 'shipping_statuses'));

//return view('home.index', compact('date_filters', 'sells_chart_1', 'sells_chart_2', 'widgets', 'all_locations', 'common_settings', 'is_admin','use'));
   
}

  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sell_index()
    {

   }


    /**
     * Retrieves purchase and sell details for a given time period.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTotals()
    { 
        $id=request()->session()->get('user.id');
        $business_id = request()->session()->get('user.business_id');
        $use=User::where('id',$id)->first();
        $is_admin = $this->businessUtil->is_admin(auth()->user());

        //Somme shipping_charges
        $shipping_charges=Transaction::select(DB::raw('SUM(transactions.shipping_charges) as shipping_charge'))->first();
        $shipp=$shipping_charges->shipping_charge;

        if (request()->ajax()) {
            $start = request()->start;
            $end = request()->end;
            $location_id = request()->location_id;
            $user = request()->user;

            $business_id = request()->session()->get('user.business_id');

            $purchase_details = $this->transactionUtil->getPurchaseTotals($business_id, $start, $end, $location_id);

            if($is_admin){
                $created_by=null;
                if(empty($user)){
                    $cmmsn_agnt=null;
                    }
                 else{
                    $cmmsn_agnt=$user;
                    
                    }
                }
            
            else{
                $created_by=$id;
                $cmmsn_agnt=null;

            }
            $sell_details = $this->transactionUtil->getSellTotals($business_id, $start, $end, $location_id,$created_by,$shipp,$cmmsn_agnt);

            $transaction_types = [
                'purchase_return', 'sell_return', 'expense'
            ];

            $transaction_totals = $this->transactionUtil->getTransactionTotals(
                $business_id,
                $transaction_types,
                $start,
                $end,
                $location_id
            );

            $total_purchase_inc_tax = !empty($purchase_details['total_purchase_inc_tax']) ? $purchase_details['total_purchase_inc_tax'] : 0;
            $total_purchase_return_inc_tax = $transaction_totals['total_purchase_return_inc_tax'];

            $total_purchase = $total_purchase_inc_tax - $total_purchase_return_inc_tax;
            $output = $purchase_details;
            $output['total_purchase'] = $total_purchase;

            $total_sell_inc_tax = !empty($sell_details['total_sell_inc_tax']) ? $sell_details['total_sell_inc_tax'] : 0;
            $total_sell_return_inc_tax = !empty($transaction_totals['total_sell_return_inc_tax']) ? $transaction_totals['total_sell_return_inc_tax'] : 0;

            $output['total_sell'] = $total_sell_inc_tax - $total_sell_return_inc_tax;

            $output['invoice_due'] = $sell_details['invoice_due'];
            $output['total_expense'] = $transaction_totals['total_expense'];
            
            return $output;
        }
    }

    /**
     * Retrieves sell products whose available quntity is less than alert quntity.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductStockAlert()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $query = VariationLocationDetails::join(
                'product_variations as pv',
                'variation_location_details.product_variation_id',
                '=',
                'pv.id'
            )
                    ->join(
                        'variations as v',
                        'variation_location_details.variation_id',
                        '=',
                        'v.id'
                    )
                    ->join(
                        'products as p',
                        'variation_location_details.product_id',
                        '=',
                        'p.id'
                    )
                    ->leftjoin(
                        'business_locations as l',
                        'variation_location_details.location_id',
                        '=',
                        'l.id'
                    )
                    ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                    ->where('p.business_id', $business_id)
                    ->where('p.enable_stock', 1)
                    ->where('p.is_inactive', 0)
                    ->whereNull('v.deleted_at')
                    ->whereRaw('variation_location_details.qty_available <= p.alert_quantity');

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('variation_location_details.location_id', $permitted_locations);
            }

            $products = $query->select(
                'p.name as product',
                'p.type',
                'p.sku',
                'pv.name as product_variation',
                'v.name as variation',
                'v.sub_sku',
                'l.name as location',
                'variation_location_details.qty_available as stock',
                'u.short_name as unit'
            )
                    ->groupBy('variation_location_details.id')
                    ->orderBy('stock', 'asc');

            return Datatables::of($products)
                ->editColumn('product', function ($row) {
                    if ($row->type == 'single') {
                        return $row->product . ' (' . $row->sku . ')';
                    } else {
                        return $row->product . ' - ' . $row->product_variation . ' - ' . $row->variation . ' (' . $row->sub_sku . ')';
                    }
                })
                ->editColumn('stock', function ($row) {
                    $stock = $row->stock ? $row->stock : 0 ;
                    return '<span data-is_quantity="true" class="display_currency" data-currency_symbol=false>'. (float)$stock . '</span> ' . $row->unit;
                })
                ->removeColumn('sku')
                ->removeColumn('sub_sku')
                ->removeColumn('unit')
                ->removeColumn('type')
                ->removeColumn('product_variation')
                ->removeColumn('variation')
                ->rawColumns([2])
                ->make(false);
        }
    }

    /**
     * Retrieves payment dues for the purchases.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchasePaymentDues()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $today = \Carbon::now()->format("Y-m-d H:i:s");

            $query = Transaction::join(
                'contacts as c',
                'transactions.contact_id',
                '=',
                'c.id'
            )
                    ->leftJoin(
                        'transaction_payments as tp',
                        'transactions.id',
                        '=',
                        'tp.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'purchase')
                    ->where('transactions.payment_status', '!=', 'paid')
                    ->whereRaw("DATEDIFF( DATE_ADD( transaction_date, INTERVAL IF(transactions.pay_term_type = 'days', transactions.pay_term_number, 30 * transactions.pay_term_number) DAY), '$today') <= 7");

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }

            $dues =  $query->select(
                'transactions.id as id',
                'c.name as supplier',
                'c.supplier_business_name',
                'ref_no',
                'final_total',
                DB::raw('SUM(tp.amount) as total_paid')
            )
                        ->groupBy('transactions.id');

            return Datatables::of($dues)
                ->addColumn('due', function ($row) {
                    $total_paid = !empty($row->total_paid) ? $row->total_paid : 0;
                    $due = $row->final_total - $total_paid;
                    return '<span class="display_currency" data-currency_symbol="true">' .
                    $due . '</span>';
                })
                ->addColumn('action', '@can("purchase.create") <a href="{{action("TransactionPaymentController@addPayment", [$id])}}" class="btn btn-xs btn-success add_payment_modal"><i class="fas fa-money-bill-alt"></i> @lang("purchase.add_payment")</a> @endcan')
                ->removeColumn('supplier_business_name')
                ->editColumn('supplier', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$supplier}}')
                ->editColumn('ref_no', function ($row) {
                    if (auth()->user()->can('purchase.view')) {
                        return  '<a href="#" data-href="' . action('PurchaseController@show', [$row->id]) . '"
                                    class="btn-modal" data-container=".view_modal">' . $row->ref_no . '</a>';
                    }
                    return $row->ref_no;
                })
                ->removeColumn('id')
                ->removeColumn('final_total')
                ->removeColumn('total_paid')
                ->rawColumns([0, 1, 2, 3])
                ->make(false);
        }
    }

    /**
     * Retrieves payment dues for the purchases.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSalesPaymentDues()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $today = \Carbon::now()->format("Y-m-d H:i:s");

            $query = Transaction::join(
                'contacts as c',
                'transactions.contact_id',
                '=',
                'c.id'
            )
                    ->leftJoin(
                        'transaction_payments as tp',
                        'transactions.id',
                        '=',
                        'tp.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.payment_status', '!=', 'paid')
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("DATEDIFF( DATE_ADD( transaction_date, INTERVAL IF(transactions.pay_term_type = 'days', transactions.pay_term_number, 30 * transactions.pay_term_number) DAY), '$today') <= 7");

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }

            $dues =  $query->select(
                'transactions.id as id',
                'c.name as customer',
                'c.supplier_business_name',
                'transactions.invoice_no',
                'final_total',
                DB::raw('SUM(tp.amount) as total_paid')
            )
                        ->groupBy('transactions.id');

            return Datatables::of($dues)
                ->addColumn('due', function ($row) {
                    $total_paid = !empty($row->total_paid) ? $row->total_paid : 0;
                    $due = $row->final_total - $total_paid;
                    return '<span class="display_currency" data-currency_symbol="true">' .
                    $due . '</span>';
                })
                ->editColumn('invoice_no', function ($row) {
                    if (auth()->user()->can('sell.view')) {
                        return  '<a href="#" data-href="' . action('SellController@show', [$row->id]) . '"
                                    class="btn-modal" data-container=".view_modal">' . $row->invoice_no . '</a>';
                    }
                    return $row->invoice_no;
                })
                ->addColumn('action', '@if(auth()->user()->can("sell.create") || auth()->user()->can("direct_sell.access")) <a href="{{action("TransactionPaymentController@addPayment", [$id])}}" class="btn btn-xs btn-success add_payment_modal"><i class="fas fa-money-bill-alt"></i> @lang("purchase.add_payment")</a> @endif')
                ->editColumn('customer', '@if(!empty($supplier_business_name)) {{$supplier_business_name}}, <br> @endif {{$customer}}')
                ->removeColumn('supplier_business_name')
                ->removeColumn('id')
                ->removeColumn('final_total')
                ->removeColumn('total_paid')
                ->rawColumns([0, 1, 2, 3])
                ->make(false);
        }
    }

    public function loadMoreNotifications()
    {
        $notifications = auth()->user()->notifications()->orderBy('created_at', 'DESC')->paginate(10);

        if (request()->input('page') == 1) {
            auth()->user()->unreadNotifications->markAsRead();
        }
        $notifications_data = $this->commonUtil->parseNotifications($notifications);

        return view('layouts.partials.notification_list', compact('notifications_data'));
    }

    /**
     * Function to count total number of unread notifications
     *
     * @return json
     */
    public function getTotalUnreadNotifications()
    {
        $unread_notifications = auth()->user()->unreadNotifications;
        $total_unread = $unread_notifications->count();

        $notification_html = '';
        $modal_notifications = [];
        foreach ($unread_notifications as $unread_notification) {
            if (isset($data['show_popup'])) {
                $modal_notifications[] = $unread_notification;
                $unread_notification->markAsRead();
            }
        }
        if (!empty($modal_notifications)) {
            $notification_html = view('home.notification_modal')->with(['notifications' => $modal_notifications])->render();
        }

        return [
            'total_unread' => $total_unread,
            'notification_html' => $notification_html
        ];
    }

    private function __chartOptions($title)
    {
        return [
            'yAxis' => [
                    'title' => [
                        'text' => $title
                    ]
                ],
            'legend' => [
                'align' => 'right',
                'verticalAlign' => 'top',
                'floating' => true,
                'layout' => 'vertical'
            ],
        ];
    }

    public function getCalendar()
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->restUtil->is_admin(auth()->user(), $business_id);
        $is_superadmin = auth()->user()->can('superadmin');
        if (request()->ajax()) {
            $data = [
                'start_date' => request()->start,
                'end_date' => request()->end,
                'user_id' => ($is_admin || $is_superadmin) && !empty(request()->user_id) ? request()->user_id : auth()->user()->id,
                'location_id' => !empty(request()->location_id) ? request()->location_id : null,
                'business_id' => $business_id,
                'events' => request()->events ?? [],
                'color' => '#007FFF'
            ];
            $events = [];

            if (in_array('bookings', $data['events'])) {
                $events = $this->restUtil->getBookingsForCalendar($data);
            }
            
            $module_events = $this->moduleUtil->getModuleData('calendarEvents', $data);

            foreach ($module_events as $module_event) {
                $events = array_merge($events, $module_event);
            }  

            return $events;
        }

        $all_locations = BusinessLocation::forDropdown($business_id)->toArray();
        $users = [];
        if ($is_admin) {
            $users = User::forDropdown($business_id, false);
        }

        $event_types = [
            'bookings' => [
                'label' => __('restaurant.bookings'),
                'color' => '#007FFF'
            ]
        ];
        $module_event_types = $this->moduleUtil->getModuleData('eventTypes');
        foreach ($module_event_types as $module_event_type) {
            $event_types = array_merge($event_types, $module_event_type);
        }
        
        return view('home.calendar')->with(compact('all_locations', 'users', 'event_types'));
    }

    public function showNotification($id)
    {
        $notification = DatabaseNotification::find($id);

        $data = $notification->data;

        $notification->markAsRead();

        return view('home.notification_modal')->with([
                'notifications' => [$notification]
            ]);
    }

    public function attachMediasToGivenModel(Request $request)
    {   
        if ($request->ajax()) {
            try {
                
                $business_id = request()->session()->get('user.business_id');

                $model_id = $request->input('model_id');
                $model = $request->input('model_type');
                $model_media_type = $request->input('model_media_type');

                DB::beginTransaction();

                //find model to which medias are to be attached
                $model_to_be_attached = $model::where('business_id', $business_id)
                                        ->findOrFail($model_id);

                Media::uploadMedia($business_id, $model_to_be_attached, $request, 'file', false, $model_media_type);

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success')
                ];
            } catch (Exception $e) {

                DB::rollBack();

                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }
    }
}
