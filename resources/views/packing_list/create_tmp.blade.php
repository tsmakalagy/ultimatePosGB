@extends('layouts.app')
@section('title', __('lang_v1.add_packing_list'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('lang_v1.add_packing_list')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        {!! Form::open(['url' => action('packingListController@store'), 'method' => 'post', 'id' => 'package_add_form', 'files' => true,'enctype' =>'multipart/form-data']); !!}
        @component('components.widget', ['class' => 'box-solid'])
            <div class="container-fluid">


                <div class="row">

                    <div class="col-md-6 ">
                        <div class="form-group">
                            {!! Form::label('mode_transport', __('lang_v1.mode_transport') . ':') !!}
                            {!! Form::select('mode_transport', [0 =>'bateau',1 =>'avion'],null,['class' => 'form-control select2',  'id' => 'product_locations','placeholder' => __('messages.please_select'),'required']); !!}
                            {{-- {!! Form::select('packages[]', $package,null,['class' => 'form-control select2', 'multiple', 'id' => 'product_locations','required']); !!} --}}

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('date_envoi', __('lang_v1.date_envoi') . ':') !!}
                            <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                                {!! Form::text('date_envoi', $carbon, ['class' => 'form-control calendar']); !!}
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        <div class="form-group">
                        {!! Form::text('my_search_product', null, ['class' => 'form-control mousetrap', 'id' => 'my_search_product', 'placeholder' => __('lang_v1.search_product_placeholder')

                                ]); !!}
                        </div>
                    </div>
                </div>
{{--                <div class="row">--}}
{{--                    <div class="col-sm-10 col-sm-offset-1">--}}
{{--                        <div class="form-group">--}}
{{--                            <div class="input-group">--}}
{{--                                <div class="input-group-btn">--}}
{{--                                    <button type="button" class="btn btn-default bg-white btn-flat" data-toggle="modal"--}}
{{--                                            data-target="#configure_search_modal"--}}
{{--                                            title="{{__('lang_v1.configure_product_search')}}"><i--}}
{{--                                                class="fas fa-search-plus"></i></button>--}}
{{--                                </div>--}}
{{--                                {!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder')--}}

{{--                                ]); !!}--}}

{{--                                <span class="input-group-btn">--}}
{{--								<button type="button" class="btn btn-default bg-white btn-flat pos_add_quick_product"--}}
{{--                                        data-href="{{action('ProductController@quickAdd')}}"--}}
{{--                                        data-container=".quick_add_product_modal"><i--}}
{{--                                            class="fa fa-plus-circle text-primary fa-lg"></i></button>--}}
{{--							</span>--}}

{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="product_row col-sm-10 col-sm-offset-1"></div>--}}

                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-bordered add-parcel-table table-condensed"
                               id="the_package_add_parcel_form_part">
                            <thead>
                            <tr>
                                <th class="col-sm">SKU</th>
                                <th class="col-sm">Dimension</th>
                                <th class="col-sm">Product</th>
                                <th class="col-sm">Client</th>
                                <th class="col-sm">qte</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            {{--                            @if($action == 'add')--}}
                            {{--                                @include('product.partials.product_variation_row', ['row_index' => 0])--}}
                            {{--                            @else--}}

                            {{--                                @forelse ($product_variations as $product_variation)--}}
                            {{--                                    @include('product.partials.edit_product_variation_row', ['row_index' => $action == 'edit' ? $product_variation->id : $loop->index])--}}
                            {{--                                @empty--}}
                            {{--                                    @include('product.partials.product_variation_row', ['row_index' => 0])--}}
                            {{--                                @endforelse--}}

                            {{--                            @endif--}}

                            </tbody>
                        </table>
                    </div>
                </div>


                <!-- Submit Button -->
                <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-2">
                        <button type="submit" id="submit-sell"
                                class="btn btn-primary pull-right">@lang('messages.save')</button>

                    </div>
                </div>


            </div>
        @endcomponent


        {!! Form::close()  !!}

        <div class="modal scan_modal" id="scan_modal" role="dialog"
             aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
    <!-- /.content -->

