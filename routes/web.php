<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

include_once('install_r.php');

Route::middleware(['setData'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

    Auth::routes();

    Route::get('/business/register', 'BusinessController@getRegister')->name('business.getRegister');
    Route::post('/business/register', 'BusinessController@postRegister')->name('business.postRegister');
    Route::post('/business/register/check-username', 'BusinessController@postCheckUsername')->name('business.postCheckUsername');
    Route::post('/business/register/check-email', 'BusinessController@postCheckEmail')->name('business.postCheckEmail');

    Route::get('/invoice/{token}', 'SellPosController@showInvoice')
        ->name('show_invoice');
    Route::get('/quote/{token}', 'SellPosController@showInvoice')
        ->name('show_quote');

    Route::get('/pay/{token}', 'SellPosController@invoicePayment')
        ->name('invoice_payment');
    Route::post('/confirm-payment/{id}', 'SellPosController@confirmPayment')
        ->name('confirm_payment');
});

//Routes for authenticated users only
Route::middleware(['setData', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu', 'CheckUserLogin'])->group(function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/home/get-totals', 'HomeController@getTotals');
    Route::get('/home/product-sales', 'HomeController@ProductSales');
    Route::get('/home/product-stock-alert', 'HomeController@getProductStockAlert');
    Route::get('/home/purchase-payment-dues', 'HomeController@getPurchasePaymentDues');
    Route::get('/home/sales-payment-dues', 'HomeController@getSalesPaymentDues');
    Route::post('/attach-medias-to-model', 'HomeController@attachMediasToGivenModel')->name('attach.medias.to.model');
    Route::get('/calendar', 'HomeController@getCalendar')->name('calendar');
    
    Route::post('/test-email', 'BusinessController@testEmailConfiguration');
    Route::post('/test-sms', 'BusinessController@testSmsConfiguration');
    Route::get('/business/settings', 'BusinessController@getBusinessSettings')->name('business.getBusinessSettings');
    Route::post('/business/update', 'BusinessController@postBusinessSettings')->name('business.postBusinessSettings');
    Route::get('/user/profile', 'UserController@getProfile')->name('user.getProfile');
    Route::post('/user/update', 'UserController@updateProfile')->name('user.updateProfile');
    Route::post('/user/update-password', 'UserController@updatePassword')->name('user.updatePassword');

    Route::resource('brands', 'BrandController');
    
//    Route::resource('payment-account', 'PaymentAccountController');

    Route::resource('tax-rates', 'TaxRateController');

    Route::resource('units', 'UnitController');

    Route::get('/contacts/payments/{contact_id}', 'ContactController@getContactPayments');
    Route::get('/contacts/map', 'ContactController@contactMap');
    Route::get('/contacts/update-status/{id}', 'ContactController@updateStatus');
    Route::get('/contacts/stock-report/{supplier_id}', 'ContactController@getSupplierStockReport');
    Route::get('/contacts/ledger', 'ContactController@getLedger');
    Route::post('/contacts/send-ledger', 'ContactController@sendLedger');
    Route::get('/contacts/import', 'ContactController@getImportContacts')->name('contacts.import');
    Route::post('/contacts/import', 'ContactController@postImportContacts');
    Route::post('/contacts/check-contacts-id', 'ContactController@checkContactId');
    Route::get('/contacts/customers', 'ContactController@getCustomers');
    Route::resource('contacts', 'ContactController');

    Route::get('taxonomies-ajax-index-page', 'TaxonomyController@getTaxonomyIndexPage');
    Route::resource('taxonomies', 'TaxonomyController');

    Route::resource('variation-templates', 'VariationTemplateController');

    Route::get('/products/stock-history/{id}', 'ProductController@productStockHistory');
    Route::get('/delete-media/{media_id}', 'ProductController@deleteMedia');
    Route::post('/products/mass-deactivate', 'ProductController@massDeactivate');
    Route::get('/products/activate/{id}', 'ProductController@activate');
    Route::get('/products/view-product-group-price/{id}', 'ProductController@viewGroupPrice');
    Route::get('/products/add-selling-prices/{id}', 'ProductController@addSellingPrices');
    Route::post('/products/save-selling-prices', 'ProductController@saveSellingPrices');
    Route::post('/products/mass-delete', 'ProductController@massDestroy');
    Route::get('/products/view/{id}', 'ProductController@view');
    Route::get('/products/list', 'ProductController@getProducts');
    Route::get('/products/list-no-variation', 'ProductController@getProductsWithoutVariations');
    Route::post('/products/bulk-edit', 'ProductController@bulkEdit');
    Route::post('/products/bulk-update', 'ProductController@bulkUpdate');
    Route::post('/products/bulk-update-location', 'ProductController@updateProductLocation');
    Route::get('/products/get-product-to-edit/{product_id}', 'ProductController@getProductToEdit');
    
    Route::post('/products/get_sub_categories', 'ProductController@getSubCategories');
    Route::get('/products/get_sub_units', 'ProductController@getSubUnits');
    Route::post('/products/product_form_part', 'ProductController@getProductVariationFormPart');
    Route::post('/products/get_product_variation_row', 'ProductController@getProductVariationRow');
    Route::post('/products/get_variation_template', 'ProductController@getVariationTemplate');
    Route::get('/products/get_variation_value_row', 'ProductController@getVariationValueRow');
    Route::post('/products/check_product_sku', 'ProductController@checkProductSku');
    Route::get('/products/quick_add', 'ProductController@quickAdd');
    Route::post('/products/save_quick_product', 'ProductController@saveQuickProduct');
    Route::get('/products/get-combo-product-entry-row', 'ProductController@getComboProductEntryRow');
    Route::post('/products/toggle-woocommerce-sync', 'ProductController@toggleWooCommerceSync');
    
    Route::resource('products', 'ProductController');
    // Route::get('products/product_sell/{id}','ProductController@productSell');
    // Route::get('test/{id}','ProductSellController@test');
    
    Route::post('/purchases/update-status', 'PurchaseController@updateStatus');
    Route::get('/purchases/get_products', 'PurchaseController@getProducts');
    Route::get('/purchases/get_suppliers', 'PurchaseController@getSuppliers');
    Route::post('/purchases/get_purchase_entry_row', 'PurchaseController@getPurchaseEntryRow');
    Route::post('/purchases/check_ref_number', 'PurchaseController@checkRefNumber');
    Route::resource('purchases', 'PurchaseController')->except(['show']);
    Route::get('/purchases/export/excel/{id}', 'PurchaseController@exportToExcel')->name('export_excel');


    Route::get('/toggle-subscription/{id}', 'SellPosController@toggleRecurringInvoices');
    Route::post('/sells/pos/get-types-of-service-details', 'SellPosController@getTypesOfServiceDetails');
    Route::get('/sells/subscriptions', 'SellPosController@listSubscriptions');
    Route::get('/sells/duplicate/{id}', 'SellController@duplicateSell');
    Route::get('/sells/drafts', 'SellController@getDrafts');
    Route::get('/sells/convert-to-draft/{id}', 'SellPosController@convertToInvoice');
    Route::get('/sells/convert-to-proforma/{id}', 'SellPosController@convertToProforma');
    Route::get('/sells/quotations', 'SellController@getQuotations');
    Route::get('/sells/draft-dt', 'SellController@getDraftDatables');
    Route::resource('sells', 'SellController')->except(['show']);

   

    Route::get('/import-sales', 'ImportSalesController@index');
    Route::post('/import-sales/preview', 'ImportSalesController@preview');
    Route::post('/import-sales', 'ImportSalesController@import');
    Route::get('/revert-sale-import/{batch}', 'ImportSalesController@revertSaleImport');

    Route::get('/sells/pos/get_product_row/{variation_id}/{location_id}', 'SellPosController@getProductRow');
    Route::post('/sells/pos/get_payment_row', 'SellPosController@getPaymentRow');
    Route::post('/sells/pos/get-reward-details', 'SellPosController@getRewardDetails');
    Route::get('/sells/pos/get-recent-transactions', 'SellPosController@getRecentTransactions');
    Route::get('/sells/pos/get-product-suggestion', 'SellPosController@getProductSuggestion');
    Route::get('/sells/pos/get-featured-products/{location_id}', 'SellPosController@getFeaturedProducts');
    Route::resource('pos', 'SellPosController');

    Route::resource('roles', 'RoleController');

    Route::resource('users', 'ManageUserController');

    Route::resource('group-taxes', 'GroupTaxController');

    Route::get('/barcodes/set_default/{id}', 'BarcodeController@setDefault');
    Route::resource('barcodes', 'BarcodeController');

    //Invoice schemes..
    Route::get('/invoice-schemes/set_default/{id}', 'InvoiceSchemeController@setDefault');
    Route::resource('invoice-schemes', 'InvoiceSchemeController');

    //Print Labels
    Route::get('/labels/show', 'LabelsController@show');
    Route::get('/labels/add-product-row', 'LabelsController@addProductRow');
    Route::get('/labels/preview', 'LabelsController@preview');

    //Reports...
    Route::get('/reports/get-stock-by-sell-price', 'ReportController@getStockBySellingPrice');
    Route::get('/reports/purchase-report', 'ReportController@purchaseReport');
    Route::get('/reports/sale-report', 'ReportController@saleReport');
    Route::get('/reports/service-staff-report', 'ReportController@getServiceStaffReport');
    Route::get('/reports/service-staff-line-orders', 'ReportController@serviceStaffLineOrders');
    Route::get('/reports/table-report', 'ReportController@getTableReport');
    Route::get('/reports/profit-loss', 'ReportController@getProfitLoss');
    Route::get('/reports/get-opening-stock', 'ReportController@getOpeningStock');
    Route::get('/reports/purchase-sell', 'ReportController@getPurchaseSell');
    Route::get('/reports/customer-supplier', 'ReportController@getCustomerSuppliers');
    Route::get('/reports/stock-report', 'ReportController@getStockReport');
    Route::get('/reports/stock-details', 'ReportController@getStockDetails');
    Route::get('/reports/tax-report', 'ReportController@getTaxReport');
    Route::get('/reports/tax-details', 'ReportController@getTaxDetails');
    Route::get('/reports/trending-products', 'ReportController@getTrendingProducts');
    Route::get('/reports/expense-report', 'ReportController@getExpenseReport');
    Route::get('/reports/stock-adjustment-report', 'ReportController@getStockAdjustmentReport');
    Route::get('/reports/register-report', 'ReportController@getRegisterReport');
    Route::get('/reports/sales-representative-report', 'ReportController@getSalesRepresentativeReport');
    Route::get('/reports/sales-representative-total-expense', 'ReportController@getSalesRepresentativeTotalExpense');
    Route::get('/reports/sales-representative-total-sell', 'ReportController@getSalesRepresentativeTotalSell');
    Route::get('/reports/sales-representative-total-commission', 'ReportController@getSalesRepresentativeTotalCommission');
    Route::get('/reports/stock-expiry', 'ReportController@getStockExpiryReport');
    Route::get('/reports/stock-expiry-edit-modal/{purchase_line_id}', 'ReportController@getStockExpiryReportEditModal');
    Route::post('/reports/stock-expiry-update', 'ReportController@updateStockExpiryReport')->name('updateStockExpiryReport');
    Route::get('/reports/customer-group', 'ReportController@getCustomerGroup');
    Route::get('/reports/product-purchase-report', 'ReportController@getproductPurchaseReport');
    Route::get('/reports/product-sell-grouped-by', 'ReportController@productSellReportBy');
    Route::get('/reports/product-sell-report', 'ReportController@getproductSellReport');
    Route::get('/reports/product-sell-report-with-purchase', 'ReportController@getproductSellReportWithPurchase');
    Route::get('/reports/product-sell-grouped-report', 'ReportController@getproductSellGroupedReport');
    Route::get('/reports/lot-report', 'ReportController@getLotReport');
    Route::get('/reports/purchase-payment-report', 'ReportController@purchasePaymentReport');
    Route::get('/reports/sell-payment-report', 'ReportController@sellPaymentReport');
    Route::get('/reports/product-stock-details', 'ReportController@productStockDetails');
    Route::get('/reports/adjust-product-stock', 'ReportController@adjustProductStock');
    Route::get('/reports/get-profit/{by?}', 'ReportController@getProfit');
    Route::get('/reports/items-report', 'ReportController@itemsReport');
    Route::get('/reports/get-stock-value', 'ReportController@getStockValue');
    
    Route::get('business-location/activate-deactivate/{location_id}', 'BusinessLocationController@activateDeactivateLocation');

    //Business Location Settings...
    Route::prefix('business-location/{location_id}')->name('location.')->group(function () {
        Route::get('settings', 'LocationSettingsController@index')->name('settings');
        Route::post('settings', 'LocationSettingsController@updateSettings')->name('settings_update');
    });

    //Business Locations...
    Route::post('business-location/check-location-id', 'BusinessLocationController@checkLocationId');
    Route::resource('business-location', 'BusinessLocationController');

    //Invoice layouts..
    Route::resource('invoice-layouts', 'InvoiceLayoutController');

    //Expense Categories...
    Route::resource('expense-categories', 'ExpenseCategoryController');

    //Expenses...
    Route::resource('expenses', 'ExpenseController');

    //Transaction payments...
    // Route::get('/payments/opening-balance/{contact_id}', 'TransactionPaymentController@getOpeningBalancePayments');
    Route::get('/payments/show-child-payments/{payment_id}', 'TransactionPaymentController@showChildPayments');
    Route::get('/payments/view-payment/{payment_id}', 'TransactionPaymentController@viewPayment');
    Route::get('/payments/add_payment/{transaction_id}', 'TransactionPaymentController@addPayment');
    Route::get('/payments/pay-contact-due/{contact_id}', 'TransactionPaymentController@getPayContactDue');
    Route::post('/payments/pay-contact-due', 'TransactionPaymentController@postPayContactDue');
    Route::resource('payments', 'TransactionPaymentController');

    //Printers...
    Route::resource('printers', 'PrinterController');

    Route::get('/stock-adjustments/remove-expired-stock/{purchase_line_id}', 'StockAdjustmentController@removeExpiredStock');
    Route::post('/stock-adjustments/get_product_row', 'StockAdjustmentController@getProductRow');
    Route::resource('stock-adjustments', 'StockAdjustmentController');

    Route::get('/cash-register/register-details', 'CashRegisterController@getRegisterDetails');
    Route::get('/cash-register/close-register/{id?}', 'CashRegisterController@getCloseRegister');
    Route::post('/cash-register/close-register', 'CashRegisterController@postCloseRegister');
    Route::resource('cash-register', 'CashRegisterController');

    //Import products
    Route::get('/import-products', 'ImportProductsController@index');
    Route::post('/import-products/store', 'ImportProductsController@store');

    //Sales Commission Agent
    Route::resource('sales-commission-agents', 'SalesCommissionAgentController');

    //Stock Transfer
    Route::get('stock-transfers/print/{id}', 'StockTransferController@printInvoice');
    Route::post('stock-transfers/update-status/{id}', 'StockTransferController@updateStatus');
    Route::resource('stock-transfers', 'StockTransferController');
    
    Route::get('/opening-stock/add/{product_id}', 'OpeningStockController@add');
    Route::post('/opening-stock/save', 'OpeningStockController@save');

    //Customer Groups
    Route::resource('customer-group', 'CustomerGroupController');

    //Import opening stock
    Route::get('/import-opening-stock', 'ImportOpeningStockController@index');
    Route::post('/import-opening-stock/store', 'ImportOpeningStockController@store');

    //Sell return
    Route::resource('sell-return', 'SellReturnController');
    Route::get('sell-return/get-product-row', 'SellReturnController@getProductRow');
    Route::get('/sell-return/print/{id}', 'SellReturnController@printInvoice');
    Route::get('/sell-return/add/{id}', 'SellReturnController@add');
    
    //Backup
    Route::get('backup/download/{file_name}', 'BackUpController@download');
    Route::get('backup/delete/{file_name}', 'BackUpController@delete');
    Route::resource('backup', 'BackUpController', ['only' => [
        'index', 'create', 'store'
    ]]);

    Route::get('selling-price-group/activate-deactivate/{id}', 'SellingPriceGroupController@activateDeactivate');
    Route::get('export-selling-price-group', 'SellingPriceGroupController@export');
    Route::post('import-selling-price-group', 'SellingPriceGroupController@import');

    Route::resource('selling-price-group', 'SellingPriceGroupController');

    Route::resource('notification-templates', 'NotificationTemplateController')->only(['index', 'store']);
    Route::get('notification/get-template/{transaction_id}/{template_for}', 'NotificationController@getTemplate');
    Route::post('notification/send', 'NotificationController@send');

    Route::post('/purchase-return/update', 'CombinedPurchaseReturnController@update');
    Route::get('/purchase-return/edit/{id}', 'CombinedPurchaseReturnController@edit');
    Route::post('/purchase-return/save', 'CombinedPurchaseReturnController@save');
    Route::post('/purchase-return/get_product_row', 'CombinedPurchaseReturnController@getProductRow');
    Route::get('/purchase-return/create', 'CombinedPurchaseReturnController@create');
    Route::get('/purchase-return/add/{id}', 'PurchaseReturnController@add');
    Route::resource('/purchase-return', 'PurchaseReturnController', ['except' => ['create']]);

    Route::get('/discount/activate/{id}', 'DiscountController@activate');
    Route::post('/discount/mass-deactivate', 'DiscountController@massDeactivate');
    Route::resource('discount', 'DiscountController');

    Route::group(['prefix' => 'account'], function () {
        Route::resource('/account', 'AccountController');
        Route::get('/fund-transfer/{id}', 'AccountController@getFundTransfer');
        Route::post('/fund-transfer', 'AccountController@postFundTransfer');
        Route::get('/deposit/{id}', 'AccountController@getDeposit');
        Route::post('/deposit', 'AccountController@postDeposit');
        Route::get('/close/{id}', 'AccountController@close');
        Route::get('/activate/{id}', 'AccountController@activate');
        Route::get('/delete-account-transaction/{id}', 'AccountController@destroyAccountTransaction');
        Route::get('/get-account-balance/{id}', 'AccountController@getAccountBalance');
        Route::get('/balance-sheet', 'AccountReportsController@balanceSheet');
        Route::get('/trial-balance', 'AccountReportsController@trialBalance');
        Route::get('/payment-account-report', 'AccountReportsController@paymentAccountReport');
        Route::get('/link-account/{id}', 'AccountReportsController@getLinkAccount');
        Route::post('/link-account', 'AccountReportsController@postLinkAccount');
        Route::get('/cash-flow', 'AccountController@cashFlow');
    });
    
    Route::resource('account-types', 'AccountTypeController');

    //Restaurant module
    Route::group(['prefix' => 'modules'], function () {
        Route::resource('tables', 'Restaurant\TableController');
        Route::resource('modifiers', 'Restaurant\ModifierSetsController');

        //Map modifier to products
        Route::get('/product-modifiers/{id}/edit', 'Restaurant\ProductModifierSetController@edit');
        Route::post('/product-modifiers/{id}/update', 'Restaurant\ProductModifierSetController@update');
        Route::get('/product-modifiers/product-row/{product_id}', 'Restaurant\ProductModifierSetController@product_row');

        Route::get('/add-selected-modifiers', 'Restaurant\ProductModifierSetController@add_selected_modifiers');

        Route::get('/kitchen', 'Restaurant\KitchenController@index');
        Route::get('/kitchen/mark-as-cooked/{id}', 'Restaurant\KitchenController@markAsCooked');
        Route::post('/refresh-orders-list', 'Restaurant\KitchenController@refreshOrdersList');
        Route::post('/refresh-line-orders-list', 'Restaurant\KitchenController@refreshLineOrdersList');

        Route::get('/orders', 'Restaurant\OrderController@index');
        Route::get('/orders/mark-as-served/{id}', 'Restaurant\OrderController@markAsServed');
        Route::get('/data/get-pos-details', 'Restaurant\DataController@getPosDetails');
        Route::get('/orders/mark-line-order-as-served/{id}', 'Restaurant\OrderController@markLineOrderAsServed');
        Route::get('/print-line-order', 'Restaurant\OrderController@printLineOrder');
    });

    Route::get('bookings/get-todays-bookings', 'Restaurant\BookingController@getTodaysBookings');
    Route::resource('bookings', 'Restaurant\BookingController');
    
    Route::resource('types-of-service', 'TypesOfServiceController');
    Route::get('sells/edit-shipping/{id}', 'SellController@editShipping');
    Route::put('sells/update-shipping/{id}', 'SellController@updateShipping');
    Route::get('shipments', 'SellController@shipments');

    Route::post('upload-module', 'Install\ModulesController@uploadModule');
    Route::resource('manage-modules', 'Install\ModulesController')
        ->only(['index', 'destroy', 'update']);
    Route::resource('warranties', 'WarrantyController');

    Route::resource('dashboard-configurator', 'DashboardConfiguratorController')
    ->only(['edit', 'update']);

    Route::get('view-media/{model_id}', 'SellController@viewMedia');

    //common controller for document & note
    Route::get('get-document-note-page', 'DocumentAndNoteController@getDocAndNoteIndexPage');
    Route::post('post-document-upload', 'DocumentAndNoteController@postMedia');
    Route::resource('note-documents', 'DocumentAndNoteController');
    Route::resource('purchase-order', 'PurchaseOrderController');
    Route::get('get-purchase-orders/{contact_id}', 'PurchaseOrderController@getPurchaseOrders');
    Route::get('get-purchase-order-lines/{purchase_order_id}', 'PurchaseController@getPurchaseOrderLines');
    Route::get('edit-purchase-orders/{id}/status', 'PurchaseOrderController@getEditPurchaseOrderStatus');
    Route::put('update-purchase-orders/{id}/status', 'PurchaseOrderController@postEditPurchaseOrderStatus');
    Route::resource('sales-order', 'SalesOrderController')->only(['index']);
    Route::get('get-sales-orders/{customer_id}', 'SalesOrderController@getSalesOrders');
    Route::get('get-sales-order-lines', 'SellPosController@getSalesOrderLines');
    Route::get('edit-sales-orders/{id}/status', 'SalesOrderController@getEditSalesOrderStatus');
    Route::put('update-sales-orders/{id}/status', 'SalesOrderController@postEditSalesOrderStatus');
    Route::get('reports/activity-log', 'ReportController@activityLog');

        
    //route pour shipper
    Route::get('shipper', 'ShipperController@index')->name('shipper.index');
    Route::get('shipper/create', 'ShipperController@create')->name('shipper.create');
    Route::post('shipper/store', 'ShipperController@store')->name('shipper.store');
    Route::get('shipper/edit/{id}', 'ShipperController@edit')->name('shipper.edit');
    Route::post('shipper/update/{id}', 'ShipperController@update')->name('shipper.update');
    Route::get('shipper/delete/{id}', 'ShipperController@delete')->name('shipper.delete');
    Route::get('shipper/show/{id}', 'ShipperController@show')->name('shipper.show');

    //ajax tel unique
    Route::post('/validate_mobile/check', 'ContactController@check_validate')->name('contact.check_validate');


    //route for  the shipper type
    Route::get('shipper/type', 'ShipperController@ShipperType')->name('shipper.shipperType');
    Route::get('shipper/type/create', 'ShipperController@createShipperType')->name('shipper.createShipperType');
    Route::post('shipper/type/store', 'ShipperController@storeShipperType')->name('shipper.storeShipperType');
    Route::get('shipper/type/edit/{id}', 'ShipperController@editShipperType')->name('shipper.editShipperType');
    Route::post('shipper/type/update/{id}', 'ShipperController@updateShipperType')->name('shipper.updateShipperType');
    Route::get('shipper/type/delete/{id}', 'ShipperController@deleteShipperType')->name('shipper.deleteShipperType');
    //route for shippers shipperType
    Route::get('get-shippers-shipperType','ShipperController@shipperswithShipperType');

    //route for the shipper sales
    Route::get('get-shipper-sales','ShipperController@shipperSell');
    Route::get('shipper/sales/{id}','ShipperController@shipperSales');

    //route for the test shipping address
    Route::get('test_shipping_address','ShipperController@testAddress');
    Route::post('details','ShipperController@imageStore');

    //route for product price setting
    Route::get('product-price-setting', 'ProductPriceSettingController@index')->name('ProductPriceSetting.index');
    Route::get('product-price-setting/create', 'ProductPriceSettingController@create')->name('ProductPriceSetting.create');
    Route::post('product-price-setting/store', 'ProductPriceSettingController@store')->name('ProductPriceSetting.store');
    Route::get('product-price-setting/edit/{id}', 'ProductPriceSettingController@edit')->name('ProductPriceSetting.edit');
    Route::post('product-price-setting/update/{id}', 'ProductPriceSettingController@update')->name('ProductPriceSetting.update');
    Route::get('product-price-setting/delete/{id}', 'ProductPriceSettingController@delete')->name('ProductPriceSetting.delete');
    Route::get('product-price-setting/show/{id}', 'ProductPriceSettingController@show')->name('ProductPriceSetting.show');

    //route for product price
    Route::get('product-price', 'ProductPriceController@index')->name('ProductPrice.index');
    Route::get('product-price/create', 'ProductPriceController@create')->name('ProductPrice.create');
    Route::post('product-price/store', 'ProductPriceController@store')->name('ProductPrice.store');
    Route::get('product-price/edit/{id}', 'ProductPriceController@edit')->name('ProductPrice.edit');
    Route::post('product-price/update/{id}', 'ProductPriceController@update')->name('ProductPrice.update');
    Route::get('product-price/delete/{id}', 'ProductPriceController@delete')->name('ProductPrice.delete');
    Route::get('product-price/show/{id}', 'ProductPriceController@show')->name('ProductPrice.show');


    //route for my_package
    Route::get('my-package', 'PackageController@index')->name('Package.index');
    Route::get('my-package/create', 'PackageController@create')->name('Package.create');
    Route::post('my-package/store', 'PackageController@store')->name('Package.store');
    Route::get('my-package/edit/{id}', 'PackageController@edit')->name('Package.edit');
    Route::post('my-package/update/{id}', 'PackageController@update')->name('Package.update');
    Route::get('my-package/delete/{id}', 'PackageController@delete')->name('Package.delete');
    Route::get('my-package/show/{id}', 'PackageController@show')->name('Package.show');
    Route::get('my-package/scan', 'PackageController@scan')->name('Package.scan');
    Route::get('my-package/upload-img/{id}', 'PackageController@uploadImg')->name('Package.upload_img');
    Route::post('my-package/save-img/{id}', 'PackageController@saveImg')->name('Package.save_img');

     //route for the_package
    Route::get('the-package', 'ThePackageController@index')->name('ThePackage.index');
    Route::get('the-package/create', 'ThePackageController@create')->name('ThePackage.create');
    Route::post('the-package/store', 'ThePackageController@store')->name('ThePackage.store');
    Route::get('the-package/edit/{id}', 'ThePackageController@edit')->name('ThePackage.edit');
    Route::post('the-package/update/{id}', 'ThePackageController@update')->name('ThePackage.update');
    Route::get('the-package/delete/{id}', 'ThePackageController@delete')->name('ThePackage.delete');
    Route::get('the-package/show/{id}', 'ThePackageController@show')->name('ThePackage.show');
    Route::get('the-package/scan', 'ThePackageController@scan')->name('ThePackage.scan');
    Route::get('the-package/upload-img/{id}', 'ThePackageController@uploadImg')->name('ThePackage.upload_img');
    Route::post('the-package/save-img/{id}', 'ThePackageController@saveImg')->name('ThePackage.save_img');
    Route::get('the-package/get-package', 'ThePackageController@getPackage')->name('ThePackage.get_package');
    Route::get('the-package/get-package-row', 'ThePackageController@getPackageRow')->name('ThePackage.get_package_row');

      //route for packing_list
      Route::get('packing-list', 'packingListController@index')->name('packingList.index');
      Route::get('packing-list/create', 'packingListController@create')->name('packingList.create');
      Route::post('packing-list/store', 'packingListController@store')->name('packingList.store');
      Route::get('packing-list/edit/{id}', 'packingListController@edit')->name('packingList.edit');
      Route::post('packing-list/update/{id}', 'packingListController@update')->name('packingList.update');
      Route::get('packing-list/delete/{id}', 'packingListController@delete')->name('packingList.delete');
      Route::get('packing-list/show/{id}', 'packingListController@show')->name('packingList.show');
      Route::get('packing-list/scan', 'packingListController@scan')->name('packingList.scan');
      Route::get('packing-list/upload-img/{id}', 'packingListController@uploadImg')->name('packingList.upload_img');
      Route::post('packing-list/save-img/{id}', 'packingListController@saveImg')->name('packingList.save_img');
      Route::get('packing-list/get-package', 'packingListController@getPackage')->name('packingList.get_package');
      Route::get('packing-list/get-package-row', 'packingListController@getPackageRow')->name('packingList.get_package_row');
      Route::get('packing-list/get-the-package-row', 'packingListController@getThePackageRow')->name('packingList.get_the_package_row');
      Route::get('packing-list/print/{id}', 'packingListController@printInvoice')->name('packingList.printInvoice');
      Route::get('/packing-list/export/excel/{id}', 'packingListController@exportToExcel')->name('packingListexport_excel');
      Route::get('packing-list/list-the-package', 'PackingListController@listThePackage')->name('packingList.list_the_package');
  
    //route for shipping_fee
    Route::get('shipping-fee', 'ShippingFeeController@index')->name('Shipping_fee.index');
    Route::get('shipping-fee/create', 'ShippingFeeController@create')->name('shipping_fee.create');
    Route::post('shipping-fee/store', 'ShippingFeeController@store')->name('Shipping_fee.store');
    Route::get('shipping-fee/edit/{id}', 'ShippingFeeController@edit')->name('Shipping_fee.edit');
    Route::post('shipping-fee/update/{id}', 'ShippingFeeController@update')->name('Shipping_fee.update');
    Route::get('shipping-fee/delete/{id}', 'ShippingFeeController@delete')->name('Shipping_fee.delete');
    Route::get('shipping-fee/show/{id}', 'ShippingFeeController@show')->name('Shipping_fee.show');


     //route for sell_transaction
     Route::get('sell-transaction', 'SellTransactionController@index')->name('Sell_transaction.index');
     Route::get('sell-transaction/create', 'SellTransactionController@create')->name('Sell_transaction.create');
     Route::post('sell-transaction/store', 'SellTransactionController@store')->name('Sell_transaction.store');
     Route::get('sell-transaction/edit/{id}', 'SellTransactionController@edit')->name('Sell_transaction.edit');
     Route::post('sell-transaction/update/{id}', 'SellTransactionController@update')->name('Sell_transaction.update');
     Route::get('sell-transaction/delete/{id}', 'SellTransactionController@delete')->name('Sell_transaction.delete');
     Route::get('sell-transaction/show/{id}', 'SellTransactionController@show')->name('Sell_transaction.show');
    
     Route::get('sell-transaction/list-the-package', 'SellTransactionController@listThePackage')->name('Sell_transaction.list_the_package');
     Route::get('sell-transaction/edit-list-the-package', 'SellTransactionController@editListThePackage')->name('Sell_transaction.edit_list_the_package');
     Route::get('sell-transaction/print/{id}', 'SellTransactionController@printInvoice')->name('Sell_transaction.printInvoice');
     
     //  Route::get('sell_transaction/scan', 'PackageController@scan')->name('Package.scan');
    //  Route::get('sell_transaction/upload-img/{id}', 'PackageController@uploadImg')->name('Package.upload_img');
    //  Route::post('sell_transaction/save-img/{id}', 'PackageController@saveImg')->name('Package.save_img');

    //Route for the products sales
    Route::get('get-product-sales','ProductController@productSell');
//    Route::get('test/{id}','ProductSellController@test');
});


