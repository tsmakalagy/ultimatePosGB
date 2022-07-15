<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        @php
            $form_id = 'shipper_add_form';
            $url = 'ShipperController@store';
        @endphp
        {!! Form::open(['url' => action('ShippingFeeController@store'), 'method' => 'post', 'id' => 'shipping_fee_add_form']) !!}



        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('lang_v1.shipping_fee_add')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6 ">
                    <div class="form-group">
                        {!! Form::label('type', __('lang_v1.type') . ':') !!}
                        {!! Form::select('type', [0 => 'bateau', 1 => 'avion'], null, ['class' => 'form-control select2', 'id' => 'product_locations', 'placeholder' => __('messages.please_select'), 'required']) !!}
                        {{-- {!! Form::select('packages[]', $package,null,['class' => 'form-control select2', 'multiple', 'id' => 'product_locations','required']); !!} --}}

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('price', __('lang_v1.price') . ':') !!}
                        {!! Form::text('price', $value = null, ['class' => 'form-control', 'rows' => 3, 'required']) !!}
                    </div>
                </div>

            </div>
            @include('layouts.partials.module_form_part')
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>

        {!! Form::close() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
