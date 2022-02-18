@extends('layouts.app')

@section('title', __('shipper.edit_shipper'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('shipper.edit_shipper')
        </h1>
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
            <li class="active">Here</li>
        </ol> -->
    </section>

    <!-- Main content -->
    <section class="content">
    
        <div class="row">
                
        
<p class="theOption">blallall</p>

            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-solid'])
                    <div class="container-fluid">
                        {!! Form::open(['url' => action('ShipperController@testAddress'), 'method' => 'get']) !!}
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('shipper_name', __('shipper.name') . ':*') !!}
                                {!! Form::text('shipper_name', null, ['class' => 'form-control theOption', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                
                                {!! Form::label('shipper_type_id', __('shipper.type') . ':*') !!}
                                {!! Form::select('shipper_type_id',[1=>'CENTRE-VILLES',2=>'PROVINCES'],null,  ['class' => 'form-control','placeholder' => __('messages.please_select')]); !!}		        

                                
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                
                                {!! Form::label('shipper_test', __('shipper.test') . ':*') !!}
                                <select name="shipper_test" id="shipper_test" class="form-control" >
                                    <option value=""selected>@lang('messages.please_select')</option>
                                    </select> 
                            </div>
                        </div>
                        <select name="" id="example"></select>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('tel', __('shipper.tel') . ':*') !!}
                                {!! Form::text('tel',null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
               

                        <!-- Submit Button -->
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-2">
                                {!! Form::submit(__('messages.add') , ['class' => 'btn btn-primary pull-right'] ) !!}
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


@section('javascript')
    <script type="text/javascript">
        $(document).ready(function () {
            $("#shipper_type_id").change(function() {
       var selectedbrand = $(this).val();
 
$.ajax({
        type: 'POST',
        url: '/test_shipping_address',
        data: {selectedbrand:selectedbrand},
        success: function(response) {
           
        $("option").remove(".theOption");
        var text = "";
        var i;
        for (i = 0; i < response.length; i++) {
          
           text+='<option value="'+response[i].id+'" class="theOption">'+response[i].nom+'</option>';
    
        } 
         $("#shipper_test").append(text);
        
        }
    });
 
    }); 
          

        });
    </script>
 
@endsection



