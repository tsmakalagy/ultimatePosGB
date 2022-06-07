@extends('layouts.app')

@section('title', __('lang_v1.product_catalogue'))

@section('content')

    <!-- Main content -->
    <section class="contents">
        <div class="container" style="background-color: white; width: 100%;">

        @foreach($product_catalogue as $product_catalogues)
            <!-- Grid item -->
                <div style="width: 30%;float: left;margin-left:15px;text-align:center;">
                    {{-- asset('/img/default.png') --}}
                    {{-- {{!empty($product_catalogues) ? <img class="img-fluid w-100 shadow-sm rounded-0 "  style="width:100%;height:100%;" src="{{$product_catalogues->image_url}}" alt=""> :  <img class="img-fluid w-100 shadow-sm rounded-0 "  style="width:100%;height:100%;" src="" alt="">}} --}}
                    <img class="img-fluid w-100 shadow-sm rounded-0 " style="width:100%;height:100%;"
                         src="{{!empty($product_catalogues) ? $product_catalogues->image_url:""}}" alt="">
                    <p style="margin-top: 10px;">{{str_limit($product_catalogues->p_name,25)}}</p>
                    <p>@lang( 'lang_v1.price:')@if (!empty($product_catalogues->sell_price_inc_tax))
                            @format_currency($product_catalogues->sell_price_inc_tax) @else 0.0 @endif</p>
                    {{-- <p>@lang( 'lang_v1.purchasse_price:'){{!empty($product_catalogues->dpp_inc_tax) ? $product_catalogues->dpp_inc_tax: "0.0000"}}</p> --}}
                    {{-- <p>@lang( 'lang_v1.stock:'){{!empty($product_catalogues->current_stock) ? $product_catalogues->current_stock: "0.0000"}} pcs</p> --}}

                </div>
                @if (($loop->index + 1) % 3 == 0)<div style="margin-bottom: 50px"></div>@endif
            @endforeach

        </div>


    </section>
    <!-- /.content -->

@endsection


