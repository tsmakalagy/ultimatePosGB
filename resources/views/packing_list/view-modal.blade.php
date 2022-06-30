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
      <div class="col-sm-9">
        <div class="col-sm-4 invoice-col">
          <b>@lang('lang_v1.date_envoi'):</b>
      {{$package->date_envoi }}<br>
      <b>@lang('lang_v1.mode_transport'): </b>
      @if($package->mode_transport ==1)
      <span>avion</span>
      @else
      <span>bateau</span>
      @endif
      <br>
 
        </div>
        </div>

    </div>
    
    <div class="row">
      <br>
      <div class="col-md-12">
        <div class="table-responsive">
          <table class="table bg-gray">
            <tr class="bg-green">
              <th>{{ __('SKU') }}</th>
              <th>{{ __('lang_v1.product') }}</th>
              
              <th>{{ __('lang_v1.length') }}</th>
              <th>{{ __('lang_v1.width') }}</th>
              <th>{{ __('lang_v1.height') }}</th>                    
              <th>{{ __('lang_v1.volume') }}</th> 
              <th>{{ __('lang_v1.weight') }}</th>   
        
            </tr>
             {{-- @php
     $arr=array();
              $arr2=array();
              $pack_listlines=$package->packinglist_lines;
           
               foreach($pack_listlines as $packinglistlines){
                  // return($packinglistlines);
                  $sku=$packinglistlines->thepackage->sku;
                  $product=$packinglistlines->thepackage->product;  
                  $packing=$packinglistlines->thepackage->thepackage_package;
                  //  return $product.'('.$sku.')';
                  // return $packing;
                  foreach($packing as $pack){
                       $prod=$pack->product;
                       array_push($arr,$prod);
                      //  return $packing;
                  }
                  $ar=implode(',', $arr);
                  $new_arr=$sku.$product.$ar;
                  array_push($arr2,$new_arr);
                  // return $packinglistlines->thepackage->product.','.$product.'('.$sku.')';
              }
              
               return $arr2;
  @endphp --}}
              @foreach ( $package->packinglist_lines as $packinglistlines )
              <tr>
                @php
                $arr=array();
              $arr2=array();
              $product=$packinglistlines->thepackage->product;  
                 $packing=$packinglistlines->thepackage->thepackage_package;
                 
                 foreach($packing as $pack){
                       $prod=$pack->product;
                       array_push($arr,$prod);
                      //  return $packing;
                  }
                  $ar=implode(',', $arr);
                  $new_arr=$product.$ar;
                  array_push($arr2,$new_arr);
                  $result=implode(',', $arr2);
                @endphp
                <td>{{$packinglistlines->thepackage->sku}}</td>
                <td>{{ $result }}</td>
        
                <td>{{$packinglistlines->thepackage->longueur }}</td>
                <td>{{$packinglistlines->thepackage->largeur }}</td>
                <td>{{$packinglistlines->thepackage->hauteur }}</td>
                <td>{{$packinglistlines->thepackage->volume }}</td>
                <td>{{$packinglistlines->thepackage->weight }}</td>
              </tr>
              @endforeach
             
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
