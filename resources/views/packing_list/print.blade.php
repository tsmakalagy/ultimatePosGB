<div class="row">
  <div class="col-xs-12">
    
      <div class="row">
        <div class="col-sm-9">
          <div class="col-sm-4 invoice-col">
      
        <b>@lang('lang_v1.mode_transport'): </b>
        @if($package->mode_transport ==1)
        <span>avion</span>
        @else
        <span>bateau</span>
        @endif
        <br>
        <b>@lang('lang_v1.date_envoi'):</b>

      {{date('Y-m-d',strtotime($package->date_envoi)) }}<br>
    
          </div>
          </div>
    
      </div>
     
    
  </div>
</div>

<div class="row">
 
  
  
    
    <div class="col-xs-12">
      <div class="table-responsive table-slim">
        <table class="table bg-gray">
          <tr class="bg-green">
              <th>{{ __('lang_v1.customer') }}</th> 
              <th>{{ __('lang_v1.customer_tel') }}</th> 
              <th>{{ __('lang_v1.barcode') }}</th> 
              <th>{{ __('lang_v1.product') }}</th>
              <th>{{ __('lang_v1.qte') }}</th>
              <th>{{ __('lang_v1.length') }}</th>
              <th>{{ __('lang_v1.width') }}</th>
              <th>{{ __('lang_v1.height') }}</th>                    
              <th>{{ __('lang_v1.volume') }}</th> 
              <th>{{ __('lang_v1.weight') }}</th>   
              
      
          </tr>

            @foreach ( $package->packinglist_lines as $packinglistlines )
            @for ($i=0; $i<$packinglistlines->qte; $i++)
            @php
              $id = str_pad($i, 2, '0', STR_PAD_LEFT);                      
              $barcode=$packinglistlines->thepackage->sku.$id;
            @endphp

            <tr>
              @php
              $arr=array();
            $arr2=array();
              $arr3=array();
              $arr4=array();
            $product=$packinglistlines->thepackage->product;  
               $packing=$packinglistlines->thepackage->thepackage_package;
               
               foreach($packing as $pack){
                       $prod=$pack->product;
                       $prod2=$pack->customer_name;
                       $prod3=$pack->customer_tel;
                       array_push($arr,$prod);
                       array_push($arr3,$prod2);
                       array_push($arr4,$prod3);
                      //  return $packing;
                  }
                  $ar=implode(',', $arr);
                  $new_arr=$product.' '.$ar;
                  array_push($arr2,$new_arr);
                  $result=implode(',', $arr2);
                  $result2=implode(',', $arr3);
                  $result3=implode(',', $arr4);
                @endphp
                <td>
                  <table>
                    @foreach ( $arr3 as $the_arr3 )
                    <tr><td>{{$the_arr3}}<td></tr>
                    @endforeach
                    </table>
                </td>

                {{-- <td>{{$length}}</td> --}}
                <td>
                  <table>
                    @foreach ( $arr4 as $the_arr4 )
                    <tr><td>{{$the_arr4}}<td></tr>
                    @endforeach
                    </table>
                </td>
                <td>{{$barcode}}</td>
                <td>{{ $result }}</td>
              <td>1</td>        
              <td>{{$packinglistlines->thepackage->longueur }}</td>
              <td>{{$packinglistlines->thepackage->largeur }}</td>
              <td>{{$packinglistlines->thepackage->hauteur }}</td>
              <td>{{$packinglistlines->thepackage->volume }}</td>
              <td>{{$packinglistlines->thepackage->weight }}</td>
            </tr>
             @endfor 
            @endforeach
           
        </table>
      </div>
    
  </div>
  

</div>

{{-- Barcode --}}
{{-- <div class="row print_section">
  <div class="col-xs-12">
    <img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG('120', 'C128', 2,30,array(39, 48, 54), true)}}">
  </div>
</div> --}}