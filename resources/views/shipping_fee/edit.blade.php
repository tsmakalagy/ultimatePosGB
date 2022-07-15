@extends('layouts.app')

@section('title', __('lang_v1.shipping_fee.edit'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('lang_v1.shipping_fee.edit')
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
                        {!! Form::open(['action' => ['ShippingFeeController@update', $shipping_fee->id]]) !!}
                        <div class="row">
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    {!! Form::label('type', __('lang_v1.type') . ':') !!}
                                    {!! Form::select('type', [0 => 'bateau', 1 => 'avion'], $shipping_fee->type, ['class' => 'form-control select2', 'id' => 'product_locations', 'placeholder' => __('messages.please_select'), 'required']) !!}
                                    {{-- {!! Form::select('packages[]', $package,null,['class' => 'form-control select2', 'multiple', 'id' => 'product_locations','required']); !!} --}}

                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('price', __('lang_v1.price') . ':') !!}
                                    {!! Form::text('price', $shipping_fee->price, ['class' => 'form-control', 'rows' => 3, 'required']) !!}
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-2">
                                {!! Form::submit(__('messages.update'), ['class' => 'btn btn-primary pull-right']) !!}
                            </div>
                        </div>

                        {!! Form::close() !!}

                        <div>
                        @endcomponent
                    </div>
                </div>


    </section>
    <!-- /.content -->

@endsection
