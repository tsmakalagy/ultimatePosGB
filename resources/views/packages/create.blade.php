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
                                        
                    
                              <div class="row">  
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('product', __('lang_v1.product_name') . '*:') !!}
                                {!! Form::text('product', $value= null, ['class' => 'form-control', 'rows' => 3,'required']); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('client', __('lang_v1.customer') . ':') !!}
                                {!! Form::select('client', $contact,null,['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
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
                                {!! Form::label('volume', __('lang_v1.volume') . '*:') !!}
                                {!! Form::text('volume', $value= null, ['class' => 'form-control', 'rows' => 3,'required']); !!}
                            </div>
                        </div>
                      
                     
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('status', __('lang_v1.status') . ':') !!}
                                {!! Form::select('status', [0=>'entrant',1=>'sortant'],null,['class' => 'form-control select2', 'placeholder' => __('messages.please_select'),'required']); !!}
                               
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                              {!! Form::label('image', __('lang_v1.product_image') . ':') !!}
                              {{-- {!! Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); !!} --}}
                              
                             <input type="file" id="upload_ima" name="image">
                            <!-- Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); -->
                              <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
                            </div>
                           
                          </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('bar_code', __('lang_v1.bar_code') . ':') !!}
                                {!! Form::text('bar_code', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
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
                     
        
     
                    </div>
                    @include('layouts.partials.module_form_part')
             
                

                  <button type="submit"  class="btn btn-primary ">@lang('messages.save')</button>
         

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



});

</script>
@endsection












