$(document).ready(function() {
    if ($('#dashboard_date_filter').length == 1) {
        dateRangeSettings.startDate = moment();
        dateRangeSettings.endDate = moment();
        $('#dashboard_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#dashboard_date_filter span').html(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            update_statistics(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
           // sell_table(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
           sell_table.ajax.reload();

            if ($('#quotation_table').length && $('#dashboard_location').length) {
                quotation_datatable.ajax.reload();
            }
        }); 
            update_statistics(moment().format('YYYY-MM-DD'), moment().format('YYYY-MM-DD'));
        
    }

        //Date range as a button
      var sell_table=$('#sell_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[1, 'desc']],
            "ajax": {
                "url": "/home",
                "data": function ( d ) {
                   
                        var start = $('#dashboard_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#dashboard_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;  
                    
                    d.is_direct_sale = 1;
    
                    if($('#only_subscriptions').is(':checked')) {
                        d.only_subscriptions = 1;
                    }
    
                    d = __datatable_ajax_callback(d);
                }
            },
            scrollY:        "75vh",
            scrollX:        true,
            scrollCollapse: true,
            columns: [
                { data: 'action', name: 'action', orderable: false, "searchable": false},
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'conatct_name', name: 'conatct_name'},
                { data: 'mobile', name: 'contacts.mobile'},
                { data: 'business_location', name: 'bl.name'},
                { data: 'payment_status', name: 'payment_status'},
                { data: 'payment_methods', orderable: false, "searchable": false},
                { data: 'date_paid_on', name: 'date_paid_on'},
                { data: 'final_total', name: 'final_total'},
                { data: 'total_paid', name: 'total_paid', "searchable": false},
                { data: 'total_remaining', name: 'total_remaining'},
                { data: 'return_due', orderable: false, "searchable": false},
                
                { data: 'total_items', name: 'total_items', "searchable": false},
                { data: 'types_of_service_name', name: 'tos.name',visible:false},
                { data: 'service_custom_field_1', name: 'service_custom_field_1',visible:false},
                { data: 'added_by', name: 'u.first_name'},
                { data: 'additional_notes', name: 'additional_notes'},
                { data: 'staff_note', name: 'staff_note'},
                { data: 'shipping_status', name: 'shipping_status'},
                { data: 'shipper_name', name: 'shipper_name'},
                { data: 'shipping_address', name: 'shipping_address'},
                { data: 'delivered_to', name: 'delivered_to'},
                { data: 'location', name: 'location'},
                { data: 'shipping_charges', name: 'shipping_charges'},
                { data: 'shipping_details', name: 'shipping_details'},
                { data: 'shipping_date', name: 'shipping_date'},
                { data: 'table_name', name: 'tables.name',visible:false},
                { data: 'waiter', name: 'ss.first_name',visible:false},
            ],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#sell_table'));
            },
            "footerCallback": function ( row, data, start, end, display ) {
                var footer_sale_total = 0;
                var footer_total_paid = 0;
                var footer_total_remaining = 0;
                var footer_total_sell_return_due = 0;
                for (var r in data){
                    footer_sale_total += $(data[r].final_total).data('orig-value') ? parseFloat($(data[r].final_total).data('orig-value')) : 0;
                    footer_total_paid += $(data[r].total_paid).data('orig-value') ? parseFloat($(data[r].total_paid).data('orig-value')) : 0;
                    footer_total_remaining += $(data[r].total_remaining).data('orig-value') ? parseFloat($(data[r].total_remaining).data('orig-value')) : 0;
                    footer_total_sell_return_due += $(data[r].return_due).find('.sell_return_due').data('orig-value') ? parseFloat($(data[r].return_due).find('.sell_return_due').data('orig-value')) : 0;
                }
    
                $('.footer_total_sell_return_due').html(__currency_trans_from_en(footer_total_sell_return_due));
                $('.footer_total_remaining').html(__currency_trans_from_en(footer_total_remaining));
                $('.footer_total_paid').html(__currency_trans_from_en(footer_total_paid));
                $('.footer_sale_total').html(__currency_trans_from_en(footer_sale_total));
    
                $('.footer_payment_status_count').html(__count_status(data, 'payment_status'));
                $('.service_type_count').html(__count_status(data, 'types_of_service_name'));
                $('.payment_method_count').html(__count_status(data, 'payment_methods'));
            },
            createdRow: function( row, data, dataIndex ) {
                $( row ).find('td:eq(6)').attr('class', 'clickable_td');
            }
        });
    

        $('#only_subscriptions').on('ifChanged', function(event){
            sell_table.ajax.reload();
        });


    $('#dashboard_location').change( function(e) {
        var start = $('#dashboard_date_filter')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');

        var end = $('#dashboard_date_filter')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');

        update_statistics(start, end);
    });

    //atock alert datatables
    var stock_alert_table = $('#stock_alert_table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: '/home/product-stock-alert',
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#stock_alert_table'));
        },
    });
    //payment dues datatables
    var purchase_payment_dues_table = $('#purchase_payment_dues_table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: '/home/purchase-payment-dues',
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#purchase_payment_dues_table'));
        },
    });

    //Sales dues datatables
    var sales_payment_dues_table = $('#sales_payment_dues_table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: '/home/sales-payment-dues',
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#sales_payment_dues_table'));
        },
    });

    //Stock expiry report table
    stock_expiry_alert_table = $('#stock_expiry_alert_table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: {
            url: '/reports/stock-expiry',
            data: function(d) {
                d.exp_date_filter = $('#stock_expiry_alert_days').val();
            },
        },
        order: [[3, 'asc']],
        columns: [
            { data: 'product', name: 'p.name' },
            { data: 'location', name: 'l.name' },
            { data: 'stock_left', name: 'stock_left' },
            { data: 'exp_date', name: 'exp_date' },
        ],
        fnDrawCallback: function(oSettings) {
            __show_date_diff_for_human($('#stock_expiry_alert_table'));
            __currency_convert_recursively($('#stock_expiry_alert_table'));
        },
    });

    if ($('#quotation_table').length) {
        quotation_datatable = $('#quotation_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": '/sells/draft-dt?is_quotation=1',
                "data": function ( d ) {
                    if ($('#dashboard_location').length > 0) {
                        d.location_id = $('#dashboard_location').val();
                    }
                }
            },
            columnDefs: [ {
                "targets": 4,
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'name', name: 'contacts.name'},
                { data: 'business_location', name: 'bl.name'},
                { data: 'action', name: 'action'}
            ]            
        });
    }
});

function update_statistics(start, end) {
    var location_id = '';
    if ($('#dashboard_location').length > 0) {
        location_id = $('#dashboard_location').val();
    }
    var data = { start: start, end: end, location_id: location_id };
    //get purchase details
    var loader = '<i class="fas fa-sync fa-spin fa-fw margin-bottom"></i>';
    $('.total_purchase').html(loader);
    $('.purchase_due').html(loader);
    $('.total_sell').html(loader);
    $('.invoice_due').html(loader);
    $('.total_expense').html(loader);
    $.ajax({
        method: 'get',
        url: '/home/get-totals',
        dataType: 'json',
        data: data,
        success: function(data) {
            //purchase details
            $('.total_purchase').html(__currency_trans_from_en(data.total_purchase, true));
            $('.purchase_due').html(__currency_trans_from_en(data.purchase_due, true));

            //sell details
            $('.total_sell').html(__currency_trans_from_en(data.total_sell, true));
            $('.invoice_due').html(__currency_trans_from_en(data.invoice_due, true));
            //expense details
            $('.total_expense').html(__currency_trans_from_en(data.total_expense, true));
        },
    });
   
}
