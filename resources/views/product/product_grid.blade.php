@extends('layouts.app')

@section('title', __('lang_v1.product_catalogue'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('lang_v1.product_catalogue')
        </h1>
        <style>
            body {
  min-height: 100vh; 
  
}
        </style>
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
            <li class="active">Here</li>
        </ol> -->
    </section>

    <!-- Main content -->
    <section class="content">
        
        
                      
                       {{-- <div class="container text-center">
                           @foreach($product_catalogue as $products)
                           <p>{!!$products->id!!}</p>
                           <img class="img-fluid w-100 mb-3 img-thumbnail shadow-sm rounded-0"  style="width:80%;height:50%;" src="{{$products->image_url}}" alt="">

                           @endforeach
                       </div> --}}
 
                       
                        <div class="container-fluid text-center">
                            <!-- Masonry grid -->
                            <div class="row">
                                <!-- Grid sizer -->
                               <h1>@lang( 'lang_v1.product_catalogue')</h1>
                               {{-- {!! Form::hidden('selected_rows', null, ['id' => 'selected_rows']); !!} --}}
                              
                                <!-- Grid item -->
                                <div class="text-left">
                                    <input type="checkbox" id="checkAll" > @lang( 'lang_v1.select_all')
                                </div>
                                {!! Form::open(['url' => action('ProductController@cataloguePdf'), 'method' => 'post', 'id' => 'add_to_catalogue' ]) !!}
                                @foreach($product as $products)
                                {{-- <input type="hidden" name="{{'product_selecte'.$products->id}}" class="product_selecte" value="{{'toky'.$products->id}}"> --}}
                                <div class="col-lg-3 col-md-4">
                                {{-- {!! Form::checkbox('product_selected'.$products->id, $products->id); !!} --}}
                                 <input type="checkbox" name="{{'product_selected'.$products->id}}" class="product_selected" value="{{$products->id}}">
                                {{-- {!! Form::hidden('selected_rows', "jdjfj", ['id' => 'selected_rows']); !!} --}}
                                <img class="img-fluid w-100 img-thumbnail shadow-sm rounded-0 img-responsive"  style="width:90%;height:40%;" src="{{$products->image_url}}" alt="">
                                <p>{!!str_limit($products->p_name,30)!!}</p>
                                <p>@lang( 'lang_v1.sell_price:'){{!empty($products->sell_price_inc_tax) ? $products->sell_price_inc_tax: "0.0000"}}</p>
                                <p>@lang( 'lang_v1.purchasse_price:'){{!empty($products->dpp_inc_tax) ? $products->dpp_inc_tax: "0.0000"}}</p>
                                <p>@lang( 'lang_v1.stock:'){{!empty($products->current_stock) ? $products->current_stock: "0.0000"}} pcs</p>
                                
                            </div>
                                @endforeach
                                <div class="form-group">
                                    <div class="col-lg-10 col-lg-offset-2">
                                        {{-- {!! Form::submit(__('messages.update') , ['class' => 'btn btn-primary pull-right'] ) !!} --}}
                               {!! Form::submit(__('lang_v1.catalogue_pdf'), array('class' => 'btn btn-primary pull-right', 'id' => 'catalogue')) !!}
                                    
                                    </div>
                                </div>
                                 {!! Form::close() !!}
                            </div>
                        </div>
                        
                        {{-- {!! Form::open(['url' => action('ProductController@cataloguePdf'), 'method' => 'post', 'id' => 'add_to_catalogue' ]) !!}
                        {{-- <input id="selected_catalogue"  name="selected_catalogue" type="number" value="" style="display: none">  --}}
                        
                        {{-- {!! Form::hidden('selected_catalogue', $product, ['id' => 'selected_catalogue']); !!}
                       
                       {!! Form::submit(__('lang_v1.catalogue_pdf'), array('class' => 'btn btn-xs btn-primary', 'id' => 'catalogue')) !!}
                       {!! Form::close() !!}  --}}
    </section>
    <!-- /.content -->

@endsection

<script src="bootstrap.min.css"></script>
<script src="jquery-3.3.1.slim.min.js"></script>
<script src="bootstrap.bundle.min.js"></script>
<script src="font-awesome.min.css"></script>
<script src="imagesloaded.pkgd.min.js"></script>
<script src="masonry.pkgd.min.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>


<script type="text/javascript">
     $(document).ready( function(){
         $("#catalogue").click(function() {
      
           var select= $('.product_selecte').val();
        //document.write(select);
         
    
         });
    

        $("#checkAll").click(function(){
           // alert('chcked');
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
        });
    </script>