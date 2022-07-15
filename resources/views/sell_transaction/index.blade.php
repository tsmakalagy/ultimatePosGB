@extends('layouts.app')
@section('title', __('lang_v1.all_sales'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>@lang('lang_v1.all_sales')
        </h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
        <div class="row col-md-10 col-sm-offset-1">
            <div class="container-fluid">
       
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('sell_list_filter_customer_id',  __('contact.customer') . ':') !!}
                {!! Form::select('sell_list_filter_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('sell_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
        </div>
    </div>
</div>
</div>

  
            @if ($is_woocommerce)
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('only_woocommerce_sells', 1, false, [
                                    'class' => 'input-icheck',
                                    'id' => 'synced_from_woocommerce',
                                ]) !!} {{ __('lang_v1.synced_from_woocommerce') }}
                            </label>
                        </div>
                    </div>
                </div>
            @endif
        @endcomponent
        @component('components.widget', ['class' => 'box-primary'])
            @if (auth()->user()->can('supplier.create') ||
                auth()->user()->can('customer.create') ||
                auth()->user()->can('supplier.view_own') ||
                auth()->user()->can('customer.view_own'))
                @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('SellTransactionController@create')}}">
                        <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
                @endslot
            @endif
            @if (auth()->user()->can('supplier.view') ||
                auth()->user()->can('customer.view') ||
                auth()->user()->can('supplier.view_own') ||
                auth()->user()->can('customer.view_own'))
                <table class="table table-bordered table-striped ajax_view " id="shipper_table" style="min-width: 100% ">
                    <thead class="text-center">
                        <tr>
                            <th>@lang('messages.action')</th>
                            <th>@lang('messages.date')</th>
                            <th>@lang('sale.invoice_no')</th>
                            <th>@lang('lang_v1.customer')</th>
                            {{-- <th>@lang('sale.customer_name')</th>
                            <th>@lang('lang_v1.contact_no')</th>
                            <th>@lang('sale.location')</th> --}}
                            <th>@lang('sale.payment_status')</th>
                            <th>@lang('lang_v1.payment_method')</th>
                            <th>@lang('sale.total_amount')</th>
                            <th>@lang('sale.total_paid')</th>
                            <th>@lang('lang_v1.sell_due')</th>
                            <th>@lang('lang_v1.commission_agent')</th>
                            {{-- <th>@lang('lang_v1.sell_due')</th> --}}
                            {{-- <th>@lang('lang_v1.sell_return_due')</th> --}}
                            
                            {{-- <th>@lang('lang_v1.total_items')</th>
                            <th>@lang('lang_v1.types_of_service')</th> --}}
                            {{-- <th>{{ $custom_labels['types_of_service']['custom_field_1'] ?? __('lang_v1.service_custom_field_1' )}}</th> --}}
                            {{-- <th>@lang('lang_v1.added_by')</th> --}}
                            {{-- <th>@lang('sale.sell_note')</th>
                            <th>@lang('sale.staff_note')</th>
                            <th>@lang('shipper.shipping_status')</th>
                            <th>@lang('shipper.shipper_name')</th>
                            <th>@lang('lang_v1.shipping_address')</th>
                            <th>@lang('lang_v1.shipping_location')</th>
                            <th>@lang('lang_v1.shipping_zone')</th>
                            <th>@lang('shipper.shipping_charges')</th>
                            <th>@lang('shipper.shipping_details')</th>
                            <th>@lang('shipper.shipping_date')</th>
                            <th>@lang('restaurant.table')</th> --}}
                            {{-- <th>@lang('restaurant.service_staff')</th> --}}


                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            @endif
        @endcomponent
        <div class="modal fade shipping_fee_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle">
        </div>
        <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
    
        <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
    </section>
    <!-- /.content -->

    <!-- This will be printed -->
    <!-- <section class="invoice print_section" id="receipt_section">
                                        </section> -->

@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {


            $('#sell_list_filter_date_range').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                        moment_date_format));
                    sell_table.ajax.reload();
                }
            );
            $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#sell_list_filter_date_range').val('');
                sell_table.ajax.reload();
            });

            sell_table = $('#shipper_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [1, 'desc']
                ],
                "ajax": {
                    "url": "/sell-transaction",
                    "data": function(d) {
                        if ($('#sell_list_filter_date_range').val()) {
                            var start = $('#sell_list_filter_date_range').data('daterangepicker')
                                .startDate.format('YYYY-MM-DD');
                            var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate
                                .format('YYYY-MM-DD');
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
                   
                    { data: 'action', name: 'action', orderable: false, "searchable": false},
            { data: 'transaction_date', name: 'transaction_date'  },
            { data: 'invoice_no', name: 'invoice_no'},
            // { data: 'conatct_name', name: 'c'},
            { data: 'customer', name: 'customer' },
            // { data: 'mobile', name: 'contacts.mobile'},
            // { data: 'business_location', name: 'bl.name'},
            { data: 'payment_status', name: 'payment_status'},
            { data: 'payment_methods', orderable: false, "searchable": false},
            { data: 'final_total', name: 'final_total'},
            { data: 'total_paid', name: 'total_paid'},
            { data: 'total_remaining', name: 'total_remaining'},
            { data: 'commission_agent', name: 'commission_agent', "searchable": false},
            // { data: 'total_remaining', name: 'total_remaining'},
            // { data: 'return_due', orderable: false, "searchable": false},
            
            // { data: 'total_items', name: 'total_items', "searchable": false},
            // { data: 'types_of_service_name', name: 'tos.name', @if(empty($is_types_service_enabled)) visible: false @endif},
            // { data: 'service_custom_field_1', name: 'service_custom_field_1', @if(empty($is_types_service_enabled)) visible: false @endif},
            // { data: 'added_by', name: 'u.first_name'},
            // { data: 'additional_notes', name: 'additional_notes'},
            // { data: 'staff_note', name: 'staff_note'},
            // { data: 'shipping_status', name: 'shipping_status'},
            // { data: 'shipper_name', name: 'shipper_name'},
            // { data: 'shipping_address', name: 'shipping_address'},
            // { data: 'delivered_to', name: 'delivered_to'},
            // { data: 'location', name: 'location'},
            // { data: 'shipping_charges', name: 'shipping_charges'},
            // { data: 'shipping_details', name: 'shipping_details'},
            // { data: 'shipping_date', name: 'shipping_date'},
            // { data: 'table_name', name: 'tables.name', @if(empty($is_tables_enabled)) visible: false @endif },
            // { data: 'waiter', name: 'ss.first_name', @if(empty($is_service_staff_enabled)) visible: false @endif },
  



                ],
                "fnDrawCallback": function(oSettings) {
                    __currency_convert_recursively($('#sell_table'));
                },

                createdRow: function(row, data, dataIndex) {
                    $(row).find('td:eq(6)').attr('class', 'clickable_td');
                }
            });

            $(document).on('change',
                '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status',
                function() {
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
