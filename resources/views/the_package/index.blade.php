@extends('layouts.app')
@section('title', __( 'lang_v1.package'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>@lang( 'lang_v1.package')
        </h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.package') ])
            @if(auth()->user()->can('supplier.create') || auth()->user()->can('customer.create') || auth()->user()->can('supplier.view_own') || auth()->user()->can('customer.view_own'))
            @slot('tool')
         
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action('ThePackageController@create')}}">
                <i class="fa fa-plus"></i> @lang('messages.add')</a>
            </div
        @endslot
            @endif
            @if(auth()->user()->can('supplier.view') || auth()->user()->can('customer.view') || auth()->user()->can('supplier.view_own') || auth()->user()->can('customer.view_own'))
                <table class="table table-bordered table-striped ajax_view " id="shipper_table"
                       style="min-width: 100% ">
                    <thead class="text-center">
                    <tr>
                       

                        <th>@lang('messages.action')</th>
                        <th>&nbsp;</th>
                                             
                        <th>@lang('lang_v1.customer')</th>
                        <th>@lang('lang_v1.mobile')</th>
                        <th>@lang('lang_v1.barcode')</th>
                        <th>@lang('lang_v1.product_name')</th> 
                        <th>@lang('lang_v1.longueur')</th>
                        <th>@lang('lang_v1.largeur')</th>
                        <th>@lang('lang_v1.hauteur')</th>
                        <th>@lang('lang_v1.weight')</th>                
                        
                        <th>@lang('lang_v1.other_field1')</th>
                        <th>@lang('lang_v1.other_field2')</th>
                        <th>@lang('lang_v1.status')</th>  
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            @endif
        @endcomponent
            {{-- <div class="modal fade package_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle">
            </div> --}}
            {{-- <div class="modal fade package_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade" id="view_package_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
 --}}
 <div class="modal product_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal " id="view_product_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal scan_modal" id="scan_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal uploadImg_modal" id="uploadImg_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

    </section>
    <!-- /.content -->

    <!-- This will be printed -->
    <!-- <section class="invoice print_section" id="receipt_section">
    </section> -->

@stop

@section('javascript')
 @php $asset_v = env('APP_VERSION'); @endphp
  <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#myModal').on('shown.bs.modal', function () {
    $('#my_barcode').focus();
});


            shipper_table = $('#shipper_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[1, 'desc']],
                "ajax": {
                    "url": "/the-package",
                    "data": function (d) {
                        if ($('#sell_list_filter_date_range').val()) {
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

                        if ($('#shipping_status').length) {
                            d.shipping_status = $('#shipping_status').val();
                        }


                        d = __datatable_ajax_callback(d);
                    }
                },
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                columns: [
                    {data: 'action', name: 'action', orderable: false, "searchable": false},
                    {data: 'image', name: 'packages.image'},
                                               
                    {data: 'customer_name', name: 'customer_name'},
                    {data: 'customer_tel', name: 'customer_tel'},
                    {data: 'bar_code', name: 'bar_code'},
                    {data: 'product', name: 'product'}, 
                    {data: 'longueur', name: 'longueur'},
                    {data: 'largeur', name: 'largeur'},
                    {data: 'hauteur', name: 'hauteur'},
                    {data: 'weight', name: 'weight'},                 
                                     
                    {data: 'other_field1', name: 'other_field1'},
                    {data: 'other_field2', name: 'other_field2'},
                    {data: 'status', name: 'status'}
                   
                ],
                "fnDrawCallback": function (oSettings) {
                    __currency_convert_recursively($('#sell_table'));
                },

                createdRow: function (row, data, dataIndex) {
                    $(row).find('td:eq(6)').attr('class', 'clickable_td');
                }
            });

            $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status', function () {
                sell_table.ajax.reload();
            });
        });
    </script>
    <script type="text/javascript">
        $(document).on('shown.bs.modal', '.shipper_modal', function(e) {
            // initAutocomplete();
        });
    </script>
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>


@endsection
