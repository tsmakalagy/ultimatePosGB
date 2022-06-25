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

        {!! Form::open(['url' => action('ThePackageController@store'), 'method' => 'post', 'id' => 'package_add_form', 'files' => true,'enctype' =>'multipart/form-data']); !!}
            <div class="row d-flex justify-content-center">   
                 <div class="container-fluid">     
                    <div class="col-md-8 ">
                    <div class="container-fluid">
                    <div class="form-group">
                                {!! Form::label('bar_code', __('lang_v1.bar_code') . ':') !!}
                                {!! Form::text('bar_code', $value=null, ['class' => 'form-control', 'rows' => 3]); !!}
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
                                {!! Form::label('packages', __('lang_v1.package') . ':') !!}
                                {!! Form::select('packages', $package,null,['class' => 'form-control select2',  'id' => 'product_locations','placeholder' => __('messages.please_select'),'required']); !!}
                                {{-- {!! Form::select('packages[]', $package,null,['class' => 'form-control select2', 'multiple', 'id' => 'product_locations','required']); !!} --}}
                               
                            </div>
                        </div>
{{-- 
                        <div class="row col-sm-8 " style="min-height: 0">
                            <div class="the_package">
                            </div>
                        </div> --}}






                        <div class="row col-sm-12 pos_product_div" style="min-height: 0">

                     
                            
                            <div class="table-responsive">
                            <table class="table table-condensed table-bordered table-striped table-responsive" id="pos_table">
                                <thead>
                                    <tr>
                                        <th class="text-center">	
                                            @lang('sale.product')
                                        </th>
                                        <th class="text-center">
                                            @lang('sale.qty')
                                        </th>   
                                       
                                        <th class="text-center"><i class="fas fa-times" id="close" onclick="Remove()"  aria-hidden="true"></i></th>
                                    </tr>
                                </thead>
                                <tbody class="my_tbody"></tbody>
                            </table>
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
var j=0;
    $('#product_locations').change(function () {
        var val=$(this).val();
      
     j=j+1;
        $.ajax({
          type: 'GET',
          cache:false,
        url: '/the-package/get-package',
         data: {val:val},
         success: function(response) {

    var name=response.product;           
    var id=response.id;           
    var barcode=response.bar_code;           
              append(name,id,bar_code,j);
         }
        });

         
      
    });
function append(name,id,barcode,j){
    
    var txt1 = '<tr><td class="text-center name">'+name+'<input type="hidden" name="packages['+j+'][]" value="'+name+'"/></td><td class="text-center"><input type="text" name="packages['+j+'][]" /></td><td class="text-center" ><span class="close">x</span></td></tr>';  
        $('.my_tbody').append(txt1);
}
// $(".close").click(function(){
//     alert('hello');
// })
// function Remove(){

//     alert('helllo');
// }
    });

</script>
@endsection












