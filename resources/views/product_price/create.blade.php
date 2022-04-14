<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    @php
      $form_id = 'shipper_add_form';
      $url = 'ShipperController@store';
    @endphp
                        {!! Form::open(['url' => action('ProductPriceController@store'), 'method' => 'post', 'id' => 'price_product_add_form']) !!}
                         
                        <input id="hide_id" name="invisible" type="number" value="{{$product_price_setting->id}}" style="display: none">
                        <input id="hide_cu" name="invisible" type="number" value="{{$product_price_setting->cours_usd}}" style="display: none">
                        <input id="hide_fub" name="invisible" type="number" value="{{$product_price_setting->frais_usd_bateau}}" style="display: none">
                        <input id="hide_cr" name="invisible" type="number" value="{{$product_price_setting->cours_rmb}}" style="display: none">
                        <input id="hide_ct" name="invisible" type="number" value="{{$product_price_setting->constante_taxe}}" style="display: none">
                        <input id="hide_fcub" name="invisible" type="number" value="{{$product_price_setting->frais_compagnie_usd_bateau}}" style="display: none">
                        <input id="hide_ftub" name="invisible" type="number" value="{{$product_price_setting->frais_taxe_usd_bateau}}" style="display: none">
                        
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">@lang('lang_v1.price_product.add')</h4>
                          </div>
                          <div class="modal-body">
                              <div class="row">  
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('product_name', __('lang_v1.product_name') . '*:') !!}
                                {!! Form::text('product_name', $value= null, ['class' => 'form-control', 'rows' => 3,'required']); !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('product_spec', __('lang_v1.product_spec') . ':') !!}
                                {!! Form::text('product_spec', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                          <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('volume', __('lang_v1.volume') . '*:') !!}
                                {!! Form::text('volume', $value= null, ['class' => 'form-control', 'rows' => 3,'required']); !!}
                            </div>
                        </div>
                      
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('china_price', __('lang_v1.china_price') . '*:') !!}
                                {!! Form::text('china_price', $value= null, ['class' => 'form-control', 'rows' => 3,'required']); !!}
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
                                {!! Form::label('weight', __('lang_v1.weight') . ':') !!}
                                {!! Form::text('weight', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                      
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('fret', __('lang_v1.fret') . ':') !!}
                                {!! Form::text('fret', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('taxe', __('lang_v1.taxe') . ':') !!}
                                {!! Form::text('taxe', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('reviens1', __('lang_v1.reviens1') . ':') !!}
                                {!! Form::text('reviens1', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('reviens2', __('lang_v1.reviens2') . ':') !!}
                                {!! Form::text('reviens2', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                          <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('reviens_max', __('lang_v1.reviens_max') . ':') !!}
                                {!! Form::text('reviens_max', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('reviens', __('lang_v1.reviens') . ':') !!}
                                {!! Form::text('reviens', $value= null, ['class' => 'form-control', 'rows' => 3]); !!}
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
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

<script type="text/javascript">
$(document).ready(function(){

//value init
$('#volume').val("0.00");
$('#china_price').val("0.00");
$('#reviens_max').val("0.00");
$('#reviens').val("0.00");
$('#fret').val("0.00");
$('#taxe').val("0.00");
$('#reviens1').val("0.00");
$('#reviens2').val("0.00");
$('#kuaidi').val("0.00");
$('#size').val("0.00");
$('#weight').val("0.00");
$('#byplane_price').val("0.00");
$('#byship_price').val("0.00");

//disable some field
$('#fret').prop('readonly', true);
$('#taxe').prop('readonly', true);
$('#reviens1').prop('readonly', true);
$('#reviens2').prop('readonly', true);
$('#reviens_max').prop('readonly', true);
$('#reviens').prop('readonly', true);

//onblur volume
$("#volume").blur(function() {
var china_priceStr = $('#china_price').val();
var china_price = parseFloat(china_priceStr)||0;

var volumeStr = $(this).val();
var volume = parseFloat(volumeStr)||0;

var kuaidiStr=$('#kuaidi').val();
var kuaidi = parseFloat(kuaidiStr)||0;

var cuStr = $('#hide_cu').val();
var cu = parseFloat(cuStr)||0;

var crStr = $('#hide_cr').val();
var cr = parseFloat(crStr)||0;

var ctStr = $('#hide_ct').val();
var ct = parseFloat(ctStr)||0;

var fcubStr = $('#hide_fcub').val();
var fcub = parseFloat(fcubStr)||0;

var fubStr = $('#hide_fub').val();
var fub = parseFloat(fubStr)||0;

var ftubStr = $('#hide_ftub').val();
var ftub = parseFloat(ftubStr)||0;


var fret=volume*cu*fub;
var fretDec=parseFloat(fret).toFixed(2);

var taxe=(china_price*cr*(ct+volume)*cu*fcub)*0.5;
var taxeDec=parseFloat(taxe).toFixed(2);

var reviens1 = (china_price+kuaidi)*cr+taxe+fret;
var reviens1Dec=parseFloat(reviens1).toFixed(2);

var reviens2 = (china_price+kuaidi)*cr+volume*ftub;
var reviens2Dec=parseFloat(reviens2).toFixed(2)

var reviens_max = Math.max(reviens1, reviens2);
var reviens_maxDec=parseFloat(reviens_max).toFixed(2)

var reviens = Math.round(reviens_max);
var reviensDec=parseFloat(reviens).toFixed(2)

if(volume!=0){
$('#fret').val(fretDec);
}
else{
$('#fret').val('0.00');
}

if(china_price!=0 && volume!=0){
$('#reviens1').val(reviens1Dec);
$('#taxe').val(taxeDec);
$('#reviens2').val(reviens2Dec);
$('#reviens_max').val(reviens_maxDec);
$('#reviens').val(reviensDec);
}
else{
$('#reviens1').val('0.00');
$('#taxe').val('0.00');
$('#reviens2').val('0.00');
$('#reviens_max').val('0.00');
$('#reviens').val('0.00');
}
}); 


//onblur china_price
$("#china_price").blur(function() {
var china_priceStr = $(this).val();
var china_price = parseFloat(china_priceStr)||0;

var volumeStr = $('#volume').val();
var volume = parseFloat(volumeStr)||0;

var kuaidiStr=$('#kuaidi').val();
var kuaidi = parseFloat(kuaidiStr)||0;

var cuStr = $('#hide_cu').val();
var cu = parseFloat(cuStr)||0;

var ftubStr = $('#hide_ftub').val();
var ftub = parseFloat(ftubStr)||0;

var idStr = $('#hide_id').val();
var id = parseFloat(idStr)||0;

var crStr = $('#hide_cr').val();
var cr = parseFloat(crStr)||0;

var ctStr = $('#hide_ct').val();
var ct = parseFloat(ctStr)||0;

var fcubStr = $('#hide_fcub').val();
var fcub = parseFloat(fcubStr)||0;

var fubStr = $('#hide_fub').val();
var fub = parseFloat(fubStr)||0;

var fret=volume*cu*fub;
var fretDec=parseFloat(fret).toFixed(2);

var taxe=(china_price*cr*(ct+volume)*cu*fcub)*0.5;
var taxeDec=parseFloat(taxe).toFixed(2);

var reviens1 = (china_price+kuaidi)*cr+taxe+fret;
var reviens1Dec=parseFloat(reviens1).toFixed(2);

var reviens2 = (china_price+kuaidi)*cr+volume*ftub;
var reviens2Dec=parseFloat(reviens2).toFixed(2)

var reviens_max = Math.max(reviens1, reviens2);
var reviens_maxDec=parseFloat(reviens_max).toFixed(2)

var reviens = Math.round(reviens_max);
var reviensDec=parseFloat(reviens).toFixed(2)

if(volume!=0){
$('#fret').val(fretDec);
}
else{
$('#fret').val('0.00');
}

if(volume!=0 && china_price!=0){
$('#reviens1').val(reviens1Dec);
$('#taxe').val(taxeDec);
$('#reviens2').val(reviens2Dec);
$('#reviens_max').val(reviens_maxDec);
$('#reviens').val(reviensDec);
}
else{
$('#reviens1').val('0.00');
$('#taxe').val('0.00');
$('#reviens2').val('0.00');
$('#reviens_max').val('0.00');
$('#reviens').val('0.00');
}
}); 


//onblur kuaidi
$("#kuaidi").blur(function() {
var china_priceStr = $('#china_price').val();
var china_price = parseFloat(china_priceStr)||0;

var volumeStr = $('#volume').val();
var volume = parseFloat(volumeStr)||0;

var kuaidiStr=$(this).val();
var kuaidi = parseFloat(kuaidiStr)||0;

var cuStr = $('#hide_cu').val();
var cu = parseFloat(cuStr)||0;

var ftubStr = $('#hide_ftub').val();
var ftub = parseFloat(ftubStr)||0;

var idStr = $('#hide_id').val();
var id = parseFloat(idStr)||0;

var crStr = $('#hide_cr').val();
var cr = parseFloat(crStr)||0;

var ctStr = $('#hide_ct').val();
var ct = parseFloat(ctStr)||0;

var fcubStr = $('#hide_fcub').val();
var fcub = parseFloat(fcubStr)||0;

var fubStr = $('#hide_fub').val();
var fub = parseFloat(fubStr)||0;

var fret=volume*cu*fub;
var fretDec=parseFloat(fret).toFixed(2);

var taxe=(china_price*cr*(ct+volume)*cu*fcub)*0.5;
var taxeDec=parseFloat(taxe).toFixed(2);

var reviens1 = (china_price+kuaidi)*cr+taxe+fret;
var reviens1Dec=parseFloat(reviens1).toFixed(2);

var reviens2 = (china_price+kuaidi)*cr+volume*ftub;
var reviens2Dec=parseFloat(reviens2).toFixed(2)

var reviens_max = Math.max(reviens1, reviens2);
var reviens_maxDec=parseFloat(reviens_max).toFixed(2)

var reviens = Math.round(reviens_max);
var reviensDec=parseFloat(reviens).toFixed(2)

if(volume!=0){
$('#fret').val(fretDec);
}
else{
$('#fret').val('0.00');
}
//alert(taxe);

if(volume!=0 && china_price!=0){
$('#reviens1').val(reviens1Dec);
$('#taxe').val(taxeDec);
$('#reviens2').val(reviens2Dec);
$('#reviens_max').val(reviens_maxDec);
$('#reviens').val(reviensDec);
}
else{
$('#reviens1').val('0.00');
$('#taxe').val('0.00');
$('#reviens2').val('0.00');
$('#reviens_max').val('0.00');
$('#reviens').val('0.00');
}


}); 

//Fret = volume*cours_usd*fret_usd_bateau
//taxe=(prix_chine*cours_rmb*constante_taxe+volume*cours_usd*fret_company_usd_bateau)*0.5


//onsubmit
$("form#price_product_add_form").validate({
    
    submitHandler: function (form) {
      
      var form = $("form#price_product_add_form");
      var url = form.attr('action');
      
      form.find('button[type="submit"]').attr('disabled', true);
      $.ajax({
          method: "POST",
          url: url,
          dataType: 'json',
          data: $(form).serialize(),
          success: function(data){
              $('.product_price_modal').modal('hide');
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







