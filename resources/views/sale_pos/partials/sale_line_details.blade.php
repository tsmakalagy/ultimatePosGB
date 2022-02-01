<table class="table bg-gray">
        <tr class="bg-green">
        <th>#</th>
        <th>{{ __('sale.product') }}</th>
        @if( session()->get('business.enable_lot_number') == 1)
            <th>{{ __('lang_v1.lot_n_expiry') }}</th>
        @endif
        @if($sell->type == 'sales_order')
            <th>@lang('lang_v1.quantity_remaining')</th>
        @endif
        <th>{{ __('sale.qty') }}</th>
        @if(!empty($pos_settings['inline_service_staff']))
            <th>
                @lang('restaurant.service_staff')
            </th>
        @endif
        <th>{{ __('sale.unit_price') }}</th>
        <th>{{ __('sale.discount') }}</th>
        <th ><div style="margin-left:10px;">{{ __('lang_v1.image') }}</div></th>
        <th>{{ __('sale.price_inc_tax') }}</th>
        <th>{{ __('sale.subtotal') }}</th>
    </tr>
    @foreach($sell->sell_lines as $sell_line)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                {{ $sell_line->product->name }}
                @if( $sell_line->product->type == 'variable')
                - {{ $sell_line->variations->product_variation->name ?? ''}}
                - {{ $sell_line->variations->name ?? ''}},
                @endif
                {{ $sell_line->variations->sub_sku ?? ''}}
                @php
                $brand = $sell_line->product->brand;
                @endphp
                @if(!empty($brand->name))
                , {{$brand->name}}
                @endif

                @if(!empty($sell_line->sell_line_note))
                <br> {{$sell_line->sell_line_note}}
                @endif
                @if($is_warranty_enabled && !empty($sell_line->warranties->first()) )
                    <br><small>{{$sell_line->warranties->first()->display_name ?? ''}} - {{ @format_date($sell_line->warranties->first()->getEndDate($sell->transaction_date))}}</small>
                    @if(!empty($sell_line->warranties->first()->description))
                    <br><small>{{$sell_line->warranties->first()->description ?? ''}}</small>
                    @endif
                @endif

                @if(in_array('kitchen', $enabled_modules))
                    <br><span class="label @if($sell_line->res_line_order_status == 'cooked' ) bg-red @elseif($sell_line->res_line_order_status == 'served') bg-green @else bg-light-blue @endif">@lang('restaurant.order_statuses.' . $sell_line->res_line_order_status) </span>
                @endif
            </td>
            @if( session()->get('business.enable_lot_number') == 1)
                <td>{{ $sell_line->lot_details->lot_number ?? '--' }}
                    @if( session()->get('business.enable_product_expiry') == 1 && !empty($sell_line->lot_details->exp_date))
                    ({{@format_date($sell_line->lot_details->exp_date)}})
                    @endif
                </td>
            @endif
            @if($sell->type == 'sales_order')
                <td><span class="display_currency" data-currency_symbol="false" data-is_quantity="true">{{ $sell_line->quantity - $sell_line->so_quantity_invoiced }}</span> @if(!empty($sell_line->sub_unit)) {{$sell_line->sub_unit->short_name}} @else {{$sell_line->product->unit->short_name}} @endif</td>
            @endif
            <td>
                <span class="display_currency" data-currency_symbol="false" data-is_quantity="true">{{ $sell_line->quantity }}</span> @if(!empty($sell_line->sub_unit)) {{$sell_line->sub_unit->short_name}} @else {{$sell_line->product->unit->short_name}} @endif
            </td>
            @if(!empty($pos_settings['inline_service_staff']))
                <td>
                {{ $sell_line->service_staff->user_full_name ?? '' }}
                </td>
            @endif
            <td>
                <span class="display_currency" data-currency_symbol="true">{{ $sell_line->unit_price_before_discount }}</span>
            </td>
            <td>
                <span class="display_currency" data-currency_symbol="true">{{ $sell_line->get_discount_amount() }}</span> @if($sell_line->line_discount_type == 'percentage') ({{$sell_line->line_discount_amount}}%) @endif
            </td>
            <td style="width:150px;">
                 <div style="width:150px;">
                    <div class="thumbnail">
                      @if(!empty($sell_line->product->image ))
                        <a href="#" id="pop">
                        <img id="imageresource" src="{{$sell_line->product->image_url}}" alt="Responsive image" width="124" height="110">            
                        </a>
                      @endif
                    </div>                
                </div>
            </td>
            <td>
                <span class="display_currency" data-currency_symbol="true">{{ $sell_line->unit_price_inc_tax }}</span>
            </td>
            <td>
                <span class="display_currency" data-currency_symbol="true">{{ $sell_line->quantity * $sell_line->unit_price_inc_tax }}</span>
            </td>
        </tr>
        @if(!empty($sell_line->modifiers))
        @foreach($sell_line->modifiers as $modifier)
            <tr>
                <td>&nbsp;</td>
                <td>
                    {{ $modifier->product->name }} - {{ $modifier->variations->name ?? ''}},
                    {{ $modifier->variations->sub_sku ?? ''}}
                </td>
                @if( session()->get('business.enable_lot_number') == 1)
                    <td>&nbsp;</td>
                @endif
                <td>{{ $modifier->quantity }}</td>
                @if(!empty($pos_settings['inline_service_staff']))
                    <td>
                        &nbsp;
                    </td>
                @endif
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{ $modifier->unit_price }}</span>
                </td>
                <td>
                    &nbsp;
                </td>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{ $modifier->item_tax }}</span> 
                    @if(!empty($taxes[$modifier->tax_id]))
                    ( {{ $taxes[$modifier->tax_id]}} )
                    @endif
                </td>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{ $modifier->unit_price_inc_tax }}</span>
                </td>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{ $modifier->quantity * $modifier->unit_price_inc_tax }}</span>
                </td>
            </tr>
            @endforeach
        @endif
    @endforeach
</table>

<!-- Creates the bootstrap modal where the image will appear -->
<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog" >
      <div class="modal-content" >
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">{{ $sell_line->product->name }}</h4>
        </div>
        <div class="modal-body">
          <img src="" id="imagepreview" style="width: 400px; height: 364px;" >
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
  $("#pop").on("click", function() {
   $('#imagepreview').attr('src', $('#imageresource').attr('src')); // here asign the image to the modal when the user click the enlarge link
   $('#imagemodal').modal('show'); // imagemodal is the id attribute assigned to the bootstrap modal, then i use the show function
});
    </script>