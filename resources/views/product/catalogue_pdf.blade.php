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
        
        

                       <div style="width: 100%;">
                        <!-- Masonry grid -->
                                         
                        <div class="header" style="text-align:center;">
           <h1>Catalogue des produits</h1>
        </div>
                             @foreach($product_catalogue as $product_catalogues)
                            <!-- Grid item -->
                            <div style="width: 30%;float: left;margin-left:15px;text-align:center;">
                                {{-- asset('/img/default.png') --}}
                                {{-- {{!empty($product_catalogues) ? <img class="img-fluid w-100 shadow-sm rounded-0 "  style="width:100%;height:100%;" src="{{$product_catalogues->image_url}}" alt=""> :  <img class="img-fluid w-100 shadow-sm rounded-0 "  style="width:100%;height:100%;" src="" alt="">}} --}}
                             <img class="img-fluid w-100 shadow-sm rounded-0 "  style="width:100%;height:100%;" src="{{!empty($product_catalogues) ? $product_catalogues->image_url:""}}" alt=""> 
                                <p>{{str_limit($product_catalogues->p_name,25)}}</p>
                                <p>@lang( 'lang_v1.price:'){{!empty($product_catalogues->sell_price_inc_tax) ? $product_catalogues->sell_price_inc_tax: "0.0000"}}</p>
                                {{-- <p>@lang( 'lang_v1.purchasse_price:'){{!empty($product_catalogues->dpp_inc_tax) ? $product_catalogues->dpp_inc_tax: "0.0000"}}</p> --}}
                                {{-- <p>@lang( 'lang_v1.stock:'){{!empty($product_catalogues->current_stock) ? $product_catalogues->current_stock: "0.0000"}} pcs</p> --}}

                            </div>
                             @endforeach
                           
                        </div>


    </section>
    <!-- /.content -->

@endsection

<script src="bootstrap.min.css"></script>
<script src="jquery-3.3.1.slim.min.js"></script>
<script src="bootstrap.bundle.min.js"></script>
<script src="font-awesome.min.css"></script>
<script src="imagesloaded.pkgd.min.js"></script>
<script src="masonry.pkgd.min.js"></script>
