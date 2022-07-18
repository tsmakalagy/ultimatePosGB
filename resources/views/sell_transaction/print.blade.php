<!-- business information here -->

<div class="row">
    <br>
    {{-- <p>
	<!-- Header text -->
	@if (!empty($receipt_details->header_text))
	<div class="col-xs-6 text-left">
			{!! $receipt_details->header_text !!}
	</div>
	@endif
	
<!-- Logo -->
@if (!empty($receipt_details->logo))
<div class="col-xs-6 text-right" >
	<img style="max-height: 120px; width: auto;" src="{{$receipt_details->logo}}" class="img img-responsive center-block">
</div>	
@endif
</p> --}}

    <!-- business information here -->
    {{-- <div class="col-xs-12 text-center">
		@if (!empty($receipt_details->heade))
		<h2 class="text-center">
			<!-- Shop & Location Name  -->
			@if (!empty($receipt_details->display_name))
				{{$receipt_details->display_name}}
			@endif
		</h2>
		<!-- NIF-STAT -->
		<div class="text-center"><strong>@lang('lang_v1.NIF:')</strong> 4002930550, 
		<strong>@lang('lang_v1.STAT:')</strong> 46101 11 2018 0 10086</div>
	
		<!-- Address -->
		<p>
		@if (!empty($receipt_details->address))
				<small class="text-center">
				{!! $receipt_details->address !!}
				</small>
		@endif
		@if (!empty($receipt_details->contact))
			<br/>{!! $receipt_details->contact !!}
		@endif	
		@if (!empty($receipt_details->contact) && !empty($receipt_details->website))
			, 
		@endif
		@if (!empty($receipt_details->website))
			{{ $receipt_details->website }}
		@endif
		@if (!empty($receipt_details->location_custom_fields))
			<br>{{ $receipt_details->location_custom_fields }}
		@endif
		</p>
	
	
		<!-- Title of receipt -->
		@if (!empty($receipt_details->invoice_heading))
			<h3 class="text-center">
				@lang('lang_v1.Invoice')
			</h3>
		@endif
	</div> --}}

    information business
    <h3 class="text-center">
        @lang('lang_v1.Invoice')
    </h3>
</div>
<div class="row">
    <!-- 	Invoice  number, Date  -->
    <!-- <p style="width: 100% !important" class="word-wrap">-->
    <div class="col-xs-4 word-wrap">
        {{-- @if (!empty($receipt_details->invoice_no_prefix)) --}}
        <b>@lang('lang_v1.Invoice No.')</b>
        {{-- @endif --}}
        {{ $package->invoice_no }}



        <!-- customer info -->
        {{-- @if (!empty($receipt_details->customer_info)) --}}
        <br />
        <b>@lang('lang_v1.Customer')</b>
        {{-- :</b> {{ $name_and_mobile }}< --}}
        @if (!empty($customer))
            <br> {!! $customer->name !!} <br>
            {!! $customer->mobile !!} <br>
        @endif


    </div>
    <div class="col-xs-4 text-center">

        <strong>@lang('lang_v1.Commission Agent')</strong><br>
        @if (!empty($added_by))
            {{-- {{ $receipt_details->commission_agent }} <br> --}}
            {{ $added_by }}<br>
        @endif
    </div>
    <div class="col-xs-4 text-right">
        <b>@lang('messages.date'):</b>
        @if (!empty($package->transaction_date))
            {{ @format_date($package->transaction_date) }}
        @endif
    </div>


</div>


<div class="row">
    <div class="col-xs-12">
        <br />

        <table class="table table-responsive table-slim">
            <tr class="bg-green">

                <th>{{ __('lang_v1.package') }}</th>

                <th>{{ __('lang_v1.product') }}</th>

                <th>{{ __('lang_v1.mode_transport') }}</th>

                <th class="text-center">{{ __('image') }}</th>
                <th class="text-right">{{ __('lang_v1.price') }}</th>

            </tr>
            @php
                $i = 1;
            @endphp
            @foreach ($package->package_transaction_line as $pack_line)
                <tr>

                    <td>{{ $pack_line->package->bar_code }}</td>

                    <td>{{ $pack_line->package->product }}</td>

                    @if ($pack_line->package->mode_transport == 1)
                        <td>Avion</td>
                    @else
                        <td>Bateau</td>
                    @endif

                    <td>
                        @if (!empty($image_url))
                            @php
                                $img = $image_url->where('product_id', $pack_line->package->id)->first();
                                $img_src = $img->image;
                                $img_expl = explode('|', $img_src);
                                
                                $img = asset('/uploads/img/' . rawurlencode($img_expl[0]));
                            @endphp
                            <div class="text-center">
                                <img id="imageresource" src="{{ $img }}" alt="Responsive image" height="70px"
                                    width="80px">

                            </div>

                            {{-- @endforeach --}}
                        @else
                            image
                        @endif

                    </td>
                    <td class="text-right">@format_currency($pack_line->package->price)</td>

                </tr>
            @endforeach
        </table>
    </div>
