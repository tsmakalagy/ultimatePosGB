<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    @php
      $form_id = 'shipper_add_form';
      $url = 'ShipperController@store';
    @endphp
                        {!! Form::open(['url' => action('ProductPriceSettingController@store'), 'method' => 'post', 'id' => 'price_product_add_form']) !!}
   
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">@lang('lang_v1.product_price_setting.add')</h4>
                          </div>
                          <div class="modal-body">
                              <div class="row">  

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('cours_usd', __('lang_v1.cours_usd') . ':') !!}
                                {!! Form::text('cours_usd', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('cours_rmb', __('lang_v1.cours_rmb') . ':') !!}
                                {!! Form::text('cours_rmb', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('frais_taxe_usd_bateau', __('lang_v1.frais_taxe_usd_bateau') . ':') !!}
                                {!! Form::text('frais_taxe_usd_bateau', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('frais_taxe_usd_avion', __('lang_v1.frais_taxe_usd_avion') . ':') !!}
                                {!! Form::text('frais_taxe_usd_avion', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('frais_usd_bateau', __('lang_v1.frais_usd_bateau') . ':') !!}
                                {!! Form::text('frais_usd_bateau', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('frais_compagnie_usd_bateau', __('lang_v1.frais_compagnie_usd_bateau') . ':') !!}
                                {!! Form::text('frais_compagnie_usd_bateau', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                      
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('constante_taxe', __('lang_v1.constante_taxe') . ':') !!}
                                {!! Form::text('constante_taxe', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>

                      
                    </div>
                    @include('layouts.partials.module_form_part')
                </div>
                
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
                  <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
                </div>

                        {!! Form::close()  !!}
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->