@extends('layouts.app')

@section('title', __('lang_v1.price_product.edit'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('lang_v1.price_product.edit')
        </h1>
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
            <li class="active">Here</li>
        </ol> -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-solid'])
                    <div class="container-fluid">
                        {!! Form::open(['action' =>['ProductPriceController@update', $price_product->id]]) !!}
                        <input id="hide_price_product_id" name="invisible" type="number" value="{{$price_product->id}}"
                               style="display: none">
                        <input id="hide_id" name="invisible" type="number" value="{{$product_price_setting->id}}"
                               style="display: none">
                        <input id="hide_cu" name="invisible" type="number" value="{{$product_price_setting->cours_usd}}"
                               style="display: none">
                        <input id="hide_fub" name="invisible" type="number"
                               value="{{$product_price_setting->frais_usd_bateau}}" style="display: none">
                        <input id="hide_cr" name="invisible" type="number" value="{{$product_price_setting->cours_rmb}}"
                               style="display: none">
                        <input id="hide_ct" name="invisible" type="number"
                               value="{{$product_price_setting->constante_taxe}}" style="display: none">
                        <input id="hide_fcub" name="invisible" type="number"
                               value="{{$product_price_setting->frais_compagnie_usd_bateau}}" style="display: none">
                        <input id="hide_ftub" name="invisible" type="number"
                               value="{{$product_price_setting->frais_taxe_usd_bateau}}" style="display: none">

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('product_name', __('lang_v1.product_name') . '*:') !!}
                                {!! Form::text('product_name', $price_product->product_name, ['class' => 'form-control', 'rows' => 3,'required']); !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('product_spec', __('lang_v1.product_spec') . ':') !!}
                                {!! Form::text('product_spec',$price_product->product_spec, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('volume', __('lang_v1.volume') . ':') !!}
                                {!! Form::text('volume', $price_product->volume, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('china_price', __('lang_v1.china_price') . ':') !!}
                                {!! Form::text('china_price', $price_product->china_price, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('kuaidi', __('lang_v1.kuaidi') . ':') !!}
                                {!! Form::text('kuaidi', $price_product->kuaidi, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('size', __('lang_v1.size') . ':') !!}
                                {!! Form::text('size', $price_product->size, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('weight', __('lang_v1.weight') . ':') !!}
                                {!! Form::text('weight', $price_product->weight, ['class' => 'form-control', 'rows' => 3]); !!}
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
                                {!! Form::text('reviens',  $price_product->suggested_price, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('link', __('lang_v1.link') . ':') !!}
                                {!! Form::text('link', $price_product->link, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('byship_price', __('lang_v1.byship_price') . ':') !!}
                                {!! Form::text('byship_price', $price_product->byship_price, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('byplane_price', __('lang_v1.byplane_price') . ':') !!}
                                {!! Form::text('byplane_price', $price_product->byplane_price, ['class' => 'form-control', 'rows' => 3]); !!}
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-2">
                                {!! Form::submit(__('messages.update') , ['class' => 'btn btn-primary pull-right'] ) !!}
                            </div>
                        </div>

                        {!! Form::close()  !!}

                        <div>

                            @endcomponent
                        </div>
                    </div>


    </section>
    <!-- /.content -->

@endsection
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css"></script>

<script type="text/javascript">
    $(document).ready(function () {

        //value init
        var china_priceStr = $('#china_price').val();
        var china_price = parseFloat(china_priceStr) || 0;

        var volumeStr = $('#volume').val();
        var volume = parseFloat(volumeStr) || 0;

        var kuaidiStr = $('#kuaidi').val();
        var kuaidi = parseFloat(kuaidiStr) || 0;

        var cuStr = $('#hide_cu').val();
        var cu = parseFloat(cuStr) || 0;

        var crStr = $('#hide_cr').val();
        var cr = parseFloat(crStr) || 0;

        var ctStr = $('#hide_ct').val();
        var ct = parseFloat(ctStr) || 0;

        var ftubStr = $('#hide_ftub').val();
        var ftub = parseFloat(ftubStr) || 0;

        var fcubStr = $('#hide_fcub').val();
        var fcub = parseFloat(fcubStr) || 0;

        var fubStr = $('#hide_fub').val();
        var fub = parseFloat(fubStr) || 0;

        var fret = volume * cu * fub;
        var fretDec = parseFloat(fret).toFixed(2);

        // var taxe = (china_price * cr * (ct + volume) * cu * fcub) * 0.5;
        var taxe = (china_price * cr * ct + volume * cu * fcub) * 0.5;
        var taxeDec = parseFloat(taxe).toFixed(2);

        var reviens1 = (china_price + kuaidi) * cr + taxe + fret;
        var reviens1Dec = parseFloat(reviens1).toFixed(2);

        var reviens2 = (china_price + kuaidi) * cr + volume * ftub;
        var reviens2Dec = parseFloat(reviens2).toFixed(2)

        var reviens_max = Math.max(reviens1, reviens2);
        var reviens_maxDec = parseFloat(reviens_max).toFixed(2)

        var reviens = Math.round(reviens_max);
        var reviensDec = parseFloat(reviens).toFixed(2)

        $('#reviens_max').val(reviens_maxDec);
        $('#fret').val(fretDec);
        $('#taxe').val(taxeDec);
        $('#reviens1').val(reviens1Dec);
        $('#reviens2').val(reviens2Dec);
        $('#reviens').val(reviensDec);

        //disable some field
        $('#fret').prop('readonly', true);
        $('#taxe').prop('readonly', true);
        $('#reviens1').prop('readonly', true);
        $('#reviens2').prop('readonly', true);
        $('#reviens_max').prop('readonly', true);
        $('#reviens').prop('readonly', true);

        //onblur volume
        $("#volume").blur(function () {
            var china_priceStr = $('#china_price').val();
            var china_price = parseFloat(china_priceStr) || 0;

            var volumeStr = $(this).val();
            var volume = parseFloat(volumeStr) || 0;

            var kuaidiStr = $('#kuaidi').val();
            var kuaidi = parseFloat(kuaidiStr) || 0;

            var cuStr = $('#hide_cu').val();
            var cu = parseFloat(cuStr) || 0;

            var crStr = $('#hide_cr').val();
            var cr = parseFloat(crStr) || 0;

            var ctStr = $('#hide_ct').val();
            var ct = parseFloat(ctStr) || 0;

            var ftubStr = $('#hide_ftub').val();
            var ftub = parseFloat(ftubStr) || 0;

            var fcubStr = $('#hide_fcub').val();
            var fcub = parseFloat(fcubStr) || 0;

            var fubStr = $('#hide_fub').val();
            var fub = parseFloat(fubStr) || 0;

            var fret = volume * cu * fub;
            var fretDec = parseFloat(fret).toFixed(2);

            // var taxe = (china_price * cr * (ct + volume) * cu * fcub) * 0.5;
            var taxe = (china_price * cr * ct + volume * cu * fcub) * 0.5;
            var taxeDec = parseFloat(taxe).toFixed(2);

            var reviens1 = (china_price + kuaidi) * cr + taxe + fret;
            var reviens1Dec = parseFloat(reviens1).toFixed(2);

            var reviens2 = (china_price + kuaidi) * cr + volume * ftub;
            var reviens2Dec = parseFloat(reviens2).toFixed(2)

            var reviens_max = Math.max(reviens1, reviens2);
            var reviens_maxDec = parseFloat(reviens_max).toFixed(2)

            var reviens = Math.round(reviens_max);
            var reviensDec = parseFloat(reviens).toFixed(2)

            if (volume != 0) {

                $('#fret').val(fretDec);
            } else {
                $('#fret').val('0.00');
            }

            if (china_price != 0 && volume != 0) {
                $('#reviens1').val(reviens1Dec);
                $('#taxe').val(taxeDec);
                $('#reviens2').val(reviens2Dec);
                $('#reviens_max').val(reviens_maxDec);
                $('#reviens').val(reviensDec);
            } else {
                $('#reviens1').val('0.00');
                $('#taxe').val('0.00');
                $('#reviens2').val('0.00');
                $('#reviens_max').val('0.00');
                $('#reviens').val('0.00');
            }
        });


        //onblur china_price
        $("#china_price").blur(function () {
            var china_priceStr = $(this).val();
            var china_price = parseFloat(china_priceStr) || 0;

            var volumeStr = $('#volume').val();
            var volume = parseFloat(volumeStr) || 0;

            var kuaidiStr = $('#kuaidi').val();
            var kuaidi = parseFloat(kuaidiStr) || 0;

            var cuStr = $('#hide_cu').val();
            var cu = parseFloat(cuStr) || 0;

            var idStr = $('#hide_id').val();
            var id = parseFloat(idStr) || 0;

            var crStr = $('#hide_cr').val();
            var cr = parseFloat(crStr) || 0;

            var ctStr = $('#hide_ct').val();
            var ct = parseFloat(ctStr) || 0;

            var ftubStr = $('#hide_ftub').val();
            var ftub = parseFloat(ftubStr) || 0;

            var fcubStr = $('#hide_fcub').val();
            var fcub = parseFloat(fcubStr) || 0;

            var fubStr = $('#hide_fub').val();
            var fub = parseFloat(fubStr) || 0;

            var fret = volume * cu * fub;
            var fretDec = parseFloat(fret).toFixed(2);

            // var taxe = (china_price * cr * (ct + volume) * cu * fcub) * 0.5;
            var taxe = (china_price * cr * ct + volume * cu * fcub) * 0.5;
            var taxeDec = parseFloat(taxe).toFixed(2);

            var reviens1 = (china_price + kuaidi) * cr + taxe + fret;
            var reviens1Dec = parseFloat(reviens1).toFixed(2);

            var reviens2 = (china_price + kuaidi) * cr + volume * ftub;
            var reviens2Dec = parseFloat(reviens2).toFixed(2)

            var reviens_max = Math.max(reviens1, reviens2);
            var reviens_maxDec = parseFloat(reviens_max).toFixed(2)

            var reviens = Math.round(reviens_max);
            var reviensDec = parseFloat(reviens).toFixed(2)

            if (volume != 0) {
                $('#fret').val(fretDec);
            } else {
                $('#fret').val('0.00');
            }
            //alert(taxe);

            if (volume != 0 && china_price != 0) {
                $('#reviens1').val(reviens1Dec);
                $('#taxe').val(taxeDec);
                $('#reviens2').val(reviens2Dec);
                $('#reviens_max').val(reviens_maxDec);
                $('#reviens').val(reviensDec);
            } else {
                $('#reviens1').val('0.00');
                $('#taxe').val('0.00');
                $('#reviens2').val('0.00');
                $('#reviens_max').val('0.00');
                $('#reviens').val('0.00');
            }

        });


//onblur kuaidi
        $("#kuaidi").blur(function () {
            var china_priceStr = $('#china_price').val();
            var china_price = parseFloat(china_priceStr) || 0;

            var volumeStr = $('#volume').val();
            var volume = parseFloat(volumeStr) || 0;

            var kuaidiStr = $(this).val();
            var kuaidi = parseFloat(kuaidiStr) || 0;

            var cuStr = $('#hide_cu').val();
            var cu = parseFloat(cuStr) || 0;

            var ftubStr = $('#hide_ftub').val();
            var ftub = parseFloat(ftubStr) || 0;

            var idStr = $('#hide_id').val();
            var id = parseFloat(idStr) || 0;

            var crStr = $('#hide_cr').val();
            var cr = parseFloat(crStr) || 0;

            var ctStr = $('#hide_ct').val();
            var ct = parseFloat(ctStr) || 0;

            var fcubStr = $('#hide_fcub').val();
            var fcub = parseFloat(fcubStr) || 0;

            var fubStr = $('#hide_fub').val();
            var fub = parseFloat(fubStr) || 0;

            var fret = volume * cu * fub;
            var fretDec = parseFloat(fret).toFixed(2);

            // var taxe = (china_price * cr * (ct + volume) * cu * fcub) * 0.5;
            var taxe = (china_price * cr * ct + volume * cu * fcub) * 0.5;
            var taxeDec = parseFloat(taxe).toFixed(2);

            var reviens1 = (china_price + kuaidi) * cr + taxe + fret;
            var reviens1Dec = parseFloat(reviens1).toFixed(2);

            var reviens2 = (china_price + kuaidi) * cr + volume * ftub;
            var reviens2Dec = parseFloat(reviens2).toFixed(2)

            var reviens_max = Math.max(reviens1, reviens2);
            var reviens_maxDec = parseFloat(reviens_max).toFixed(2)

            var reviens = Math.round(reviens_max);
            var reviensDec = parseFloat(reviens).toFixed(2)

            if (volume != 0) {
                $('#fret').val(fretDec);
            } else {
                $('#fret').val('0.00');
            }
//alert(taxe);

            if (volume != 0 && china_price != 0) {
                $('#reviens1').val(reviens1Dec);
                $('#taxe').val(taxeDec);
                $('#reviens2').val(reviens2Dec);
                $('#reviens_max').val(reviens_maxDec);
                $('#reviens').val(reviensDec);
            } else {
                $('#reviens1').val('0.00');
                $('#taxe').val('0.00');
                $('#reviens2').val('0.00');
                $('#reviens_max').val('0.00');
                $('#reviens').val('0.00');
            }
        });
        //Fret = volume*cours_usd*fret_usd_bateau
        //taxe=(prix_chine*cours_rmb*constante_taxe+volume*cours_usd*fret_company_usd_bateau)*0.5

    });

</script>