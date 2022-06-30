@extends('layouts.app')

@section('title', __('lang_v1.edit_the_package'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('lang_v1.edit_the_package')
        </h1>
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
            <li class="active">Here</li>
        </ol> -->
    </section>

    <!-- Main content -->
    <section class="content">
     {!! Form::open(['action' =>['ThePackageController@update', $the_package->id],'files' => true,'enctype' =>'multipart/form-data']) !!}
     @component('components.widget', ['class' => 'box-solid'])                   
     <div class="container-fluid">


        <div class="row">
                
            <div class="col-md-6 ">
                <div class="form-group">
                    {!! Form::label('mode_transport', __('lang_v1.mode_transport') . ':') !!}
                    {!! Form::select('mode_transport', [0 =>'bateau',1 =>'avion'],$package->mode_transport,['class' => 'form-control select2',  'id' => 'product_locations','placeholder' => __('messages.please_select'),'required']); !!}
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
                    {!! Form::text('date_envoi', $package->mode_transport, ['class' => 'form-control calendar']); !!}
                </div>
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
                                {!! Form::submit(__('messages.update') , ['class' => 'btn btn-primary pull-right'] ) !!}
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
{{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css"></script> --}}
@section('javascript')
 @php $asset_v = env('APP_VERSION'); @endphp
  <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
    
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
           $('.list').remove();
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

                $('#the_package_add_parcel_form_part').on('click', '.move_packages_row', function () {
                    $(this).closest('tr').fadeOut(300, function() { $(this).remove(); });
                });
          
    
    });
    
    </script>
    @endsection