@endsection

@section('javascript')
    @php $asset_v = env('APP_VERSION'); @endphp
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>

    {{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css"></script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script> --}}

    <script type="text/javascript">
        $(document).ready(function () {
            function delay(callback, ms) {
                var timer = 0;
                return function () {
                    var context = this, args = arguments;
                    clearTimeout(timer);
                    timer = setTimeout(function () {
                        callback.apply(context, args);
                    }, ms || 0);
                };
            }


            if ($('#my_search_product').length) {
                //Add Product
                $('#my_search_product')
                    .autocomplete({
                        delay: 1000,
                        source: function(request, response) {
                            $.getJSON(
                                '/packing-list/list-the-package',
                                {
                                    term: request.term
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
                            var sku = item.sku;
                            var product = item.product;
                            var dimension = item.dimension;
                            var p_product = '';
                            var p_c_name = '';

                            if (item.packages.length !== 0) {
                                var package = item.packages[0];
                                p_product = package.p;
                                p_c_name = package.c_name;
                            }
                            if (product == null && p_product != null) {
                                product = p_product;
                            }
                            var tr = '<tr class="package_row"  data-id="' + id + '"><td>' + sku + '</td><td>' + dimension + '<td>' + product + '</td><td>' + p_c_name + '</td>';
                            tr += '<td ><input type="text" name="packages[' +id+ '][qte]" required style="width:50px;" /> </td>';
                            tr +='<td><button type="button" class="btn btn-danger btn-xs move_packages_row">-</button>';
                            tr +='<input type="hidden" name="packages[' +id+ '][id]" class="package_row_index" value="' +id+ '"></td></tr>';

                            $('#the_package_add_parcel_form_part tbody').append(tr);

                            // if (ui.item.enable_stock != 1 || ui.item.qty_available > 0 || is_overselling_allowed) {
                            //     $(this).val(null);
                            //
                            //     //Pre select lot number only if the searched term is same as the lot number
                            //     var purchase_line_id = ui.item.purchase_line_id && searched_term == ui.item.lot_number ? ui.item.purchase_line_id : null;
                            //     pos_product_row(ui.item.variation_id, purchase_line_id);
                            // } else {
                            //     alert(LANG.out_of_stock);
                            // }
                        },
                    })
                    .autocomplete('instance')._renderItem = function(ul, item) {
                        var row_lists = [];
                        $('#the_package_add_parcel_form_part tbody').find('.package_row').each( function() {
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


            // Example usage:

            $('#search_product').keyup(delay(function (e) {


                    // $('.product_row').closest('ul').fadeOut(300, function() { $('.product_row').remove(); });
                    $('.list').remove();
                    if (this.value.length > 2) {
                        var val = $(this).val();
                        // alert(val);
                        $.ajax({
                            type: 'GET',
                            cache: false,
                            url: '/packing-list/get-the-package-row',
                            data: {val: val},
                            success: function (response) {
                                console.log(response);
                                // $('#my_modal .close').click();
                                $('.product_row').append(response);
                            }
                        });
                        // $(this).val('');
                    }
                },
                1000));


            $('.calendar').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });
            $('.product_row').on('click', '.remove_package_row', function () {
                var id = $(this).find(':hidden').val();
                alert(id);

                // alert('hello');
                $.ajax({
                    type: 'GET',
                    cache: false,
                    url: '/packing-list/get-package-row',
                    data: {id: id},
                    success: function (response) {
                        console.log(response);
                        // $('#my_modal .close').click();
                        $('#the_package_add_parcel_form_part tbody').append(response);
                    }
                });
                $('#search_product').val('');
                $(this).closest('ul').fadeOut(300, function () {
                    $(this).remove();
                });
            });

            $('#the_package_add_parcel_form_part').on('click', '.move_packages_row', function () {
                $(this).closest('tr').fadeOut(300, function () {
                    $(this).remove();
                });

            });


        });

    </script>
@endsection












