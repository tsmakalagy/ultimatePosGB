<div class="modal-dialog modal-sm" id="my_modal" role="document">
    <div class="modal-content" id="my_modal_content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle">@lang('lang_v1.package')</h4>
        </div>
        <div class="modal-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 ">
{{--                        <div class="form-group ">--}}
{{--                            <label for="scanner">@lang('lang_v1.scan_barcode')</label>--}}
{{--                            <input type="text" name="my_barcode" id="scanner" class="form-control"/>--}}
{{--                        </div>--}}
                        <div class="form-group">
                            {!! Form::label('packages', __('lang_v1.package') . ':') !!}
                            {!! Form::select('packages', $package,null,['class' => 'form-control my-select2',  'id' => 'product_locations','placeholder' => __('messages.please_select'),'required']); !!}
                            {{-- {!! Form::select('packages[]', $package,null,['class' => 'form-control select2', 'multiple', 'id' => 'product_locations','required']); !!} --}}

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="modal-footer">

            <button type="button" class="btn btn-default no-print"
                    data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
    </div>
</div>



<script type="text/javascript">
    $(document).ready(function () {

        $('.my-select2').on('select2:select', function (e) {
            var data =  e.params.data;
            var barcode = data.text;

            if (barcode == '') {
                return;  // Do nothing if input field is empty
            }
            $.ajax({
                type: 'GET',
                cache: false,
                url: '/the-package/get-package-row',
                data: { barcode: barcode },
                success: function (response) {
                    $('#my_modal .close').click();
                    $('#the_package_add_parcel_form_part tbody').append(response);
                }
            });
        });

        $('.my-select2').select2({
            minimumResultsForSearch: 2,
            placeholder: LANG.search,
        });


        $('#the_package_add_parcel_form_part tr').each(function(e) {
            var id = $(this).data('id');
            if (id === 'undefined') {
                return;
            }
            $(".my-select2 option").each(function() {
                var thisOptionValue = $(this).val();
                if (thisOptionValue == id) {
                    $(this).prop('disabled', ! $(this).prop('disabled'));
                    $(".my-select2").select2();
                }
            });
        });

    });

</script>
