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
                           @foreach($product_catalogue as $product_catalogues)
                           <p>{!!$product_catalogues->id!!}</p>
                           <img class="img-fluid w-100 mb-3 img-thumbnail shadow-sm rounded-0"  style="width:80%;height:50%;" src="{{$product_catalogues->image_url}}" alt="">

                           @endforeach
                       </div> --}}
                      
                    
                       
                        <div class="container-fluid text-center">
                            <!-- Masonry grid -->
                            <div class="row">
                                <!-- Grid sizer -->
                               <h1>template</h1>
                                
                                <!-- Grid item -->
                                @foreach($product_catalogue as $product_catalogues)
                                <div class="col-lg-3 col-md-4">
                                      
                                <img class="img-fluid w-100 img-thumbnail shadow-sm rounded-0"  style="width:90%;height:40%;" src="{{$product_catalogues->image_url}}" alt="">
                                <p>{!!$product_catalogues->p_name!!}</p>
                                <p>{!!$product_catalogues->sell_price_inc_tax!!}</p>
                                <p>{!!$product_catalogues->dpp_inc_tax!!}</p>
                                <p>{{ !empty($product_catalogues->current_stock) ? $product_catalogues->current_stock: "0.0000"}}</p>
                                
                            </div>
                                @endforeach
                            </div>
                        </div>
                        <a href=""></a>
                        {{-- {!! Form::open(['url' => action('ProductController@cataloguePdf'), 'method' => 'post', 'id' => 'add_to_catalogue' ]) !!}
                        {{-- <input id="selected_catalogue"  name="selected_catalogue" type="number" value="" style="display: none">  --}}
                        
                        {!! Form::hidden('selected_catalogue', $product_catalogue, ['id' => 'selected_catalogue']); !!}
                       
                       {!! Form::submit(__('lang_v1.catalogue_pdf'), array('class' => 'btn btn-xs btn-primary', 'id' => 'catalogue')) !!}
                       {!! Form::close() !!} --}}
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
    // $(document).ready( function(){
    //     $("#catalogue").click(function() {
      
    //       var select= $('input#selected_catalogue').val();
    //    alert(select);
         
    
    //     });
    // });
    </script>