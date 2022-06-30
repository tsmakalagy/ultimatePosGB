@extends('layouts.app')
@section('title', __('lang_v1.new_box'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('lang_v1.new_box')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        {!! Form::open(['url' => action('packingListController@store'), 'method' => 'post', 'id' => 'package_add_form', 'files' => true,'enctype' =>'multipart/form-data']); !!}
        @component('components.widget', ['class' => 'box-solid'])
            <div class="container-fluid">
    
              
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('customer_name', __('lang_v1.customer') . ':') !!}
                            {!! Form::text('customer_name', $value= null, ['class' => 'form-control', 'rows' => 3,'required']); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('customer_tel', __('lang_v1.customer_tel') . ':') !!}
                            {!! Form::text('customer_tel', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('longueur', __('lang_v1.length') . ':') !!}
                            {!! Form::text('longueur', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('largeur', __('lang_v1.width') . ':') !!}
                            {!! Form::text('largeur', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('hauteur', __('lang_v1.height') . ':') !!}
                            {!! Form::text('hauteur', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('weight', __('lang_v1.weight') . ':') !!}
                            {!! Form::text('weight', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('volume', __('lang_v1.volume') . ':') !!}
                            {!! Form::text('volume', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                        </div>
                    </div>
                </div>
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
                        {!! Form::label('shipping_date', __('lang_v1.shipping_date') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('shipping_date', $carbon, ['class' => 'form-control calendar']); !!}
                        </div>
                    </div>
                </div>
            </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('other_field1', __('lang_v1.other_field1') . ':') !!}
                            {!! Form::text('other_field1', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('other_field2', __('lang_v1.other_field2') . ':') !!}
                            {!! Form::text('other_field2', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('image', __('lang_v1.image') . ':') !!}
                            {{-- {!! Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); !!} --}}

                            <input type="file" id="upload_ima" name="images[]" accept="image/*" multiple>
                            <!-- Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); -->
                            <small>
                                <p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                                    <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
                        </div>

                    </div>
                </div>

                <div class="row">
                <div class="col-sm-10 col-sm-offset-1">
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-btn">
								<button type="button" class="btn btn-default bg-white btn-flat" data-toggle="modal" data-target="#configure_search_modal" title="{{__('lang_v1.configure_product_search')}}"><i class="fas fa-search-plus"></i></button>
							</div>
							{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder')
							
							]); !!}
                            
							<span class="input-group-btn">
								<button type="button" class="btn btn-default bg-white btn-flat pos_add_quick_product" data-href="{{action('ProductController@quickAdd')}}" data-container=".quick_add_product_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
							</span>
                           
						</div>
					</div>
				</div>
            </div>
                <div class="product_row col-sm-10 col-sm-offset-1"></div>
           
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-bordered add-parcel-table table-condensed"
                               id="the_package_add_parcel_form_part">
                            <thead>
                                <tr>
                                    <th class="col-sm">@lang('lang_v1.barcode')</th>
                   
                                    <th class="col-sm">@lang('lang_v1.length')</th>
                                    <th class="col-sm">@lang('lang_v1.width')</th>
                                    <th class="col-sm">@lang('lang_v1.height')</th>
                                    <th class="col-sm">@lang('lang_v1.volume')</th>
                                    <th class="col-sm">@lang('lang_v1.weight')</th>
                                    <th class="col-sm">@lang('qte')</th>
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
        return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
            callback.apply(context, args);
            }, ms || 0);
        };
        }


        // Example usage:

        $('#search_product').keyup(delay(function (e) {
            
               
            // $('.product_row').closest('ul').fadeOut(300, function() { $('.product_row').remove(); });
           $('div ul').remove();
            if (this.value.length > 2) {
            var val=$(this).val();
        // alert(val);
        $.ajax({
                type: 'GET',
                cache: false,
                url: '/packing-list/get-the-package-row',
                data: { val: val },
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
                
                // alert('hello');
                $.ajax({
                type: 'GET',
                cache: false,
                url: '/packing-list/get-package-row',
                data: { id:id },
                success: function (response) {
                    console.log(response);
                    // $('#my_modal .close').click();
                    $('#the_package_add_parcel_form_part tbody').append(response);
                }
                });
            $('#search_product').val('');
                    $(this).closest('ul').fadeOut(300, function() { $(this).remove(); });
                });


          
    
        });

    </script>
@endsection












