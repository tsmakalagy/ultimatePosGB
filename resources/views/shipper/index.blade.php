@extends('layouts.app')
@section('title', __( 'shipper.shipper'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang( 'shipper.shipper')
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">
    
    @component('components.filters', ['title' => __('report.filters')])
    
  <div class="row">
    <div class="col-md-12">
    
        <div class="container-fluid">
    {!! Form::open(['route' => 'shipper.store', 'class' => 'form-horizontal']) !!}
        <div class="col-md-3">
            <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('shipper_name', __('shipper.name') . '*:') !!}
                {!! Form::text('shipper_name', $value = null, ['class' => 'form-control', 'rows' => 3]); !!}
            </div>
        </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <div class="col-md-12">
                {!! Form::label('type', __('shipper.type') . '*:') !!}
                {!! Form::text('type', $value = null, ['class' => 'form-control', 'rows' => 3]); !!}
            </div>
        </div>
        </div>
        <div class="col-md-3">
            <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('tel', __('shipper.tel') . '*:') !!}
                {!! Form::text('tel', $value = null, ['class' => 'form-control', 'rows' => 3]); !!}
            </div>
        </div>
        </div>
        <div class="col-md-3">
            <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('other_details', __('shipper.other_details') . ':') !!}
                {!! Form::text('other_details', $value = null, ['class' => 'form-control', 'rows' => 3]); !!}
            </div>
        </div>
        </div>
       
<!-- Submit Button -->
<div class="form-group">
            <div class="col-lg-10 col-lg-offset-2">
                {!! Form::submit(__('messages.add'), ['class' => 'btn btn-lg btn-info pull-right'] ) !!}
            </div>
        </div>

        {!! Form::close()  !!}

        
        <div class="container-fluid">
    
    </div>
</div>

        @if($is_woocommerce)
            <div class="col-md-3">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                          {!! Form::checkbox('only_woocommerce_sells', 1, false, 
                          [ 'class' => 'input-icheck', 'id' => 'synced_from_woocommerce']); !!} {{ __('lang_v1.synced_from_woocommerce') }}
                        </label>
                    </div>
                </div>
            </div>
        @endif
    @endcomponent
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'shipper.shipper')])
        @can('direct_sell.access')
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('ShipperController@create')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
            @endslot
        @endcan
        @if(auth()->user()->can('direct_sell.view') ||  auth()->user()->can('view_own_sell_only') ||  auth()->user()->can('view_commission_agent_sell'))
        @php
            $custom_labels = json_decode(session('business.custom_labels'), true);
         @endphp
            <table class="table table-bordered table-striped ajax_view" id="sell_table">
                <thead>
                    <tr>
                        <th>@lang('messages.action')</th>
                        <th>@lang('shipper_name')</th>                       
                        <th>@lang('shipper.type')</th>
                        <th>@lang('shipper.tel')</th>
                        <th>@lang('shipper.other_details')</th>
                    </tr>
                </thead>
                <tbody></tbody>
   
            </table>
        @endif
    @endcomponent
</section>
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<!-- This will be printed -->
<!-- <section class="invoice print_section" id="receipt_section">
</section> -->

@stop

@section('javascript')
<script type="text/javascript">
$(document).ready( function(){
    //Date range as a button
    $('#sell_list_filter_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            sell_table.ajax.reload();
        }
    );
    $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#sell_list_filter_date_range').val('');
        sell_table.ajax.reload();
    });

    sell_table = $('#sell_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[1, 'desc']],
        "ajax": {
            "url": "/shipper",
            "data": function ( d ) {
                if($('#sell_list_filter_date_range').val()) {
                    var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                }
                d.is_direct_sale = 1;

                d.location_id = $('#sell_list_filter_location_id').val();
                d.customer_id = $('#sell_list_filter_customer_id').val();
                d.payment_status = $('#sell_list_filter_payment_status').val();
                d.created_by = $('#created_by').val();
                d.sales_cmsn_agnt = $('#sales_cmsn_agnt').val();
                d.service_staffs = $('#service_staffs').val();

                if($('#shipping_status').length) {
                    d.shipping_status = $('#shipping_status').val();
                }
                
                @if($is_woocommerce)
                    if($('#synced_from_woocommerce').is(':checked')) {
                        d.only_woocommerce_sells = 1;
                    }
                @endif

                if($('#only_subscriptions').is(':checked')) {
                    d.only_subscriptions = 1;
                }

                d = __datatable_ajax_callback(d);
            }
        },
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        columns: [
            { data: 'action', name: 'action', orderable: false, "searchable": false},
   
    
            { data: 'name', name: 'name'},
            { data: 'type', name: 'type'},
            { data: 'tel', name: 'tel'},
            { data: 'other_details', name: 'other_details'},

        ],
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('#sell_table'));
        },
 
        createdRow: function( row, data, dataIndex ) {
            $( row ).find('td:eq(6)').attr('class', 'clickable_td');
        }
    });

    $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status',  function() {
        sell_table.ajax.reload();
    });
    @if($is_woocommerce)
        $('#synced_from_woocommerce').on('ifChanged', function(event){
            sell_table.ajax.reload();
        });
    @endif

    $('#only_subscriptions').on('ifChanged', function(event){
        sell_table.ajax.reload();
    });
});
</script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection