
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    @php
      $form_id = 'shipper_add_form';
      $url = 'ShipperController@createShipperType';
    @endphp
      {!! Form::open(['url' => action('ShipperController@storeShipperType'), 'method' => 'post', 'id' => $form_id]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang('shipper.add')</h4>
      </div>
  
      <div class="modal-body">
          <div class="row">            
          
              <div class="col-md-6">
                  <div class="form-group">
                      {!! Form::label('shipper_type', __('shipper.shipper_type') . ':') !!}
                      <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-truck"></i>
                          </span>
                          
                                {!! Form::text('shipper_type', $value= null, ['class' => 'form-control', 'rows' => 3,'placeholder' => __('shipper.shipper_type'),'required']); !!}
  
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
  
  
  
   
    
  
      <script type="text/javascript">
          $(document).ready(function(){
              $("form#shipper_add_form").validate({
      
      submitHandler: function (form) {
        
        var form = $("form#shipper_add_form");
        var url = form.attr('action');
        
        form.find('button[type="submit"]').attr('disabled', true);
        $.ajax({
            method: "POST",
            url: url,
            dataType: 'json',
            data: $(form).serialize(),
            success: function(data){
                $('.shipper_modal').modal('hide');
                if( data.success){
                    toastr.success(data.msg);
                   
                } else {
                    toastr.error(data.msg);
                }
            }
        });
        return true;
      }
    });
  });
  
      </script>
  