Route::middleware(['EcomApi'])->prefix('api/ecom')->group(function () {
    Route::get('products/{id?}', 'ProductController@getProductsApi');
//    Route::get('categories', 'CategoryController@getCategoriesApi');
    Route::get('brands', 'BrandController@getBrandsApi');
    Route::post('customers', 'ContactController@postCustomersApi');
    Route::get('settings', 'BusinessController@getEcomSettings');
    Route::get('variations', 'ProductController@getVariationsApi');
    Route::post('orders', 'SellPosController@placeOrdersApi');
});

//common route
Route::middleware(['auth'])->group(function () {
    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
});

Route::middleware(['setData', 'auth', 'SetSessionData', 'language', 'timezone'])->group(function () {
    Route::get('/load-more-notifications', 'HomeController@loadMoreNotifications');
    Route::get('/get-total-unread', 'HomeController@getTotalUnreadNotifications');
    Route::get('/purchases/print/{id}', 'PurchaseController@printInvoice');
    Route::get('/purchases/{id}', 'PurchaseController@show');
    Route::get('/download-purchase-order/{id}/pdf', 'PurchaseOrderController@downloadPdf')->name('purchaseOrder.downloadPdf');
    Route::get('/sells/{id}', 'SellController@show');
    Route::get('/sells/{transaction_id}/print', 'SellPosController@printInvoice')->name('sell.printInvoice');
    Route::get('/download-sells/{transaction_id}/pdf', 'SellPosController@downloadPdf')->name('sell.downloadPdf');
    Route::get('/download-quotation/{id}/pdf', 'SellPosController@downloadQuotationPdf')
        ->name('quotation.downloadPdf');
    Route::get('/download-packing-list/{id}/pdf', 'SellPosController@downloadPackingListPdf')
        ->name('packing.downloadPdf');
    Route::get('/sells/invoice-url/{id}', 'SellPosController@showInvoiceUrl');
    Route::get('/show-notification/{id}', 'HomeController@showNotification');
});

