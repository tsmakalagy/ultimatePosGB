@extends('layouts.app')

@section('title', __('lang_v1.product_price_setting.edit'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('lang_v1.product_price_setting.edit')
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
                        {!! Form::open(['action' =>['ProductPriceSettingController@update',$price_product->id]]) !!}
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('cours_usd', __('lang_v1.cours_usd') . '*:') !!}
                                {!! Form::text('cours_usd', $price_product->cours_usd, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('cours_rmb', __('lang_v1.cours_rmb') . '*:') !!}
                                {!! Form::text('cours_rmb',$price_product->cours_rmb, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('frais_taxe_usd_bateau', __('lang_v1.frais_taxe_usd_bateau') . '*:') !!}
                                {!! Form::text('frais_taxe_usd_bateau', $price_product->frais_taxe_usd_bateau, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('frais_taxe_usd_avion', __('lang_v1.frais_taxe_usd_avion') . ':') !!}
                                {!! Form::text('frais_taxe_usd_avion', $price_product->frais_taxe_usd_avion, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('frais_usd_bateau', __('lang_v1.frais_usd_bateau') . ':') !!}
                                {!! Form::text('frais_usd_bateau', $price_product->frais_usd_bateau, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('frais_compagnie_usd_bateau', __('lang_v1.frais_compagnie_usd_bateau') . ':') !!}
                                {!! Form::text('frais_compagnie_usd_bateau', $price_product->frais_compagnie_usd_bateau, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                      
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('constante_taxe', __('lang_v1.constante_taxe') . ':') !!}
                                {!! Form::text('constante_taxe', $price_product->constante_taxe, ['class' => 'form-control', 'rows' => 3]); !!}
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

