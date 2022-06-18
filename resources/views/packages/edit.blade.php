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
        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-solid'])
                    <div class="container-fluid">
                        {!! Form::open(['action' =>['PackageController@update', $package->id],'files' => true,'enctype' =>'multipart/form-data']) !!}
 
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('product', __('lang_v1.product_name') . '*:') !!}
                                {!! Form::text('product', $package->product, ['class' => 'form-control', 'rows' => 3,'required']); !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('client', __('lang_v1.customer') . ':') !!}
                                {!! Form::select('client',  $contact, !empty($package->contact_id) ? $package->contact_id : null ,['class' => 'form-control select2', 'placeholder' => __('messages.please_select'),'required']); !!}
                                
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('volume', __('lang_v1.volume') . ':') !!}
                                {!! Form::text('volume', $package->volume, ['class' => 'form-control', 'rows' => 3]); !!}
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
                                {!! Form::label('status', __('lang_v1.status') . ':') !!}
                                {{-- {!! Form::text('status', $package->status, ['class' => 'form-control', 'rows' => 3]); !!} --}}
                                {!! Form::select('status', [0 =>'entrant',1 =>'sortant'], !empty($package->status) ? $package->status : null ,['class' => 'form-control select2', 'placeholder' => __('messages.please_select'),'required']); !!}
                                
                            </div>
                        </div>
                     
                      
                 
                      
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('image', __('lang_v1.image') . ':') !!}
                              
                                <input type="file" id="upload_ima" value="{{$package->image}}" name="image">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('bar_code', __('lang_v1.bar_code') . ':') !!}
                                {!! Form::text('bar_code',$package->bar_code, ['class' => 'form-control', 'rows' => 3]); !!}
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
  
                        

                        <!-- Submit Button -->
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-2">
                                {!! Form::submit(__('messages.update') , ['class' => 'btn btn-primary pull-right'] ) !!}
                            </div>
                        </div>

                        {!! Form::close()  !!}

                        <div>

                            @endcomponent
                        </div>
                    </div>


    </section>
    <!-- /.content -->

@endsection
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css"></script>

<script type="text/javascript">
    $(document).ready(function(){
    

    
    });
    
    </script>