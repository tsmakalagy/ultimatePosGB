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
                        <div class="form-group ">
                            <label for="scanner">@lang('lang_v1.scan_barcode')</label>
                            <input type="text" name="my_barcode" id="scanner" class="form-control"/>
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
            var val = $(this).val();
            $.ajax({
                type: 'GET',
                cache: false,
                url: '/my-package/create',
                data: {val: val},
                success: function (response) {

                    window.location = response.url + '?barcode=' + val;

                }
            });
        });

    });

</script>
