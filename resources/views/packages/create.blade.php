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

        {!! Form::open(['url' => action('PackageController@store'), 'method' => 'post', 'id' => 'package_add_form', 'files' => true,'enctype' =>'multipart/form-data']); !!}
            <div class="row d-flex justify-content-center">   
                 <div class="container-fluid">     
                    <div class="col-md-8 ">
                    <div class="container-fluid">
                    <div class="form-group">
                                {!! Form::label('bar_code', __('lang_v1.bar_code') . ':') !!}
                                {!! Form::text('bar_code', $barcode, ['class' => 'form-control', 'rows' => 3]); !!}
                     </div>
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
                                {!! Form::label('customer_name', __('lang_v1.customer') . '*:') !!}
                                {!! Form::text('customer_name', $value= null, ['class' => 'form-control', 'rows' => 3,'required']); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('customer_tel', __('lang_v1.mobile') . ':') !!}
                                {!! Form::text('customer_tel', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('product', __('lang_v1.product_name') . ':') !!}
                                {!! Form::text('product', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>

                        
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('status', __('lang_v1.status') . ':') !!}
                                {!! Form::select('status', [0=>'entrant',1=>'sortant'],0,['class' => 'form-control select2', 'placeholder' => __('messages.please_select'),'required']); !!}
                               
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('weight', __('lang_v1.weight') . ':') !!}
                                {!! Form::text('weight', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                             
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('longeur', __('lang_v1.longeur') . ':') !!}
                                {!! Form::text('longeur', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                             
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('largeur', __('lang_v1.largeur') . ':') !!}
                                {!! Form::text('largeur', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                             
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('hauteur', __('lang_v1.hauteur') . ':') !!}
                                {!! Form::text('hauteur', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
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
                              <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
                            </div>
                           
                          </div>

                           <!-- Submit Button -->
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-2">
                                <button type="submit" id="submit-sell" class="btn btn-primary pull-right">@lang('messages.save')</button>

                            </div>
                        </div>
     
                    </div>
                    @endcomponent
                </div>
            </div>

                    {{-- @include('layouts.partials.module_form_part') --}}
             
           {!! Form::close()  !!}

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
$(document).ready(function(){

    $('#bar_code').prop('readonly', true);
    // // $('#status').prop('readonly', true);
    //   $('#status').attr('disabled',true);
    		$('#status').prop('disabled',true);
			$('form').bind('submit', function () {
				$('#status').prop('disabled', false);
    });

    });

</script>
@endsection












