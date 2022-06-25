@extends('layouts.app')

@section('title', __('lang_v1.edit_package'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('lang_v1.edit_package')
        </h1>
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
            <li class="active">Here</li>
        </ol> -->
    </section>

    <!-- Main content -->
    <section class="content">
     {!! Form::open(['action' =>['PackageController@update', $package->id],'files' => true,'enctype' =>'multipart/form-data']) !!}
 

                    <div class="row">   
                     <div class="container-fluid">     
                        <div class="col-md-8 ">    
                            <div class="form-group">
                                {!! Form::label('bar_code', __('lang_v1.bar_code') . ':') !!}
                                {!! Form::text('bar_code', $package->bar_code, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                    </div>
                    </div>  
        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-solid'])
                    <div class="container-fluid">

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('customer_name', __('lang_v1.customer') . ':') !!}
                                {!! Form::text('customer_name', $package->customer_name, ['class' => 'form-control', 'rows' => 3]); !!}
                                
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('customer_tel', __('lang_v1.tel') . ':') !!}
                                {!! Form::text('customer_tel', $package->customer_tel, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>

                            <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('product', __('lang_v1.product_name') . '*:') !!}
                                {!! Form::text('product', $package->product, ['class' => 'form-control', 'rows' => 3,'required']); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('status', __('lang_v1.status') . ':') !!}
                                {{-- {!! Form::text('status', $package->status, ['class' => 'form-control', 'rows' => 3]); !!} --}}
                                {!! Form::select('status', [0 =>'entrant',1 =>'sortant'], 0 ,['class' => 'form-control select2', 'placeholder' => __('messages.please_select'),'required']); !!}
                                
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('weight', __('lang_v1.weight') . ':') !!}
                                {!! Form::text('weight', $package->weight, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('longeur', __('lang_v1.longeur') . ':') !!}
                                {!! Form::text('longeur', $package->longeur, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('largeur', __('lang_v1.largeur') . ':') !!}
                                {!! Form::text('largeur', $package->largeur, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('hauteur', __('lang_v1.hauteur') . ':') !!}
                                {!! Form::text('hauteur', $package->hauteur, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>            
  
                          <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('other_field1', __('lang_v1.other_field1') . ':') !!}
                                {!! Form::text('other_field1', $package->other_field1, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('other_field2', __('lang_v1.other_field2') . ':') !!}
                                {!! Form::text('other_field2', $package->other_field2, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                               
                                {!! Form::label('image', __('lang_v1.product_image') . ':') !!}
                               <input type="file" id="upload_ima" name="images[]" accept="image/*" multiple>
                              <!-- Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); -->
                                <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
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
                        </div>
                    </div>
                     {!! Form::close()  !!}


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
    
        $('#bar_code').prop('readonly', true);
        $('#status').prop('disabled',true);
			$('form').bind('submit', function () {
				$('#status').prop('disabled', false);
    });
    });
    
    </script>
    @endsection