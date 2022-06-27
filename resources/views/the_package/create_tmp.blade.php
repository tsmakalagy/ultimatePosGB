@extends('layouts.app')
@section('title', __('lang_v1.new_box'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('lang_v1.new_box')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        {!! Form::open(['url' => action('ThePackageController@store'), 'method' => 'post', 'id' => 'package_add_form', 'files' => true,'enctype' =>'multipart/form-data']); !!}
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
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('product', __('lang_v1.product_list') . ' ('.__('lang_v1.qty') .'):') !!}
                            {!! Form::textarea('product', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
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



                <div class="col-sm-12">
                    <h4>@lang('lang_v1.add_package'):
                        <button type="button" class="btn btn-primary my-btn-modal" id="add_parcel"
                                data-action="add"
                                data-href="{{ action('ThePackageController@scan') }}"
                                data-container=".scan_modal">+
                        </button>
                    </h4>
                </div>
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-bordered add-parcel-table table-condensed"
                               id="the_package_add_parcel_form_part">
                            <thead>
                                <tr>
                                    <th class="col-sm">@lang('lang_v1.barcode')</th>
                                    <th class="col-sm">@lang('lang_v1.customer')</th>
                                    <th class="col-sm">@lang('lang_v1.customer_tel')</th>
                                    <th class="col-sm">@lang('lang_v1.product')</th>
                                    <th class="col-sm">@lang('lang_v1.length')</th>
                                    <th class="col-sm">@lang('lang_v1.width')</th>
                                    <th class="col-sm">@lang('lang_v1.height')</th>
                                    <th class="col-sm">@lang('lang_v1.volume')</th>
                                    <th class="col-sm">@lang('lang_v1.weight')</th>
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


                <div class="hidden">

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('customer_tel', __('lang_v1.customer_tel') . ':') !!}
                            {!! Form::text('customer_tel', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                            <div class="form-group">
                                {!! Form::label('packages', __('lang_v1.package') . ':') !!}
                                {!! Form::select('packages', $package, null, ['class' => 'form-control select2',  'id' => 'product_locations','placeholder' => __('messages.please_select'),'required']); !!}
                                {{-- {!! Form::select('packages[]', $package,null,['class' => 'form-control select2', 'multiple', 'id' => 'product_locations','required']); !!} --}}

                            </div>
                        </div>
                    </div>
                    {{--
                    <div class="row col-sm-8 " style="min-height: 0">
                        <div class="the_package">
                        </div>
                    </div>
                    --}}

                    <div class="row" style="min-height: 0">
                        <div class="table-responsive">
                            <div class="col-md-8 col-md-offset-2">
                                <table class="table table-condensed table-bordered table-striped table-responsive"
                                       id="pos_table">
                                    <thead>
                                    <tr>
                                        <th class="text-center">
                                            @lang('sale.product')
                                        </th>
                                        <th class="text-center">
                                            @lang('sale.qty')
                                        </th>
                                        <th class="text-center"><i class="fas fa-times" id="close"
                                                                   onclick="Remove()" aria-hidden="true"></i></th>
                                    </tr>
                                    </thead>
                                    <tbody class="my_tbody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('product', __('lang_v1.product_list') . ' ('.__('lang_v1.qty') .'):') !!}
                                {!! Form::textarea('product', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('longeur', __('lang_v1.length') . ':') !!}
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
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('image', __('lang_v1.product_image') . ':') !!}
                            {{-- {!! Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); !!} --}}

                            <input type="file" id="upload_ima" name="images[]" accept="image/*" multiple>
                            <!-- Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); -->
                            <small>
                                <p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                                    <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
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


            $(document).on('click', '.my-btn-modal', function(e) {
                $('#the_package_add_parcel_form_part').on('click', '.remove_package_row', function () {
                    $(this).closest('tr').fadeOut(300, function() { $(this).remove(); });
                });

                e.preventDefault();
                var container = $(this).data('container');

                $.ajax({
                    url: $(this).data('href'),
                    dataType: 'html',
                    success: function(result) {
                        $(container)
                            .html(result)
                            .modal('show');
                    },
                });
            });
        });

    </script>
@endsection












