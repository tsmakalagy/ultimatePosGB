@extends('layouts.app')

@php
if (!empty($status) && $status == 'quotation') {
    $title = __('lang_v1.add_quotation');
} elseif (!empty($status) && $status == 'draft') {
    $title = __('lang_v1.add_draft');
} else {
    $title = __('sale.edit_sale');
}

if ($sale_type == 'sales_order') {
    $title = __('lang_v1.edit_sales_order');
}
@endphp

@section('title', $title)

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>{{ $title }}</h1>
    </section>
    <!-- Main content -->


    <section class="content">

        {!! Form::open([
            'action' => ['SellTransactionController@update', $package->id],
            'files' => true,
            'enctype' => 'multipart/form-data',
        ]) !!}



        <div class="row">
            <div class="col-md-12 col-sm-12">
                @component('components.widget', ['class' => 'box-solid'])
                    {{-- {!! Form::hidden('location_id', !empty($default_location) ? $default_location->id : null, ['id' => 'location_id', 'data-receipt_printer_type' => !empty($default_location->receipt_printer_type) ? $default_location->receipt_printer_type : 'browser', 'data-default_payment_accounts' => !empty($default_location) ? $default_location->default_payment_accounts : '']) !!} --}}

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('customer', __('contact.customer') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                <input type="hidden" id="default_customer_id"
                                    value="{{ !empty($package->customer) ? $package->customer->id : '' }}">
                                <input type="hidden" id="default_customer_name"
                                    value="{{ !empty($package->customer) ? $package->customer->name : '' }}">
                                {!! Form::select('customer', [], null, [
                                    'class' => 'form-control mousetrap',
                                    'id' => 'customer_id',
                                    'placeholder' => 'Enter Customer name / phone',
                                ]) !!}
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default bg-white btn-flat add_new_customer"
                                        data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('customer', __('contact.customer') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                {!! Form::select('customer', [], null, [
                                    'class' => 'form-control mousetrap',
                                    'id' => 'customer',
                                    'placeholder' => __('shipment.customer_name_phone'),
                                    'required',
                                ]) !!}
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default bg-white btn-flat add_new_customer"
                                        data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                </span>
                            </div>
                        </div>
                    </div> --}}

                    @if (!empty($commission_agent))
                        @if ($use->is_cmmsn_agnt == 1)
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('commission_agent', __('lang_v1.commission_agent') . ':') !!}
                                    {!! Form::select('commission_agent', $commission_agent, $use->id, ['class' => 'form-control select2']) !!}
                                </div>
                            </div>
                        @else
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('commission_agent', __('lang_v1.commission_agent') . ':') !!}
                                    {!! Form::select('commission_agent', $commission_agent, $package->created_by, [
                                        'class' => 'form-control select2',
                                    ]) !!}
                                </div>
                            </div>
                        @endif
                    @endif

                    @if (!empty($status))
                        <input type="hidden" name="status" id="status" value="{{ $status }}">
                    @else
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label('status', __('sale.status') . ':*') !!}
                                {!! Form::select('status', $statuses, $package->status, [
                                    'class' => 'form-control select2',
                                    'placeholder' => __('messages.please_select'),
                                    'required',
                                ]) !!}
                            </div>
                        </div>
                    @endif
                    <div class=" col-sm-3 ">
                        <div class="form-group">
                            {!! Form::label('transaction_date', __('sale.sale_date') . ':*') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::text('transaction_date', $transaction_date, ['class' => 'form-control', 'readonly', 'required']) !!}
                            </div>
                        </div>
                    </div>
                @endcomponent
                {!! Form::hidden('pack_id', $impl, ['class' => 'form-control pack_id', 'rows' => 3]) !!}

                @component('components.widget', ['class' => 'box-solid'])
                    <div class="row">
                        <div class="col-sm-10 col-sm-offset-1">
                            <div class="form-group">
                                {!! Form::text('my_search_product', null, [
                                    'class' => 'form-control mousetrap',
                                    'id' => 'my_search_product',
                                    'placeholder' => __('lang_v1.search_product_placeholder'),
                                ]) !!}
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered add-parcel-table table-condensed"
                                id="the_package_add_parcel_form_part">
                                <thead>
                                    <tr>
                                        <th class="col-sm">barcode</th>
                                        <th class="col-sm">Dimension</th>
                                        <th class="col-sm">Product</th>
                                        <th class="col-sm">price</th>


                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-md-2 col-md-offset-10">

                                <b>@lang('sale.total_payable'): </b>
                                <input type="hidden" name="final_total" id="final_total_input">
                                <span id="total_payable"> 0 </span>

                            </div>
                        </div>
                    </div>
                @endcomponent

            </div>
        </div>

        {{-- @if ((empty($status) || !in_array($status, ['quotation', 'draft']) || $is_enabled_download_pdf) && $sale_type != 'sales_order')
			@can('sell.payments') --}}
        {{-- @component('components.widget', ['class' => 'box-solid', 'id' => 'payment_body_id', 'title' => __('purchase.add_payment')]) --}}
        {{-- @if ($is_enabled_download_pdf) --}}

        {{-- @endif --}}
        {{-- @if (empty($status) || !in_array($status, ['quotation', 'draft']))
                <div class="payment_row">

                    @include('sell_transaction.partials.payment_row_form', [
                        'row_index' => 0,
                        'show_date' => true,
                    ])
                    <hr> --}}
        {{-- <div class="row">
                        <div class="col-sm-12">
                            <div class="pull-right"><strong>@lang('lang_v1.balance'):</strong> <span class="balance_due">0.00</span>
                            </div>
                        </div>
                    </div> --}}
        {{-- </div>
            @endif
        @endcomponent --}}

        @component('components.widget', ['class' => 'box-solid', 'title' => __('purchase.add_payment')])
            <div id="payment_rows_div">
                @foreach ($payment_lines as $payment_line)
                    @if ($payment_line['is_return'] == 1)
                        @php
                            $change_return = $payment_line;
                        @endphp

                        @continue
                    @endif
                    {{-- {{ dd($payment_lines) }} --}}
                    @if (!empty($payment_line['id']))
                        {!! Form::hidden("payment[$loop->index][payment_id]", $payment_line['id']) !!}
                    @endif

                    @include('sell_transaction.partials.payment_row_form', [
                        'row_index' => $loop->index,
                        'show_date' => true,
                        'payment_line' => $payment_line,
                    ])
                @endforeach
            </div>

            {{-- <div class="col-md-12">
        		<hr>
        		<strong>
        			@lang('lang_v1.change_return'):
        		</strong>
        		<br/>
        		<span class="lead text-bold change_return_span">0</span>
        		{!! Form::hidden("change_return", $change_return['amount'], ['class' => 'form-control change_return input_number', 'required', 'id' => "change_return"]); !!}
        		<!-- <span class="lead text-bold total_quantity">0</span> -->
        		@if (!empty($change_return['id']))
            		<input type="hidden" name="change_return_id" 
            		value="{{$change_return['id']}}">
            	@endif
			</div> --}}
        @endcomponent

        <!-- Submit Button -->
        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-2">
                <button type="submit" id="add_sell_transaction"
                    class="btn btn-primary pull-right">@lang('messages.save')</button>

            </div>
        </div>

        {!! Form::close() !!}

        <div class="modal scan_modal" id="scan_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>

    <!-- /.content -->
    <div class="modal fade register_details_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade close_register_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <!-- quick product modal -->
    <div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>

    {{-- @include('sale_pos.partials.configure_search_modal') --}}
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        @include('contact.create', ['quick_add' => true])
    </div>
