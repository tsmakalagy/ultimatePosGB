@extends('layouts.app')
@section('title', __('home.home'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header content-header-custom">
    <h1>{{ __('home.welcome_message', ['name' => Session::get('user.first_name')]) }}
    </h1>
</section>
<!-- Main content -->
<section class="content content-custom no-print">
    <br>
    @if(auth()->user()->can('dashboard.data'))
        
        	<div class="row">
                <div class="col-md-4 col-xs-12">
                    @if(count($all_locations) > 1)
                        {!! Form::select('dashboard_location', $all_locations, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.select_location'), 'id' => 'dashboard_location']); !!}
                    @endif
                </div>
        		<div class="col-md-8 col-xs-12">
                    <div class="form-group pull-right">
                          <div class="input-group">
                            <button type="button" class="btn btn-primary" id="dashboard_date_filter">
                              <span>
                                <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                              </span>
                              <i class="fa fa-caret-down"></i>
                            </button>
                          </div>
                    </div>
        		</div>
        	</div>
    	   <br>
    	   <div class="row row-custom">
            	<div class="col-md-3 col-sm-6 col-xs-12 col-custom">
            	   <div class="info-box info-box-new-style">
            	        <span class="info-box-icon bg-aqua"><i class="ion ion-cash"></i></span>

            	        <div class="info-box-content">
            	          <span class="info-box-text">{{ __('home.total_purchase') }}</span>
            	          <span class="info-box-number total_purchase"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
            	        </div>
            	        <!-- /.info-box-content -->
            	   </div>
        	       <!-- /.info-box -->
        	    </div>
        	    <!-- /.col -->
        	    <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
        	       <div class="info-box info-box-new-style">
            	        <span class="info-box-icon bg-aqua"><i class="ion ion-ios-cart-outline"></i></span>

            	        <div class="info-box-content">
            	          <span class="info-box-text">{{ __('home.total_sell') }}</span>
            	          <span class="info-box-number total_sell"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
            	        </div>
            	        <!-- /.info-box-content -->
        	       </div>
        	      <!-- /.info-box -->
        	    </div>
        	    <!-- /.col -->
        	    <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
        	       <div class="info-box info-box-new-style">
            	        <span class="info-box-icon bg-yellow">
            	        	<i class="fa fa-dollar"></i>
            				<i class="fa fa-exclamation"></i>
            	        </span>

            	        <div class="info-box-content">
            	          <span class="info-box-text">{{ __('home.purchase_due') }}</span>
            	          <span class="info-box-number purchase_due"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
            	        </div>
            	        <!-- /.info-box-content -->
        	       </div>
        	      <!-- /.info-box -->
        	    </div>
        	    <!-- /.col -->

    	       <!-- fix for small devices only -->
        	    <!-- <div class="clearfix visible-sm-block"></div> -->
        	    <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
        	        <div class="info-box info-box-new-style">
        	           <span class="info-box-icon bg-yellow">
            	        	<i class="ion ion-ios-paper-outline"></i>
            	        	<i class="fa fa-exclamation"></i>
        	           </span>

            	        <div class="info-box-content">
            	          <span class="info-box-text">{{ __('home.invoice_due') }}</span>
            	          <span class="info-box-number invoice_due"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
            	        </div>
            	        <!-- /.info-box-content -->
        	        </div>
        	      <!-- /.info-box -->
        	    </div>
    	    <!-- /.col -->
            </div>
          	<div class="row row-custom">
                <!-- expense -->
                <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
                    <div class="info-box info-box-new-style">
                        <span class="info-box-icon bg-red">
                          <i class="fas fa-minus-circle"></i>
                        </span>

                        <div class="info-box-content">
                          <span class="info-box-text">
                            {{ __('lang_v1.expense') }}
                          </span>
                          <span class="info-box-number total_expense"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                  <!-- /.info-box -->
                </div>
            </div>
            @if(!empty($widgets['after_sale_purchase_totals']))
                @foreach($widgets['after_sale_purchase_totals'] as $widget)
                    {!! $widget !!}
                @endforeach
            @endif
         


    
    
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.all_sales')])
        @can('direct_sell.access')
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('SellController@create')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
            @endslot
        @endcan
     
        @php
            $custom_labels = json_decode(session('business.custom_labels'), true);
         @endphp
            <table class="table table-bordered table-striped ajax_view" id="sell_table" >
                <thead>
                    <tr>
                        <th>@lang('messages.action')</th>
                        <th>@lang('messages.date')</th>
                        <th>@lang('sale.invoice_no')</th>
                        <th>@lang('sale.customer_name')</th>
                        <th>@lang('lang_v1.contact_no')</th>
                        <th>@lang('sale.location')</th>
                        <th>@lang('sale.payment_status')</th>
                        <th>@lang('lang_v1.payment_method')</th>
                        <th>@lang('lang_v1.paid_on')</th>
                        <th>@lang('sale.total_amount')</th>
                        <th>@lang('sale.paid')</th>
                        <th>@lang('lang_v1.sell_due')</th>
                        <th>@lang('lang_v1.sell_return_due')</th>
                        
                        <th>@lang('lang_v1.total_items')</th>
                        <th>@lang('lang_v1.types_of_service')</th>
                        <th>{{ $custom_labels['types_of_service']['custom_field_1'] ?? __('lang_v1.service_custom_field_1' )}}</th>
                        <th>@lang('lang_v1.added_by')</th>
                        <th>@lang('sale.sell_note')</th>
                        <th>@lang('sale.staff_note')</th>
                        <th>@lang('shipper.shipping_status')</th>
                        <th>@lang('shipper.shipper_name')</th>
                        <th>@lang('lang_v1.shipping_address')</th>
                        <th>@lang('lang_v1.shipping_location')</th>
                        <th>@lang('lang_v1.shipping_zone')</th>
                        <th>@lang('shipper.shipping_charges')</th>
                        <th>@lang('shipper.shipping_details')</th>
                        <th>@lang('shipper.shipping_date')</th>
                        <th>@lang('restaurant.table')</th>
                        <th>@lang('restaurant.service_staff')</th>
                        
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr class="bg-gray font-17 footer-total text-center">
                        <td colspan="6"><strong>@lang('sale.total'):</strong></td>
                        <td class="footer_payment_status_count"></td>
                        <td class="payment_method_count"></td>
                        <td class="footer_sale_total"></td>
                        <td class="footer_total_paid"></td>
                        <td class="footer_total_remaining"></td>
                        <td class="footer_total_sell_return_due"></td>
                        <td colspan="2"></td>
                        <td class="service_type_count"></td>
                        <td colspan="7"></td>
                    </tr>
                </tfoot>
            </table>
       
    @endcomponent



        <!-- end is_admin check -->
         @if(auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view'))
            @if(!empty($all_locations))
              	<!-- sales chart start -->
              	<div class="row">
              		<div class="col-sm-12">
                        @component('components.widget', ['class' => 'box-primary', 'title' => __('home.sells_last_30_days')])
                          {!! $sells_chart_1->container() !!}
                        @endcomponent
              		</div>
              	</div>
            @endif
            @if(!empty($widgets['after_sales_last_30_days']))
                @foreach($widgets['after_sales_last_30_days'] as $widget)
                    {!! $widget !!}
                @endforeach
            @endif
            @if(!empty($all_locations))
              	<div class="row">
              		<div class="col-sm-12">
                        @component('components.widget', ['class' => 'box-primary', 'title' => __('home.sells_current_fy')])
                          {!! $sells_chart_2->container() !!}
                        @endcomponent
              		</div>
              	</div>
            @endif
        @endif
      	<!-- sales chart end -->
        @if(!empty($widgets['after_sales_current_fy']))
            @foreach($widgets['after_sales_current_fy'] as $widget)
                {!! $widget !!}
            @endforeach
        @endif
      	<!-- products less than alert quntity -->
      	<div class="row">
            @if(auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view'))
                <div class="col-sm-6">
                    @component('components.widget', ['class' => 'box-warning'])
                      @slot('icon')
                        <i class="fa fa-exclamation-triangle text-yellow" aria-hidden="true"></i>
                      @endslot
                      @slot('title')
                        {{ __('lang_v1.sales_payment_dues') }} @show_tooltip(__('lang_v1.tooltip_sales_payment_dues'))
                      @endslot
                      <table class="table table-bordered table-striped" id="sales_payment_dues_table">
                        <thead>
                          <tr>
                            <th>@lang( 'contact.customer' )</th>
                            <th>@lang( 'sale.invoice_no' )</th>
                            <th>@lang( 'home.due_amount' )</th>
                            <th>@lang( 'messages.action' )</th>
                          </tr>
                        </thead>
                      </table>
                    @endcomponent
                </div>
            @endif
            @can('purchase.view')
                <div class="col-sm-6">
                    @component('components.widget', ['class' => 'box-warning'])
                    @slot('icon')
                    <i class="fa fa-exclamation-triangle text-yellow" aria-hidden="true"></i>
                    @endslot
                    @slot('title')
                    {{ __('lang_v1.purchase_payment_dues') }} @show_tooltip(__('tooltip.payment_dues'))
                    @endslot
                    <table class="table table-bordered table-striped" id="purchase_payment_dues_table">
                        <thead>
                          <tr>
                            <th>@lang( 'purchase.supplier' )</th>
                            <th>@lang( 'purchase.ref_no' )</th>
                            <th>@lang( 'home.due_amount' )</th>
                            <th>@lang( 'messages.action' )</th>
                          </tr>
                        </thead>
                    </table>
                    @endcomponent
                </div>
            @endcan
        </div>
        @can('stock_report.view')
            <div class="row">
                <div class="@if((session('business.enable_product_expiry') != 1) && auth()->user()->can('stock_report.view')) col-sm-12 @else col-sm-6 @endif">
                    @component('components.widget', ['class' => 'box-warning'])
                      @slot('icon')
                        <i class="fa fa-exclamation-triangle text-yellow" aria-hidden="true"></i>
                      @endslot
                      @slot('title')
                        {{ __('home.product_stock_alert') }} @show_tooltip(__('tooltip.product_stock_alert'))
                      @endslot
                      <table class="table table-bordered table-striped" id="stock_alert_table" style="width: 100%;">
                        <thead>
                          <tr>
                            <th>@lang( 'sale.product' )</th>
                            <th>@lang( 'business.location' )</th>
                            <th>@lang( 'report.current_stock' )</th>
                          </tr>
                        </thead>
                      </table>
                    @endcomponent
                </div>
                @if(session('business.enable_product_expiry') == 1)
                    <div class="col-sm-6">
                        @component('components.widget', ['class' => 'box-warning'])
                          @slot('icon')
                            <i class="fa fa-exclamation-triangle text-yellow" aria-hidden="true"></i>
                          @endslot
                          @slot('title')
                            {{ __('home.stock_expiry_alert') }} @show_tooltip( __('tooltip.stock_expiry_alert', [ 'days' =>session('business.stock_expiry_alert_days', 30) ]) )
                          @endslot
                          <input type="hidden" id="stock_expiry_alert_days" value="{{ \Carbon::now()->addDays(session('business.stock_expiry_alert_days', 30))->format('Y-m-d') }}">
                          <table class="table table-bordered table-striped" id="stock_expiry_alert_table">
                            <thead>
                              <tr>
                                  <th>@lang('business.product')</th>
                                  <th>@lang('business.location')</th>
                                  <th>@lang('report.stock_left')</th>
                                  <th>@lang('product.expires_in')</th>
                              </tr>
                            </thead>
                          </table>
                        @endcomponent
                    </div>
                @endif
      	    </div>
        @endcan
        @if(auth()->user()->can('so.view_all') || auth()->user()->can('so.view_own'))
            <div class="row" @if(!auth()->user()->can('dashboard.data'))style="margin-top: 190px !important;"@endif>
                <div class="col-sm-12">
                    @component('components.widget', ['class' => 'box-warning'])
                        @slot('icon')
                            <i class="fas fa-list-alt text-yellow fa-lg" aria-hidden="true"></i>
                        @endslot
                        @slot('title')
                            {{__('lang_v1.sales_order')}}
                        @endslot
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped ajax_view" id="sales_order_table">
                                <thead>
                                    <tr>
                                        <th>@lang('messages.action')</th>
                                        <th>@lang('messages.date')</th>
                                        <th>@lang('restaurant.order_no')</th>
                                        <th>@lang('sale.customer_name')</th>
                                        <th>@lang('lang_v1.contact_no')</th>
                                        <th>@lang('sale.location')</th>
                                        <th>@lang('sale.status')</th>
                                        <th>@lang('lang_v1.shipping_status')</th>
                                        <th>@lang('lang_v1.quantity_remaining')</th>
                                        <th>@lang('lang_v1.added_by')</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    @endcomponent
                </div>
            </div>
        @endif
        @if(!empty($common_settings['enable_purchase_order']) && (auth()->user()->can('purchase_order.view_all') || auth()->user()->can('purchase_order.view_own')) )
            <div class="row" @if(!auth()->user()->can('dashboard.data'))style="margin-top: 190px !important;"@endif>
                <div class="col-sm-12">
                    @component('components.widget', ['class' => 'box-warning'])
                      @slot('icon')
                          <i class="fas fa-list-alt text-yellow fa-lg" aria-hidden="true"></i>
                      @endslot
                      @slot('title')
                          @lang('lang_v1.purchase_order')
                      @endslot
                        <div class="table-responsive">
                                <table class="table table-bordered table-striped ajax_view" id="purchase_order_table" style="width: 100%;">
                                  <thead>
                                      <tr>
                                          <th>@lang('messages.action')</th>
                                          <th>@lang('messages.date')</th>
                                          <th>@lang('purchase.ref_no')</th>
                                          <th>@lang('purchase.location')</th>
                                          <th>@lang('purchase.supplier')</th>
                                          <th>@lang('sale.status')</th>
                                          <th>@lang('lang_v1.quantity_remaining')</th>
                                          <th>@lang('lang_v1.added_by')</th>
                                      </tr>
                                  </thead>
                                </table>
                        </div>
                    @endcomponent
                </div>
            </div>
        @endif

        @if(auth()->user()->can('access_pending_shipments_only') || auth()->user()->can('access_shipping') || auth()->user()->can('access_own_shipping') )
            @component('components.widget', ['class' => 'box-warning'])
              @slot('icon')
                  <i class="fas fa-list-alt text-yellow fa-lg" aria-hidden="true"></i>
              @endslot
              @slot('title')
                  @lang('lang_v1.pending_shipments')
              @endslot
                <div class="table-responsive">
                    <table class="table table-bordered table-striped ajax_view" id="shipments_table">
                        <thead>
                            <tr>
                                <th>@lang('messages.action')</th>
                                <th>@lang('messages.date')</th>
                                <th>@lang('sale.invoice_no')</th>
                                <th>@lang('sale.customer_name')</th>
                                <th>@lang('lang_v1.contact_no')</th>
                                <th>@lang('sale.location')</th>
                                <th>@lang('lang_v1.shipping_status')</th>
                                @if(!empty($custom_labels['shipping']['custom_field_1']))
                                    <th>
                                        {{$custom_labels['shipping']['custom_field_1']}}
                                    </th>
                                @endif
                                @if(!empty($custom_labels['shipping']['custom_field_2']))
                                    <th>
                                        {{$custom_labels['shipping']['custom_field_2']}}
                                    </th>
                                @endif
                                @if(!empty($custom_labels['shipping']['custom_field_3']))
                                    <th>
                                        {{$custom_labels['shipping']['custom_field_3']}}
                                    </th>
                                @endif
                                @if(!empty($custom_labels['shipping']['custom_field_4']))
                                    <th>
                                        {{$custom_labels['shipping']['custom_field_4']}}
                                    </th>
                                @endif
                                @if(!empty($custom_labels['shipping']['custom_field_5']))
                                    <th>
                                        {{$custom_labels['shipping']['custom_field_5']}}
                                    </th>
                                @endif
                                <th>@lang('sale.payment_status')</th>
                                <th>@lang('restaurant.service_staff')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        @endif

        @if(!empty($widgets['after_dashboard_reports']))
          @foreach($widgets['after_dashboard_reports'] as $widget)
            {!! $widget !!}
          @endforeach
        @endif

    @endif
   <!-- can('dashboard.data') end -->
</section>


<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade edit_pso_status_modal" tabindex="-1" role="dialog"></div>
<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>


@stop
@section('javascript')
    <script src="{{ asset('js/home.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
    @includeIf('sales_order.common_js')
    @includeIf('purchase_order.common_js')
    @if(!empty($all_locations))
        {!! $sells_chart_1->script() !!}
        {!! $sells_chart_2->script() !!}
    @endif
    <script type="text/javascript">
     
  

    </script>
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection

