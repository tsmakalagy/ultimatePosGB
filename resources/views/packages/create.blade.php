@extends('layouts.app')
@section('title', __('lang_v1.add_package'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('lang_v1.add_package')</h1>
        <!-- <ol class="breadcrumb">
                                                                                                                                <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                                                                                                                                <li class="active">Here</li>
                                                                                                                            </ol> -->
    </section>

    <!-- Main content -->
    <section class="content">

        {!! Form::open([
            'url' => action('PackageController@store'),
            'method' => 'post',
            'id' => 'package_add_form',
            'files' => true,
            'enctype' => 'multipart/form-data',
        ]) !!}
        <div class="row d-flex justify-content-center">
            <div class="container-fluid">
                <div class="col-md-4 col-md-offset-4">
                    <div class="container-fluid">
                        <div class="form-group">
                            {!! Form::label('bar_code', __('lang_v1.bar_code') . ':') !!}
                            {!! Form::text('bar_code', $barcode, ['class' => 'form-control', 'rows' => 3]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-solid'])
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
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
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('product', __('lang_v1.product_list') . ' (' . __('lang_v1.qty') . '):') !!}
                                    {!! Form::textarea('product', $value = null, ['class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('longueur', __('lang_v1.length') . ':') !!}
                                    {!! Form::text('longueur', $value = null, ['class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('largeur', __('lang_v1.width') . ':') !!}
                                    {!! Form::text('largeur', $value = null, ['class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('hauteur', __('lang_v1.height') . ':') !!}
                                    {!! Form::text('hauteur', $value = null, ['class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('weight', __('lang_v1.weight') . ':') !!}
                                    {!! Form::text('weight', $value = null, ['class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('volume', __('lang_v1.volume') . ':') !!}
                                    {!! Form::text('volume', $value = null, ['class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    {!! Form::label('commission_agent', __('lang_v1.commission_agent') . ':') !!}
                                    {!! Form::select('commission_agent', $users, null, [
                                        'class' => 'form-control select2',
                                        'id' => 'product_locations',
                                        'placeholder' => __('messages.please_select'),
                                    ]) !!}
                                    {{-- {!! Form::select('packages[]', $package,null,['class' => 'form-control select2', 'multiple', 'id' => 'product_locations','required']); !!} --}}

                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    {!! Form::label('mode_transport', __('lang_v1.mode_transport') . ':') !!}
                                    {!! Form::select('mode_transport', [0 => 'bateau', 1 => 'avion'], null, [
                                        'class' => 'form-control select2',
                                        'id' => 'product_locations',
                                        'placeholder' => __('messages.please_select'),
                                    ]) !!}
                                    {{-- {!! Form::select('packages[]', $package,null,['class' => 'form-control select2', 'multiple', 'id' => 'product_locations','required']); !!} --}}

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('other_field1', __('lang_v1.other_field1') . ':') !!}
                                    {!! Form::text('other_field1', $value = null, ['class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('other_field2', __('lang_v1.other_field2') . ':') !!}
                                    {!! Form::text('other_field2', $value = null, ['class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('image', __('lang_v1.product_image') . ':') !!}
                                    {{-- {!! Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); !!} --}}

                                    <input type="file" id="upload_ima" name="images[]" accept="image/*" multiple>
                                    <!-- Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); -->
                                    <small>
                                        <p class="help-block">@lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000])
                                            <br> @lang('lang_v1.aspect_ratio_should_be_1_1')
                                        </p>
                                    </small>
                                </div>
                            </div>

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
        </div>
        </div>

        {{-- @include('layouts.partials.module_form_part') --}}

        {!! Form::close() !!}

    </section>
    <!-- /.content -->
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        @include('contact.create', ['quick_add' => true])
    </div>

@stop

@section('javascript')
    @php $asset_v = env('APP_VERSION'); @endphp
    {{-- <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script> --}}


    {{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css"></script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script> --}}
    <script src="{{ asset('js/shipment.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            $('#bar_code').prop('readonly', true);
            // // $('#status').prop('readonly', true);
            //   $('#status').attr('disabled',true);
            $('#status').prop('disabled', true);
            $('form').bind('submit', function() {
                $('#status').prop('disabled', false);
            });

        });
    </script>
@endsection