@stop

@section('javascript')
    <script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
    {{-- <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script> --}}
    {{-- <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script> --}}
    {{-- <script src="{{ asset('js/shipment.js?v=' . $asset_v) }}"></script> --}}

    <!-- Call restaurant module if defined -->
    @if (in_array('tables', $enabled_modules) ||
        in_array('modifiers', $enabled_modules) ||
        in_array('service_staff', $enabled_modules))
        <script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif
    <script type="text/javascript">
        $(document).ready(function() {

            var pack_id = $('.pack_id').val();

            if (pack_id.length) {

                var myarr = pack_id.split(',');
                myarr.forEach(thefunction);
            }

            function thefunction(item) {
                $.ajax({
                    type: 'GET',
                    cache: false,
                    url: '/sell-transaction/edit-list-the-package',
                    data: {
                        term: item
                    },
                    success: function(response) {
                        var resp = response[0].pack;
                        console.log(response[0].pack);
                        var id = resp.id;
                        var barcode = resp.bar_code;
                        var mode_transport = resp.mode_transport;
                        var customer_tel = resp.customer_tel;
                        var customer_name = resp.customer_name;
                        var commission_agent = resp.commission_agent;
                        var product = resp.product;
                        var price = response[0].price;

                        var lengt = resp.longueur;
                        var length = Number(lengt).toFixed(0)
                        var widt = resp.largeur;
                        var width = Number(widt).toFixed(0);
                        var heigh = resp.hauteur;
                        var height = Number(heigh).toFixed(0);

                        var dimension = "";
                        if (length > 0 && width > 0 && height > 0) {

                            var dimension = '(' + length + 'x' + width + 'x' + height + 'cm)';

                        }

                        var p_product = '';
                        var p_c_name = '';

                        var tr = '<tr class="package_row"  data-id="' + id + '"><td>' + barcode +
                            '</td><td>' + dimension + '<td>' + product + '</td>';
                        tr += '<td ><input type="text" class="price_readonly" value="' + price +
                            '" name="packages[' +
                            id +
                            '][price]" required style="width:50px;" /> </td>';

                        tr +=
                            '<input type="hidden" class="price_total_hidden" name="packages[' +
                            id +
                            '][price_total]" class="package_row_index" value="' + id + '">';
                        tr +=
                            '<td><button type="button" class="btn btn-danger btn-xs move_packages_row">-</button>';
                        tr += '<input type="hidden" name="packages[' + id +
                            '][id]" class="package_row_index" value="' + id + '"></td></tr>';

                        $('#the_package_add_parcel_form_part tbody').append(tr);
                        calculate_total_payable();
                    }
                });
            }

            // $('#add_sell_form').submit(function(event) {
            $('#add_sell_transaction').click(function() {
                var final = $('input#final_total_input').val();
                // alert(final)
                if (final.length == 0) {
                    // alert('hello');
                    toastr.error(LANG.no_package_found);
                }
                //   alert( "Handler for .submit() called." );
                // event.preventDefault();
            });


            $("#customer_id").change(function() {
                // alert('hello');
                $('#the_package_add_parcel_form_part tbody').find('.package_row').each(function() {
                    $(this).closest('tr').fadeOut(300, function() {
                        $(this).remove();
                    });
                });
                $('span#total_payable').text(__currency_trans_from_en(0, false));
                $('input#final_total_input').val('');
                $('#amount').val(__currency_trans_from_en(0, false));
                // $('#my_search_product').val('');
                // $('.cacher').prop('disabled',false);
            });

            $('#status').change(function() {
                if ($(this).val() == 'final') {
                    $('#payment_rows_div').removeClass('hide');
                } else {
                    $('#payment_rows_div').addClass('hide');
                }
            });
            $('.paid_on').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });
            $('.calendar').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });

            $('#shipping_documents').fileinput({
                showUpload: false,
                showPreview: false,
                browseLabel: LANG.file_browse_label,
                removeLabel: LANG.remove,
            });

            $(document).on('change', '#prefer_payment_method', function(e) {
                var default_accounts = $('select#select_location_id').length ?
                    $('select#select_location_id')
                    .find(':selected')
                    .data('default_payment_accounts') : $('#location_id').data('default_payment_accounts');
                var payment_type = $(this).val();
                if (payment_type) {
                    var default_account = default_accounts && default_accounts[payment_type]['account'] ?
                        default_accounts[payment_type]['account'] : '';
                    var account_dropdown = $('select#prefer_payment_account');
                    if (account_dropdown.length && default_accounts) {
                        account_dropdown.val(default_account);
                        account_dropdown.change();
                    }
                }
            });

            function setPreferredPaymentMethodDropdown() {
                var payment_settings = $('#location_id').data('default_payment_accounts');
                payment_settings = payment_settings ? payment_settings : [];
                enabled_payment_types = [];
                for (var key in payment_settings) {
                    if (payment_settings[key] && payment_settings[key]['is_enabled']) {
                        enabled_payment_types.push(key);
                    }
                }
                if (enabled_payment_types.length) {
                    $("#prefer_payment_method > option").each(function() {
                        if (enabled_payment_types.indexOf($(this).val()) != -1) {
                            $(this).removeClass('hide');
                        } else {
                            $(this).addClass('hide');
                        }
                    });
                }
            }

            setPreferredPaymentMethodDropdown();

            $('#is_export').on('change', function() {
                if ($(this).is(':checked')) {
                    $('div.export_div').show();
                } else {
                    $('div.export_div').hide();
                }
            });



            //search_product
            if ($('#my_search_product').length) {
                //Add Product
                $('#my_search_product')
                    .autocomplete({

                        delay: 1000,
                        source: function(request, response) {
                            var customer = $('#customer_id').val();
                            // alert(customer);
                            $.getJSON(

                                // alert(customer),
                                '/sell-transaction/list-the-package', {
                                    term: request.term,
                                    customer: customer
                                },
                                response
                            );
                        },
                        minLength: 2,
                        response: function(event, ui) {
                            if (ui.content.length == 1) {
                                // $(this)
                                //     .data('ui-autocomplete')
                                //     ._trigger('select', 'autocompleteselect', ui);
                                // $(this).autocomplete('close');
                            } else if (ui.content.length == 0) {
                                toastr.error(LANG.no_package_found);
                                $('input#my_search_product').select();
                            }
                        },
                        focus: function(event, ui) {
                            if (ui.item.qty_available <= 0) {
                                return false;
                            }
                        },
                        select: function(event, ui) {
                            var item = ui.item;
                            var id = item.id;
                            var barcode = item.barcode;
                            var mode_transport = item.mode_transport;
                            var customer_tel = item.customer_tel;
                            var customer_name = item.customer_name;
                            var commission_agent = item.commission_agent;
                            var product = item.product;
                            var price = item.price;
                            var dimension = item.dimension;
                            var p_product = '';
                            var p_c_name = '';

                            // if (item.packages.length !== 0) {
                            //     var package = item.packages[0];
                            //     p_product = package.p;
                            //     p_c_name = package.c_name;
                            // }
                            // if (product == null && p_product != null) {
                            //     product = p_product;
                            //  }
                            var tr = '<tr class="package_row"  data-id="' + id + '"><td>' + barcode +
                                '</td><td>' + dimension + '<td>' + product + '</td>';
                            tr += '<td ><input type="text" class="price_readonly" value="' + price +
                                '"  name="packages[' +
                                id +
                                '][price]" required style="width:50px;" /> </td>';
                            // tr += '<td ><input type="text" class="qte" name="packages[' + id +
                            //     '][qte]" required style="width:50px;" /> </td>';

                            // tr +=
                            //     '<td > <p class="price_total">0.00</p> <input type="hidden" class="price_total_hidden" name="packages[' +
                            //     id +
                            //     '][price_total]" class="package_row_index" value="' + id + '"></td>';
                            tr +=
                                '<input type="hidden" class="price_total_hidden" name="packages[' +
                                id +
                                '][price_total]" class="package_row_index" value="' + id + '">';
                            tr +=
                                '<td><button type="button" class="btn btn-danger btn-xs move_packages_row">-</button>';
                            tr += '<input type="hidden" name="packages[' + id +
                                '][id]" class="package_row_index" value="' + id + '"></td></tr>';

                            $('#the_package_add_parcel_form_part tbody').append(tr);
                            calculate_total_payable();

                        },
                    })
                    .autocomplete('instance')._renderItem = function(ul, item) {

                        var row_lists = [];
                        $('#the_package_add_parcel_form_part tbody').find('.package_row').each(function() {
                            row_lists.push($(this).data('id'));
                        });

                        if (row_lists.includes(item.id)) { // DISABLED
                            var string = '<li class="ui-state-disabled"><div>' + item.displayLine + '</div></li>';
                            return $(string).appendTo(ul);
                        } else {
                            var string = '<div>' + item.displayLine;
                            string += '</div>';

                            return $('<li>')
                                .append(string)
                                .appendTo(ul);
                        }
                    };
            }
            $('#the_package_add_parcel_form_part').on('click', '.move_packages_row', function() {
                $(this).closest('tr').fadeOut(300, function() {
                    $(this).remove();
                    calculate_total_payable();
                });

            });

            $('#the_package_add_parcel_form_part').on('change', '.price_readonly', function() {
                calculate_total_payable();
                // var price = $(this).val();
                // $(".payment_amount").val(price);

                // calculate_balance_due2();
                // var price = $(this).val();
                // var prix = $('#price_readonly').val();
                // var val = $(this).val();
                // // alert(val);
                // if (!$(this).val()) {
                //     $('.price_total').text('0.00');
                //     $('.price_total_hidden').val(0);


                // } else {


                //     result = quantite * prix;
                //     if (!isNaN(result)) {

                //         $('.price_total').text(result);
                //         $('.price_total_hidden').val(result);
                //     } else {

                //         $('.price_total').text(result);
                //         $('.price_total_hidden').val(0);
                //     }
                // }
            });
            // $('##payment_rows_div').on('change', '.payment-amount', function() {
            //     alert('hello');
            //     calculate_total_payable();
            //     calculate_balance_due2();

            // });
            $(".payment-amount").change(function() {
                // alert('hrllo');
                // calculate_balance_due2();
                // $('.cacher').prop('disabled',false);
            });

            // $('#the_package_add_parcel_form_part').click(function() {
            //     alert('helllo');
            // });
            function calculate_total_payable() {
                var total_paying = 0;
                $('#the_package_add_parcel_form_part')
                    .find('.price_readonly')
                    .each(function() {
                        if (parseFloat($(this).val())) {
                            total_paying += __read_number($(this));
                        }
                    });
                // var bal_due = total_payable - total_paying;
                // var change_return = 0;
                $('span#total_payable').text(__currency_trans_from_en(total_paying, false));
                $('input#final_total_input').val(total_paying);
                $('#amount').val(__currency_trans_from_en(total_paying, false));
                // $('#my_search_product').val(__currency_trans_from_en(total_paying, false));
                // alert(total_paying);
                // __read_number($('input#final_total_input'))
            }

            function calculate_balance_due2() {
                var total_payable = __read_number($('#final_total_input'));
                var total_paying = 0;
                $('#payment_rows_div')
                    .find('.payment-amount')
                    .each(function() {
                        if (parseFloat($(this).val())) {
                            total_paying += __read_number($(this));
                        }
                    });
                var bal_due = total_payable - total_paying;
                var change_return = 0;

                //change_return
                if (bal_due < 0 || Math.abs(bal_due) < 0.05) {
                    __write_number($('input#change_return'), bal_due * -1);
                    $('span.change_return_span').text(__currency_trans_from_en(bal_due * -1, true));
                    change_return = bal_due * -1;
                    bal_due = 0;
                } else {
                    __write_number($('input#change_return'), 0);
                    $('span.change_return_span').text(__currency_trans_from_en(0, true));
                    change_return = 0;
                }

                __write_number($('input#total_paying_input'), total_paying);
                $('span.total_paying').text(__currency_trans_from_en(total_paying, true));

                __write_number($('input#in_balance_due'), bal_due);
                $('span.balance_due').text(__currency_trans_from_en(bal_due, true));

                __highlight(bal_due * -1, $('span.balance_due'));
                __highlight(change_return * -1, $('span.change_return_span'));
            }


        });
    </script>
@endsection
