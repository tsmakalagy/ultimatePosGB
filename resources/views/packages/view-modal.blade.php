<<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content">
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


      @if(!empty($image_url))
  @php
    $img_src=$image_url->image;
        $img_expl=explode('|',$img_src);
  @endphp
  @foreach($img_expl as $images)
  @php
  $img = asset('/uploads/img/' . rawurlencode($images));
  @endphp
  <div class="col-sm-3 col-md-3 invoice-col">
					{{-- <div class="thumbnail"> --}}
						<img src="{{$img}}" alt="Product image">
					{{-- </div> --}}
				</div>
      
  @endforeach
      @endif
    </div>
    
    <div class="row">
      <br>
      <div class="col-md-12">
        <div class="table-responsive">
          <table class="table bg-gray">
            <tr class="bg-green">
            
              <th>{{ __('lang_v1.product_name') }}</th>
              <th>{{ __('lang_v1.customer') }}</th>
              <th>{{ __('lang_v1.volume') }}</th>
              <th>{{ __('lang_v1.weight') }}</th>
              <th>{{ __('lang_v1.barcode') }}</th>
              <th>{{ __('lang_v1.status') }}</th>
            </tr>

              <tr>
                <td>{{ ucfirst($package->product) }}</td>
                <td>{{ ucfirst($package->name) }}</td>
                <td>{{$package->volume }}</td>
                <td>{{$package->weight }}</td>
                <td>{{$package->bar_code }} </td>
                <td>{{$package->status }} </td>

              </tr>

          </table>
        </div>
      </div>
    </div>
</div>
<div class="modal-footer">

  <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
</div>
</div>
</div>
   
     
{{--       
<script type="text/javascript">
  $(document).ready(function(){
    var element = $('div.modal-xl');
    __currency_convert_recursively(element);
  });
</script> --}}
