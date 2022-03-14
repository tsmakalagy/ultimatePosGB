<div class="modal-dialog" role="document">
	{!! Form::open(['url' => action('SellController@updateShipping', [$transaction->id]), 'method' => 'put', 'id' => 'edit_shipping_form' ]) !!}
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">@lang('lang_v1.edit_shipping') - @if($transaction->type == 'purchase_order') {{$transaction->ref_no}} @else {{$transaction->invoice_no}} @endif</h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-6">
			        <div class="form-group">
			            {!! Form::label('shipping_details', __('sale.shipping_details') . ':*' ) !!}
			            {!! Form::textarea('shipping_details', !empty($transaction->shipping_details) ? $transaction->shipping_details : '', ['class' => 'form-control','placeholder' => __('sale.shipping_details'), 'required' ,'rows' => '4']); !!}
			        </div>
			    </div>		
				<div class="col-md-6">
					<div class="form-group">
						{!! Form::label('shipping_address', __('lang_v1.shipping_address') . ':' ) !!}
						{!! Form::textarea('shipping_address',!empty($transaction->shipping_address) ? $transaction->shipping_address : '', ['class' => 'form-control','placeholder' => __('lang_v1.shipping_address') ,'rows' => '4']); !!}
					</div>
				</div>

			    <div class="col-md-6">
			        <div class="form-group">
			            {!! Form::label('shipping_status', __('lang_v1.shipping_status') . ':' ) !!}
			            {!! Form::select('shipping_status',$shipping_statuses, !empty($transaction->shipping_status) ? $transaction->shipping_status : null, ['class' => 'form-control shipping_change','placeholder' => __('messages.please_select')]); !!}
			        </div>
			    </div>

				<div class="col-md-6">				
				<div class="form-group">
		            {!! Form::label('shipping_charges', __('lang_v1.shipping_charges')) !!}
					{!! Form::text('shipping_charges', !empty($transaction->shipping_charges) ? $transaction->shipping_charges : null, ['class' => 'form-control','placeholder' => __('lang_v1.shipping_charges')]); !!}
				</div>
			</div>
			
				<div class="col-md-6">
					<div class="form-group">
						{!! Form::label('shipping_date', __('lang_v1.shipping_date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('shipping_date', !empty($transaction->shipping_date) ? $transaction->shipping_date : null, ['class' => 'form-control date_shipping', 'readonly', 'required']);!!}
						</div>
					</div>
				</div>			
				<div class="col-md-6">
				<div class="form-group">
		            {!! Form::label('shipper_id', __('lang_v1.shipper_name')) !!}
		            {!! Form::select('shipper_id',$shippers,!empty($shipper->shipper_id) ? $shipper->shipper_id : null,  ['class' => 'form-control']); !!}
				</div>				
			</div>
			<div class="clearfix"></div>
			<div class="col-md-12">
				<div class="col-md-6">
					<div class="form-group">						
						{!! Form::label('shipper_type_id', __('lang_v1.shipping_zone') . ':') !!}
						<select name="shipper_type_id" id="shipper_type_id" class="form-control" >
							<option value="3">@lang('messages.please_select')</option>
							<option value="1">TANA-VILLE</option>
							<option value="2">PROVINCE</option>
							</select> 						
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group id_100">						
						{!! Form::label('address_id', __('lang_v1.shipping_location') . ':') !!}
						<select name="address_id" id="address_id" class="form-control" >							
							@if(isset($address))
							@foreach($all_address as $all_addresses)
							<option value="{{$all_addresses->id}}"  {{($all_addresses->id == $address->address_id) ? ' selected'  : null}}  class="theOption">{{$all_addresses->nom}}</option>
							@endforeach							
							@else
							<option value="" class="theOption">@lang('messages.please_select')</option>
							@endif
							</select> 
					</div>
				</div>
			</div>
		
			    @php
			        $custom_labels = json_decode(session('business.custom_labels'), true);

			        $shipping_custom_label_1 = !empty($custom_labels['shipping']['custom_field_1']) ? $custom_labels['shipping']['custom_field_1'] : '';

			        $is_shipping_custom_field_1_required = !empty($custom_labels['shipping']['is_custom_field_1_required']) && $custom_labels['shipping']['is_custom_field_1_required'] == 1 ? true : false;

			        $shipping_custom_label_2 = !empty($custom_labels['shipping']['custom_field_2']) ? $custom_labels['shipping']['custom_field_2'] : '';

			        $is_shipping_custom_field_2_required = !empty($custom_labels['shipping']['is_custom_field_2_required']) && $custom_labels['shipping']['is_custom_field_2_required'] == 1 ? true : false;

			        $shipping_custom_label_3 = !empty($custom_labels['shipping']['custom_field_3']) ? $custom_labels['shipping']['custom_field_3'] : '';
			        
			        $is_shipping_custom_field_3_required = !empty($custom_labels['shipping']['is_custom_field_3_required']) && $custom_labels['shipping']['is_custom_field_3_required'] == 1 ? true : false;

			        $shipping_custom_label_4 = !empty($custom_labels['shipping']['custom_field_4']) ? $custom_labels['shipping']['custom_field_4'] : '';
			        
			        $is_shipping_custom_field_4_required = !empty($custom_labels['shipping']['is_custom_field_4_required']) && $custom_labels['shipping']['is_custom_field_4_required'] == 1 ? true : false;

			        $shipping_custom_label_5 = !empty($custom_labels['shipping']['custom_field_5']) ? $custom_labels['shipping']['custom_field_5'] : '';
			        
			        $is_shipping_custom_field_5_required = !empty($custom_labels['shipping']['is_custom_field_5_required']) && $custom_labels['shipping']['is_custom_field_5_required'] == 1 ? true : false;
		        @endphp

		        @if(!empty($shipping_custom_label_1))
		        	@php
		        		$label_1 = $shipping_custom_label_1 . ':';
		        		if($is_shipping_custom_field_1_required) {
		        			$label_1 .= '*';
		        		}
		        	@endphp

		        	<div class="col-md-6">
				        <div class="form-group">
				            {!! Form::label('shipping_custom_field_1', $label_1 ) !!}
				            {!! Form::text('shipping_custom_field_1', !empty($transaction->shipping_custom_field_1) ? $transaction->shipping_custom_field_1 : null, ['class' => 'form-control','placeholder' => $shipping_custom_label_1, 'required' => $is_shipping_custom_field_1_required]); !!}
				        </div>
				    </div>
		        @endif
		        @if(!empty($shipping_custom_label_2))
		        	@php
		        		$label_2 = $shipping_custom_label_2 . ':';
		        		if($is_shipping_custom_field_2_required) {
		        			$label_2 .= '*';
		        		}
		        	@endphp

		        	<div class="col-md-6">
				        <div class="form-group">
				            {!! Form::label('shipping_custom_field_2', $label_2 ) !!}
				            {!! Form::text('shipping_custom_field_2', !empty($transaction->shipping_custom_field_2) ? $transaction->shipping_custom_field_2 : null, ['class' => 'form-control','placeholder' => $shipping_custom_label_2, 'required' => $is_shipping_custom_field_2_required]); !!}
				        </div>
				    </div>
		        @endif
		        @if(!empty($shipping_custom_label_3))
		        	@php
		        		$label_3 = $shipping_custom_label_3 . ':';
		        		if($is_shipping_custom_field_3_required) {
		        			$label_3 .= '*';
		        		}
		        	@endphp

		        	<div class="col-md-6">
				        <div class="form-group">
				            {!! Form::label('shipping_custom_field_3', $label_3 ) !!}
				            {!! Form::text('shipping_custom_field_3', !empty($transaction->shipping_custom_field_3) ? $transaction->shipping_custom_field_3 : null, ['class' => 'form-control','placeholder' => $shipping_custom_label_3, 'required' => $is_shipping_custom_field_3_required]); !!}
				        </div>
				    </div>
		        @endif
		        @if(!empty($shipping_custom_label_4))
		        	@php
		        		$label_4 = $shipping_custom_label_4 . ':';
		        		if($is_shipping_custom_field_4_required) {
		        			$label_4 .= '*';
		        		}
		        	@endphp

		        	<div class="col-md-6">
				        <div class="form-group">
				            {!! Form::label('shipping_custom_field_4', $label_4 ) !!}
				            {!! Form::text('shipping_custom_field_4', !empty($transaction->shipping_custom_field_4) ? $transaction->shipping_custom_field_4 : null, ['class' => 'form-control','placeholder' => $shipping_custom_label_4, 'required' => $is_shipping_custom_field_4_required]); !!}
				        </div>
				    </div>
		        @endif
		        @if(!empty($shipping_custom_label_5))
		        	@php
		        		$label_5 = $shipping_custom_label_5 . ':';
		        		if($is_shipping_custom_field_5_required) {
		        			$label_5 .= '*';
		        		}
		        	@endphp

		        	<div class="col-md-6">
				        <div class="form-group">
				            {!! Form::label('shipping_custom_field_5', $label_5 ) !!}
				            {!! Form::text('shipping_custom_field_5', !empty($transaction->shipping_custom_field_5) ? $transaction->shipping_custom_field_5 : null, ['class' => 'form-control','placeholder' => $shipping_custom_label_5, 'required' => $is_shipping_custom_field_5_required]); !!}
				        </div>
				    </div>
		        @endif
		        <div class="clearfix"></div>
		        <div class="col-md-12">
			        <div class="form-group">
			            {!! Form::label('shipping_note', __('lang_v1.shipping_note') . ':' ) !!}
			            {!! Form::textarea('shipping_note', null, ['class' => 'form-control','placeholder' => __('lang_v1.shipping_note') ,'rows' => '4']); !!}
			        </div>
			    </div>
		        <div class="col-md-12">
		        	<div class="form-group">
                        <label for="fileupload">
                            @lang('lang_v1.shipping_documents'):
                        </label>
                        <div class="dropzone" id="shipping_documents_dropzone"></div>
                        {{-- params for media upload --}}
					    <input type="hidden" id="media_upload_url" value="{{route('attach.medias.to.model')}}">
					    <input type="hidden" id="model_id" value="{{$transaction->id}}">
					    <input type="hidden" id="model_type" value="App\Transaction">
					    <input type="hidden" id="model_media_type" value="shipping_document">
                    </div>
		        </div>
		        <div class="col-md-12">
		        	@php
                    	$medias = $transaction->media->where('model_media_type', 'shipping_document')->all();
                    @endphp
                    @include('sell.partials.media_table', ['medias' => $medias, 'delete' => true])
		        </div>
			</div>
			@if(!empty($activities))
			  <div class="row">
			    <div class="col-md-12">
			          <strong>{{ __('lang_v1.activities') }}:</strong><br>
			          @includeIf('activity_log.activities', ['activity_type' => 'sell'])
			      </div>
			  </div>
			  @endif
		</div>
		<div class="modal-footer">
			<button type="submit" class="btn btn-primary">@lang('messages.update')</button>
		    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.cancel')</button>
		</div>
	
		{{ Form::hidden('status_date_updating', $transaction->status_date_updating,['class' => 'datetime']) }}
			        
		{!! Form::close() !!}
	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

<script type="text/javascript">
    	$(document).ready( function() {
		//var d= new Date().toISOString().slice(0, 19).replace('T', ' ');
		var time = '<?php echo $carbon ?>';
    	$('.shipping_change').change(function(){
			$('.datetime').val(time);

	        });
		
		$('.date_shipping').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            ignoreReadonly: true,
            });
			
		//dropdown with search 
		$('#address_id').select2({ width: '100%'});

		//addresses
		var val=$("#address_id").val();	
		$("#shipper_type_id").change(function() {

   		var selectedbrand = $(this).val();
   		var type_id = {{$type_id}};
  
   		var form = $("form#edit_sell_form");
		var url = form.attr('action');
		$.ajax({
		type: 'GET',
		url: '/sells/edit-shipping/'+type_id,
		data: {selectedbrand:selectedbrand},
		success: function(response) {
	

		//$("option").remove(".theOption");
		//$(".theOption").hide();
		$("#address_id option").each(function (index) {
			if ($(this).is(':selected')) {
				$(this).prop('disabled', false);
			}
			else {
				$(this).remove();
			}
		});
		var text;
		var i;
		for (i = 0; i < response.length; i++) {
		
		text+='<option value="'+response[i].id+'" class="theOption">'+response[i].nom+'</option>';
		} 
		$("#address_id").append(text);
		}	
		});
		}); 
		});
    </script>