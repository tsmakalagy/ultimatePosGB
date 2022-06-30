<div class="modal-dialog modal-xl" id="my_modal" role="document">
	<div class="modal-content" id="my_modal_content">
     {!! Form::open(['action' =>['ThePackageController@saveImg', $id],'files' => true,'enctype' =>'multipart/form-data']) !!}
		<div class="modal-header">
		    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      <h4 class="modal-title" id="modalTitle">@lang('lang_v1.package')</h4>
	    </div>


   
	    <div class="modal-body">

 
    {{-- <div class="row">
      <div class="col-xs-12">
          <p class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($sell->transaction_date) }}</p>
      </div>
    </div> --}}
    
    <div class="row">
      <div class="container-fluid">     
        <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('image', __('lang_v1.product_image') . ':') !!}
              {{-- {!! Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); !!} --}}
              
             <input type="file" id="upload_ima" name="images[]" accept="image/*" multiple>
            <!-- Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); -->
              <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
            </div>
           
          </div>
    </div>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
  <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
</div>
{!! Form::close() !!}
</div>
</div>
   
     
       
<script type="text/javascript">
  $(document).ready(function(){
    var element = $('div.modal-xl');
    __currency_convert_recursively(element);


$('#scanner').val('');  // Input field should be empty on page load
    $('#scanner').focus();  // Input field should be focused on page load 

    $('html').on('click', function () {
        $('#scanner').focus();  // Input field should be focused again if you click anywhere
    });

    $('html').on('blur', function () {
        $('#scanner').focus();  // Input field should be focused again if you blur
    });

    $('#scanner').change(function () {

        if ($('#scanner').val() == '') {
            return;  // Do nothing if input field is empty
        }
        var val=$(this).val();
        $.ajax({
          type: 'GET',
          cache:false,
        url: '/package/create',
         data: {val:val},
         success: function(response) {

              window.location=response.url+'?barcode='+val;
 
         }
        });
    });

//     $("#scanner").blur(function() {

//         var val=$(this).val();
//         if(val.length !== 0){
//         $.ajax({
//         type: 'GET',
//         url: '/package/create',
//         data: {val:val},
//         success: function(response) {

//              window.location=response.url+'?barcode='+val;
 
//         }
//     });
// }
//     });

  });



</script>
