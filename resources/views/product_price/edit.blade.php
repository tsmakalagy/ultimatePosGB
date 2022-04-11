@extends('layouts.app')

@section('title', __('lang_v1.price_product.edit'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('lang_v1.price_product.edit')
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
                        {!! Form::open(['action' =>['ProductPriceController@update',$price_product->id]]) !!}
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('product_name', __('lang_v1.product_name') . '*:') !!}
                                {!! Form::text('product_name', $price_product->product_name, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('product_spec', __('lang_v1.product_spec') . '*:') !!}
                                {!! Form::text('product_spec',$price_product->product_spec, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('china_price', __('lang_v1.china_price') . '*:') !!}
                                {!! Form::text('china_price', $price_product->china_price, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('kuaidi', __('lang_v1.kuaidi') . ':') !!}
                                {!! Form::text('kuaidi', $price_product->kuaidi, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('size', __('lang_v1.size') . ':') !!}
                                {!! Form::text('size', $price_product->size, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('volume', __('lang_v1.volume') . ':') !!}
                                {!! Form::text('volume', $price_product->volume, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                      
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('weight', __('lang_v1.weight') . ':') !!}
                                {!! Form::text('weight', $price_product->weight, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('link', __('lang_v1.link') . ':') !!}
                                {!! Form::text('link', $price_product->link, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('other_field1', __('lang_v1.other_field1') . ':') !!}
                                {!! Form::text('other_field1', $price_product->other_field1, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('other_field2', __('lang_v1.other_field2') . ':') !!}
                                {!! Form::text('other_field2', $price_product->other_field2, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('byship_price', __('lang_v1.byship_price') . ':') !!}
                                {!! Form::text('byship_price', $price_product->byship_price, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('byplane_price', __('lang_v1.byplane_price') . ':') !!}
                                {!! Form::text('byplane_price', $price_product->byplane_price, ['class' => 'form-control', 'rows' => 3]); !!}
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

