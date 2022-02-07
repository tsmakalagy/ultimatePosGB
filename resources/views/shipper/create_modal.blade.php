<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
  @php
    $form_id = 'shipper_add_form';
    $url = action('ShipperController@store');
  @endphp
    {!! Form::open(['url' => $url, 'method' => 'post', 'id' => $form_id ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('shipper.add')</h4>
    </div>

    <div class="modal-body">
        <div class="row">            
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('shipper_name', __('shipper.name') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        {!! Form::text('shipper_name', null, ['class' => 'form-control','placeholder' => __('shipper.name')]); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('tel', __('shipper.tel') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-mobile"></i>
                        </span>
                        {!! Form::text('tel', null, ['class' => 'form-control','placeholder' => __('shipper.tel')]); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('type', __('shipper.type') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-truck"></i>
                        </span>
                        {!! Form::text('type', null, ['class' => 'form-control','placeholder' => __('shipper.type')]); !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('other_details', __('shipper.other_details') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-cubes"></i>
                        </span>
                        {!! Form::text('other_details', null, ['class' => 'form-control','placeholder' => __('shipper.other_details')]); !!}
                    </div>
                </div>
            </div>

          <div class="clearfix"></div>


        </div>
        @include('layouts.partials.module_form_part')
    </div>
    
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}
  
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->