</div>
<br>
<br>
<div class="row">

    <div class="col-xs-12">

        <table class="table table-slim">

            @if (!empty($package->payment_lines))
                {{-- @foreach ($receipt_details->payments as $payment) --}}
                @foreach ($package->payment_lines as $payment_line)
                    <tr>

                        <td>{{ $payment_line->payment_ref_no }}</td>
                        <td>@format_currency($payment_line->amount)</td>
                        <td> {{ $payment_line->method }}</td>
                        <td class="text-right">{{ @format_date($payment_line->paid_on) }}</td>
                        {{-- <td> @lang('lang_v1.Cash')</td>
                <td> payment amount</td>
                <td> payment date</td> --}}

                    </tr>
                @endforeach
                {{-- @endforeach --}}
            @endif

            <!-- Total Paid-->

            <tr>
                <td>
                    <b> @lang('lang_v1.Total Paid'):</b>
                </td>
                <td>
                    @format_currency($package->final_total)
                </td>
            </tr>





        </table>
    </div>

    {{-- <div class="col-xs-6">
        <div class="table-responsive">
            <table class="table table-slim">
                <tbody>
                    @if (!empty($receipt_details->total_quantity_label))
                        <tr class="color-555">
                            <th style="width:70%">
                                {!! $receipt_details->total_quantity_label !!}
                            </th>
                            <td class="text-right">
                                {{ $receipt_details->total_quantity }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <th style="width:70%">
                            @lang('lang_v1.Subtotal:')
                        </th>
                        <td class="text-right">
                            {{ $receipt_details->subtotal }}
                        </td>
                    </tr>
                    @if (!empty($receipt_details->total_exempt_uf))
                        <tr>
                            <th style="width:70%">
                                @lang('lang_v1.exempt')
                            </th>
                            <td class="text-right">
                                {{ $receipt_details->total_exempt }}
                            </td>
                        </tr>
                    @endif
                    <!-- Shipping Charges -->
                    @if (!empty($receipt_details->shipping_charges))
                        <tr>
                            <th style="width:70%">
                                @lang('lang_v1.shipping_charges:')
                            </th>
                            <td class="text-right">
                                {{ $receipt_details->shipping_charges }}
                            </td>
                        </tr>
                    @endif

                    @if (!empty($receipt_details->packing_charge))
                        <tr>
                            <th style="width:70%">
                                {!! $receipt_details->packing_charge_label !!}
                            </th>
                            <td class="text-right">
                                {{ $receipt_details->packing_charge }}
                            </td>
                        </tr>
                    @endif

                    <!-- Discount -->
                    @if (!empty($receipt_details->discount))
                        <tr>
                            <th>
                                @lang('lang_v1.Discount:')
                            </th>

                            <td class="text-right">
                                (-) {{ $receipt_details->discount }}
                            </td>
                        </tr>
                    @endif

                    @if (!empty($receipt_details->total_line_discount))
                        <tr>
                            <th>
                                {!! $receipt_details->line_discount_label !!}
                            </th>

                            <td class="text-right">
                                (-) {{ $receipt_details->total_line_discount }}
                            </td>
                        </tr>
                    @endif

                    @if (!empty($receipt_details->reward_point_label))
                        <tr>
                            <th>
                                {!! $receipt_details->reward_point_label !!}
                            </th>

                            <td class="text-right">
                                (-) {{ $receipt_details->reward_point_amount }}
                            </td>
                        </tr>
                    @endif

                    <!-- Tax -->
                    @if (!empty($receipt_details->tax))
                        <tr>
                            <th>
                                {!! $receipt_details->tax_label !!}
                            </th>
                            <td class="text-right">
                                (+) {{ $receipt_details->tax }}
                            </td>
                        </tr>
                    @endif

                    @if ($receipt_details->round_off_amount > 0)
                        <tr>
                            <th>
                                {!! $receipt_details->round_off_label !!}
                            </th>
                            <td class="text-right">
                                {{ $receipt_details->round_off }}
                            </td>
                        </tr>
                    @endif

                    <!-- Total -->
                    <tr>
                        <th>
                            @lang('lang_v1.Total:')
                        </th>
                        <td class="text-right">
                            {{ $receipt_details->total }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> --}}
    {{-- @if (!empty($receipt_details->total_in_words))
        <div class="col-xs-12">
            <p>Arrêté la présente facture à la somme de {!! nl2br($receipt_details->total_in_words) !!} Ariary</p>
        </div>
    @endif
    <div class="col-xs-12">
        <p>{!! nl2br($receipt_details->additional_notes) !!}</p>
    </div> --}}
</div>
{{-- <div class="row">
    @if (!empty($receipt_details->footer_text))
        <div class="@if ($receipt_details->show_barcode || $receipt_details->show_qr_code) col-xs-8 @else col-xs-12 @endif">
            {!! $receipt_details->footer_text !!}
        </div>
    @endif
    @if ($receipt_details->show_barcode || $receipt_details->show_qr_code)
        <div class="@if (!empty($receipt_details->footer_text)) col-xs-4 @else col-xs-12 @endif text-center">
            @if ($receipt_details->show_barcode)
                {{-- Barcode --}}
{{-- <img class="center-block"
    src="data:image/png;base64,{{ DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2, 30, [39, 48, 54], true) }}">
@endif

@if ($receipt_details->show_qr_code && !empty($receipt_details->qr_code_details))
    @php
        $qr_code_text = implode(', ', $receipt_details->qr_code_details);
    @endphp
    <img class="center-block mt-5"
        src="data:image/png;base64,{{ DNS2D::getBarcodePNG($qr_code_text, 'QRCODE', 3, 3, [39, 48, 54]) }}">
@endif
</div>
@endif
</div> --}}
