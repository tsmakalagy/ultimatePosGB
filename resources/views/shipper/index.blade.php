@extends('layouts.app')
@section('title', __( 'lang_v1.all_sales'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang( 'sale.sells')
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">
    
 
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.all_sales')])
        @can('direct_sell.access')
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('SellController@create')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
            @endslot
        @endcan
        @if(auth()->user()->can('direct_sell.view') ||  auth()->user()->can('view_own_sell_only') ||  auth()->user()->can('view_commission_agent_sell'))
        @php
            $custom_labels = json_decode(session('business.custom_labels'), true);
         @endphp
            <table class="table table-bordered table-striped ajax_view" id="shipper_table">
                <thead>
                    <tr>
                        <th>@lang('messages.action')</th>
                        <th>@lang('shipper.name')</th>
                        <th>@lang('shipper.type')</th>
                        <th>@lang('shipper.tel')</th>
                        <th>@lang('shipper.other_details')</th>  
                        
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
            shipper_table.ajax.reload();
        }
    );
    $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#sell_list_filter_date_range').val('');
        shipper_table.ajax.reload();
    });

    shipper_table = $('#shipper_table').DataTable({
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
      
   
                d = __datatable_ajax_callback(d);
            }
        },
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        columns: [
            { data: 'action', name: 'action', orderable: false, "searchable": false},

            { data: 'name', name: 'name'  },
            { data: 'type', name: 'type'  },
            { data: 'tel', name: 'tel'  },
            { data: 'other_details', name: 'other_details'  },
        ],
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('#shipper_table'));
        },

        createdRow: function( row, data, dataIndex ) {
            $( row ).find('td:eq(6)').attr('class', 'clickable_td');
        }
    });

    $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status',  function() {
        shipper_table.ajax.reload();
    });
   

    $('#only_subscriptions').on('ifChanged', function(event){
        shipper_table.ajax.reload();
    });
});
</script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection