<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    @php
      $form_id = 'shipper_add_form';
      $url = 'ShipperController@store';
    @endphp
                        {!! Form::open(['url' => action('ProductPriceController@store'), 'method' => 'post', 'id' => 'price_product_add_form']) !!}
                        
                        <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('lang_v1.product_price.add')</h4>
    </div>
    <div class="modal-body">
        <div class="row">  
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('product_name', __('lang_v1.product_name') . '*:') !!}
                                {!! Form::text('product_name', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('product_spec', __('lang_v1.product_spec') . '*:') !!}
                                {!! Form::text('product_spec', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('china_price', __('lang_v1.china_price') . '*:') !!}
                                {!! Form::text('china_price', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('kuaidi', __('lang_v1.kuaidi') . ':') !!}
                                {!! Form::text('kuaidi', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('size', __('lang_v1.size') . ':') !!}
                                {!! Form::text('size', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('volume', __('lang_v1.volume') . ':') !!}
                                {!! Form::text('volume', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
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
                                {!! Form::label('link', __('lang_v1.link') . ':') !!}
                                {!! Form::text('link', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
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
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('byship_price', __('lang_v1.byship_price') . ':') !!}
                                {!! Form::text('byship_price', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('byplane_price', __('lang_v1.byplane_price') . ':') !!}
                                {!! Form::text('byplane_price', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
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

