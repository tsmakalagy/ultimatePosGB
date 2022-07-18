<div class="modal-dialog modal-xl no-print" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle"> @lang('sale.sell_details') (<b>
                    {{-- @if ($sell->type == 'sales_order')
                        @lang('restaurant.order_no')
                    @else --}}
                    @lang('sale.invoice_no')
                    {{-- @endif : --}}
                </b> {{ $package->invoice_no }})
            </h4>
        </div>
        <div class="modal-body">

            <div class="row">
                <div class="col-xs-12">
                    <p class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($package->transaction_date) }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <b>

                        {{ __('sale.invoice_no') }}

                    </b> #{{ $package->invoice_no }}<br>
                    <b>{{ __('sale.status') }}:</b>
                    {{ $package->status }}

                    <br>

                    <b>{{ __('sale.payment_status') }}:</b>
                    @if (!empty($package->payment_status))
                        {{ __('lang_v1.' . $package->payment_status) }}
                    @endif


                </div>
                <div class="col-sm-4">
                    {{-- @if (!empty($sell->contact->supplier_business_name))
                        {{ $sell->contact->supplier_business_name }}<br>
                    @endif --}}
                    <b>{{ __('lang_v1.customer') }}:</b> {{ $name_and_mobile }}<br>
                    <b>{{ __('lang_v1.commission_agent') }}:</b>
                    @if (!empty($added_by))
                        {{ $added_by }}<br>
                    @endif
                    {{-- {{ $name_and_mobile }}<br> --}}
                    {{-- <b>{{ __('business.address') }}:</b><br> --}}
                    {{-- @if (!empty($sell->billing_address()))
                        {{ $sell->billing_address() }}
                    @else
                        {!! $sell->contact->contact_address !!}
                        @if ($sell->contact->mobile)
                            <br>
                            {{ __('contact.mobile') }}: {{ $sell->contact->mobile }}
                        @endif
                        @if ($sell->contact->alternate_number)
                            <br>
                            {{ __('contact.alternate_contact_number') }}: {{ $sell->contact->alternate_number }}
                        @endif
                        @if ($sell->contact->landline)
                            <br>
                            {{ __('contact.landline') }}: {{ $sell->contact->landline }}
                        @endif
                    @endif --}}

                </div>


            </div>
            <br>
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <h4>{{ __('lang_v1.package') }}:</h4>
                </div>

                <div class="col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table class="table bg-gray">
                            <tr class="bg-green">
                                <th>#</th>
                                <th>{{ __('lang_v1.package') }}</th>

                                <th>{{ __('lang_v1.product') }}</th>

                                <th>{{ __('lang_v1.mode_transport') }}</th>
                                {{-- <th>{{ __('lang_v1.price') }}</th> --}}
                                <th>{{ __('lang_v1.length') }}</th>
                                <th>{{ __('lang_v1.width') }}</th>
                                <th>{{ __('lang_v1.height') }}</th>
                                <th>{{ __('lang_v1.weight') }}</th>
                                <th>{{ __('lang_v1.volume') }}</th>

                                <th class="text-right">{{ __('lang_v1.price') }}</th>

                            </tr>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($package->package_transaction_line as $pack_line)
                                {{-- {{ dd($pack_line->package) }} --}}
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $pack_line->package->bar_code }}</td>

                                    <td>{{ $pack_line->package->product }}</td>

                                    @if ($pack_line->package->mode_transport == 1)
                                        <td>Avion</td>
                                    @else
                                        <td>Bateau</td>
                                    @endif


                                    <td>{{ $pack_line->package->longueur }}</td>
                                    <td>{{ $pack_line->package->largeur }}</td>
                                    <td>{{ $pack_line->package->hauteur }}</td>
                                    <td>{{ $pack_line->package->weight }}</td>
                                    <td>{{ $pack_line->package->volume }}</td>

                                    <td><span class="display_currency pull-right"
                                            data-currency_symbol="true">{{ $pack_line->package->price }}</span></td>

                                </tr>
                            @endforeach

                        </table>


                    </div>
                </div>
            </div>
            <div class="row">
                @php
                    $total_paid = 0;
                @endphp

                <div class="col-sm-12 col-xs-12">
                    <h4>{{ __('sale.payment_info') }}:</h4>
                </div>
                <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table class="table bg-gray">
                            <tr class="bg-green">
                                <th>#</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('purchase.ref_no') }}</th>
                                <th>{{ __('sale.amount') }}</th>
                                <th>{{ __('sale.payment_mode') }}</th>
                                <th>{{ __('sale.payment_note') }}</th>
                            </tr>
                            @foreach ($package->payment_lines as $payment_line)
                                {{-- @php
                                        if ($payment_line->is_return == 1) {
                                            $total_paid -= $payment_line->amount;
                                        } else {
                                            $total_paid += $payment_line->amount;
                                        }
                                    @endphp --}}
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ @format_date($payment_line->paid_on) }}</td>
                                    <td>{{ $payment_line->payment_ref_no }}</td>
                                    <td><span class="display_currency"
                                            data-currency_symbol="true">{{ $payment_line->amount }}</span></td>
                                    <td>
                                        {{ $payment_types[$payment_line->method] ?? $payment_line->method }}
                                        @if ($payment_line->is_return == 1)
                                            <br />
                                            ({{ __('lang_v1.change_return') }})
                                        @endif
                                    </td>
                                    <td>
                                        @if ($payment_line->note)
                                            {{ ucfirst($payment_line->note) }}
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 ">
                    <div class="table-responsive">
                        <table class="table bg-gray">
                            <tr>
                                <th>{{ __('sale.total') }}: </th>
                                <td></td>
                                <td><span class="display_currency pull-right"
                                        data-currency_symbol="true">{{ $package->final_total }}</span></td>
                            </tr>

                            {{-- <tr>
                                <th>{{ __('lang_v1.round_off') }}: </th>
                                <td></td>
                                <td><span class="display_currency pull-right"
                                        data-currency_symbol="true">{{ $sell->round_off_amount }}</span></td>
                            </tr> --}}
                            <tr>
                                <th>{{ __('sale.total_payable') }}: </th>
                                <td></td>
                                <td><span class="display_currency pull-right"
                                        data-currency_symbol="true">{{ $package->final_total }}</span></td>
                            </tr>

                            <tr>
                                <th>{{ __('sale.total_paid') }}:</th>
                                <td></td>
                                <td><span class="display_currency pull-right"
                                        data-currency_symbol="true">{{ $package->final_total }}</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('sale.total_remaining') }}:</th>
                                <td></td>
                                <td>
                                    <!-- Converting total paid to string for floating point substraction issue -->
                                    @php
                                        $total_paid = (string) $total_paid;
                                    @endphp
                                    <span class="display_currency pull-right"
                                        data-currency_symbol="true">{{ $package->final_total }}</span>
                                </td>
                            </tr>

                        </table>
                    </div>
                </div>
            </div>
            {{-- <div class="row">
                <div class="col-sm-6">
                    <strong>{{ __('sale.sell_note') }}:</strong><br>
                    <p class="well well-sm no-shadow bg-gray">
                        @if ($sell->additional_notes)
                            {!! nl2br($sell->additional_notes) !!}
                        @else
                            --
                        @endif
                    </p>
                </div>
                <div class="col-sm-6">
                    <strong>{{ __('sale.staff_note') }}:</strong><br>
                    <p class="well well-sm no-shadow bg-gray">
                        @if ($sell->staff_note)
                            {!! nl2br($sell->staff_note) !!}
                        @else
                            --
                        @endif
                    </p>
                </div>
            </div> --}}
            <div class="row">
                <div class="col-md-12">
                    <strong>{{ __('lang_v1.activities') }}:</strong><br>
                    @includeIf('activity_log.activities', ['activity_type' => 'sell'])
                </div>
            </div>
        </div>
        <div class="modal-footer">
            {{-- @if ($sell->type != 'sales_order')
                    <a href="#" class="print-invoice btn btn-success"
                        data-href="{{ route('sell.printInvoice', [$package->id]) }}?package_slip=true"><i
                            class="fas fa-file-alt" aria-hidden="true"></i> @lang('lang_v1.packing_slip')</a>
                @endif --}}

            @can('print_invoice')
                <a href="#" class="print-invoice btn btn-primary"
                    data-href="{{ route('Sell_transaction.printInvoice', [$package->id]) }}"><i class="fa fa-print"
                        aria-hidden="true"></i> @lang('lang_v1.print_invoice')</a>
            @endcan
            <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang('messages.close')</button>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var element = $('div.modal-xl');
        __currency_convert_recursively(element);
    });
</script>
