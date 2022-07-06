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
        {!! Form::open(['action' => ['ThePackageController@update', $the_package->id], 'files' => true, 'enctype' => 'multipart/form-data']) !!}
        @component('components.widget', ['class' => 'box-solid'])
            <div class="container-fluid">



                <div class="row">

                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('product', __('lang_v1.product_list') . '*:') !!}
                            {!! Form::textarea('product', $the_package->product, ['class' => 'form-control', 'rows' => 3]) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('longueur', __('lang_v1.length') . ':') !!}
                            {!! Form::text('longueur', $the_package->longueur, ['class' => 'form-control', 'rows' => 3]) !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('largeur', __('lang_v1.width') . ':') !!}
                            {!! Form::text('largeur', $the_package->largeur, ['class' => 'form-control', 'rows' => 3]) !!}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('hauteur', __('lang_v1.height') . ':') !!}
                            {!! Form::text('hauteur', $the_package->hauteur, ['class' => 'form-control', 'rows' => 3]) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('weight', __('lang_v1.weight') . ':') !!}
                            {!! Form::text('weight', $the_package->weight, ['class' => 'form-control', 'rows' => 3]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('volume', __('lang_v1.volume') . ':') !!}
                            {!! Form::text('volume', $the_package->volume, ['class' => 'form-control', 'rows' => 3]) !!}
                        </div>
                    </div>
                </div>
                {!! Form::hidden('mybarcode', $impl, ['class' => 'form-control mybarcode', 'rows' => 3]) !!}


                {!! Form::hidden('bar_code', $the_package->bar_code, ['class' => 'form-control', 'rows' => 3]) !!}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('other_field1', __('lang_v1.other_field1') . ':') !!}
                            {!! Form::text('other_field1', $the_package->other_field1, ['class' => 'form-control', 'rows' => 3]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('other_field2', __('lang_v1.other_field2') . ':') !!}
                            {!! Form::text('other_field2', $the_package->other_field2, ['class' => 'form-control', 'rows' => 3]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">

                        {!! Form::label('image', __('lang_v1.product_image') . ':') !!}
                        <input type="file" id="upload_ima" name="images[]" accept="image/*" multiple>
                        <!-- Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); -->
                        <small>
                            <p class="help-block">@lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p>
                        </small>
                    </div>
                </div>

                <div class="col-sm-12">
                    <h4>@lang('lang_v1.add_the_package'):
                        <button type="button" class="btn btn-primary my-btn-modal" id="add_parcel" data-action="add"
                            data-href="{{ action('ThePackageController@scan') }}" data-container=".scan_modal">+
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
                                {{-- @if ($action == 'add') --}}
                                {{-- @include('product.partials.product_variation_row', ['row_index' => 0]) --}}
                                {{-- @else --}}

                                {{-- @forelse ($product_variations as $product_variation) --}}
                                {{-- @include('product.partials.edit_product_variation_row', ['row_index' => $action == 'edit' ? $product_variation->id : $loop->index]) --}}
                                {{-- @empty --}}
                                {{-- @include('product.partials.product_variation_row', ['row_index' => 0]) --}}
                                {{-- @endforelse --}}

                                {{-- @endif --}}

                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-2">
                        {!! Form::submit(__('messages.update'), ['class' => 'btn btn-primary pull-right']) !!}
                    </div>
                </div>

            </div>
        @endcomponent
        {!! Form::close() !!}

        <div class="modal scan_modal" id="scan_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
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
        $(document).ready(function() {

            var mybarcode = $('.mybarcode').val();

            if (mybarcode.length) {

                var myarr = mybarcode.split(',');
                myarr.forEach(thefunction);
            }

            function thefunction(item) {
                $.ajax({
                    type: 'GET',
                    cache: false,
                    url: '/the-package/get-package-row',
                    data: {
                        barcode: item
                    },
                    success: function(response) {
                        $('#my_modal .close').click();
                        $('#the_package_add_parcel_form_part tbody').append(response);
                    }
                });
            }

            $('#bar_code').prop('readonly', true);
            $('#status').prop('disabled', true);
            $('form').bind('submit', function() {
                $('#status').prop('disabled', false);
            });

            $(document).on('click', '.my-btn-modal', function(e) {
                $('#the_package_add_parcel_form_part').on('click', '.remove_package_row', function() {
                    $(this).closest('tr').fadeOut(300, function() {
                        $(this).remove();
                    });
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
