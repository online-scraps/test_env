<?php

use App\Http\Controllers\Admin\MstSequenceCrudController;
use App\Models\MstSubcategory;
use App\Models\PurchaseOrderDetail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MstSubcategoryCrudController;
use App\Http\Controllers\Admin\PurchaseOrderDetailCrudController;
use App\Http\Controllers\Auth\UserCrudController;
use App\Http\Controllers\Admin\StockEntriesCrudController;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.


Route::group(
    [
        'namespace'  => 'App\Http\Controllers\Auth',
        'middleware' => 'web',
        'prefix'     => config('backpack.base.route_prefix'),
    ],
    function () {
        Route::get('login', 'LoginController@showLoginForm')->name('backpack.auth.login');
        Route::get('logout', 'LoginController@logout')->name('backpack.auth.logout');

        Route::post('login', 'LoginController@login');
        Route::post('logout', 'LoginController@logout');
    }
);


Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes

    Route::crud('mst-unit', 'MstUnitCrudController');
    Route::crud('mst-disc-mode', 'MstDiscModeCrudController');
    Route::crud('mst-category', 'MstCategoryCrudController');

    Route::crud('mst-subcategory', 'MstSubcategoryCrudController');
    Route::get('api/mstsubcategory/{category_id}', [MstSubcategoryCrudController::class, 'getSubCategoryAPI']);

    Route::crud('mst-supplier', 'MstSupplierCrudController');
    Route::crud('mst-country', 'MstCountryCrudController');
    Route::crud('mst-province', 'MstProvinceCrudController');
    Route::crud('mst-district', 'MstDistrictCrudController');
    Route::crud('mst-brand', 'MstBrandCrudController');
    Route::crud('mst-gender', 'MstGenderCrudController');
    Route::crud('mst-position', 'MstPositionCrudController');
    Route::crud('mst-department', 'MstDepartmentCrudController');
    Route::crud('mst-relation', 'MstRelationCrudController');

    Route::crud('mst-item', 'MstItemCrudController');
    // Bulk Upload for Items (Excel Upload)
    Route::post('mst-item/excel-import', 'MstItemCrudController@itemEntriesExcelImport')
    ->name('item.importExcel');

    Route::crud('sup-organization', 'SupOrganizationCrudController');
    Route::crud('hr-employee', 'HrEmployeeCrudController');

    Route::crud('mst-store', 'MstStoreCrudController');
    Route::crud('return-reason', 'ReturnReasonCrudController');

    Route::crud('sales', 'SalesCrudController');
    Route::get('sales/{id}/Invoice', 'SalesCrudController@printInvoice')->name('sales.pdfInvoice');
    Route::get('sales/{id}/InvoiceNoHeader', 'SalesCrudController@printInvoiceNoHeader')->name('sales.printInvoiceNoHeader');
    Route::get('sales/{id}/ReturnInvoice', 'SalesCrudController@printSalesReturnInvoice')->name('sales.printSalesReturnInvoice');
    Route::get('sales/{id}/ReturnInvoiceNoHeader', 'SalesCrudController@printSalesReturnInvoiceNoHeader')->name('sales.printSalesReturnInvoiceNoHeader');
    Route::get('sales/{id}/showReturn', 'SalesCrudController@showReturn')->name('sales.showReturn');

    Route::crud('menu-item', 'MenuItemCrudController');

    Route::crud('purchase-order-type', 'PurchaseOrderTypeCrudController');
    Route::crud('purchase-order-detail', 'PurchaseOrderDetailCrudController');

    Route::crud('sup-status', 'SupStatusCrudController');
    Route::crud('purchase-item', 'PurchaseItemCrudController');
    Route::crud('grn', 'GrnCrudController');
    Route::get('purchase-return-grn/{id}', 'GrnCrudController@purchaseReturn')->name('purchase-return-grn');
    Route::get('grn-item-details/{item}', 'GrnCrudController@grnDetails')->name('custom.grn-details');
    // Route::get('grn-history-details/{id}/{to}/{from}', 'GrnCrudController@grnHistoryDetails')->name('custom.grnh-details');
    Route::get('search-grn/{id}/{to}/{from}', 'GrnCrudController@grnItemHistory')->name('custom.grn-item-search');
    Route::get('search-po/{po_no}/{from}/{to}/{supplier}/{po_type}', 'GrnCrudController@poItemFetchForGrn')->name('custom.po-item-search');
    Route::get('get-podetails/{po_no}', 'GrnCrudController@fetchPODforGRN')->name('custom.get-pod-for-grn');



    Route::crud('grn-item', 'GrnItemCrudController');
    Route::crud('purchase-return', 'PurchaseReturnCrudController');
    Route::crud('purchase-return-item', 'PurchaseReturnItemCrudController');
    Route::crud('stock-entries', 'StockEntriesCrudController');

    Route::get('stock-status', 'StockEntriesCrudController@stockStatus');

    Route::get('stock-status/List-pdf', 'StockEntriesCrudController@listStatusPdfDownload')
        ->name('stock.exportPdf');

    Route::get('stock-status/List-excel', 'StockEntriesCrudController@listStatusExcelDownload')
        ->name('stock.exportExcel');

    Route::post('stock-entries/excel-import', 'StockEntriesCrudController@stockEntriesImportExcel')
        ->name('stock.importExcel');

    Route::get('stock-item/{item}', 'StockEntriesCrudController@stockItem')->name('custom.stock-item');
    Route::get('barcode-report-details/{num}', 'StockEntriesCrudController@getBarcodeDetail')->name('custom.barcode-report-details');
    Route::crud('batch-no', 'BatchNoCrudController');
    Route::post('stock-entries-barcode', 'StockEntriesCrudController@getSplitedBarcode')->name('custom.barcode-split');
    //api
    Route::get('get-batch/{itemId}', 'SalesCrudController@getBatchItem')->name('custom.get-batch');
    Route::get('get-batch-detail/{itemId}/{batchId}', 'SalesCrudController@getBatchDetail')->name('custom.get-batch-detail');
    Route::get('get-batch-item-detail/{itemId}/{batchNo}', 'PurchaseReturnCrudController@getBatchDetail')->name('custom.get-batch-item-detail');
    Route::get('get-total/{item}', 'SalesCrudController@stockItem')->name('custom.get-total');
    Route::get('sales-bill-history/{detail}/{to}/{from}', 'SalesCrudController@getSalesHistoryDetails')->name('custom.sales-bill-history');
    Route::get('sales/{id}/edit', 'SalesCrudController@edit')->name('custom.sales-edit');
    Route::post('sale-barcode-details/{stockItem}/{batchId}', 'SalesCrudController@barcodeSessionStore')->name('custom.sale-barcode');

    //TODO: Sales Return Route
    Route::get('sales-return/{id}', 'SalesCrudController@editSalesReturn')->name('custom.sales-return');
    Route::post('sales-return/{id}', 'SalesCrudController@storeSalesReturn')->name('custom.sales-return-store');
    Route::post('sales-return-barcode-details/{stockItem}', 'SalesCrudController@retrunSessionStore')->name('custom.sale-barcode-return');


    // this should be the absolute last line of this file
    Route::get('search-stock/{id}/{to}/{from}', 'StockEntriesCrudController@StockItemHistory')->name('custom.stock-item-search');
    Route::post('stock-barcode-details/{stockItem}', 'StockEntriesCrudController@barcodeSessionStore')->name('custom.stock-barcode');
    Route::get('stock-barcode-details/flush/{key}', 'StockEntriesCrudController@barcodeSessionFlush')->name('custom.stock-barcode-flush');


    Route::get('po-item-details/{item}', 'PurchaseOrderDetailCrudController@poDetails')->name('custom.po-details');
    Route::get('purchase-history-details/{id}/{to}/{from}', 'PurchaseOrderDetailCrudController@purchaseOrderHistoryDetails')->name('custom.poh-details');
    Route::get('get-contact-details/{detail}', 'PurchaseOrderDetailCrudController@getContactDetails')->name('custom.contact-details');


    Route::crud('payment-mode', 'PaymentModeCrudController');
    Route::crud('batch-no', 'BatchNoCrudController');
    Route::crud('grn-types', 'GrnTypesCrudController');
    Route::crud('app-setting', 'AppSettingCrudController');

    Route::get('api/mststore/{sup_org_id}', [UserCrudController::class, 'getStoreListAPI']);
    Route::get('barcode-report', [StockEntriesCrudController::class, 'barcodeReport']);

    Route::crud('fixed-asset-entries', 'FixedAssetEntriesCrudController');
    Route::get('api/childstore', [UserCrudController::class, 'getchildStoreListAPI']);
    Route::get('barcode-report',[StockEntriesCrudController::class,'barcodeReport']);

    // journal voucher
    Route::crud('journal-voucher', 'JournalVoucherCrudController');
    Route::get('journal-voucher/get-voucher-series', 'JournalVoucherCrudController@getVoucherSeries')->name('get-voucher-series');

    Route::crud('series-number', 'SeriesNumberCrudController');
    Route::crud('mst-fiscal-year', 'MstFiscalYearCrudController');
    Route::crud('system-configuration', 'SystemConfigurationCrudController');

    Route::crud('charts-of-account', 'ChartsOfAccountCrudController');
    Route::get('charts-of-account/create-group', 'ChartsOfAccountCrudController@createGroup')->name('createGroup');
    Route::get('charts-of-account/get-group-data', 'ChartsOfAccountCrudController@getGroupData')->name('getGroupData');
    Route::get('charts-of-account/{id}/get-group-info', 'ChartsOfAccountCrudController@getGroupInfo')->name('getGroupInfo');

    Route::crud('purchase-type-master', 'PurchaseTypeMasterCrudController');
    Route::crud('sales-type-master', 'SalesTypeMasterCrudController');
    Route::crud('mst-customer', 'MstCustomerCrudController');

    Route::crud('bill-sundry', 'BillSundryCrudController');
    //?? API Route to get customers in Modal's select
    Route::get('/api/customer/{id}', 'MstCustomerCrudController@getCustomerDetailById')->name('api.customerDetail');
    
    Route::crud('contra-voucher', 'ContraVoucherCrudController');
    Route::crud('payment-voucher', 'PaymentVoucherCrudController');
    Route::crud('receipt-voucher', 'ReceiptVoucherCrudController');
    Route::get('/api/customer/{name}', 'MstCustomerCrudController@getCustomerDetailByName')->name('api.customerDetailName');
    Route::get('/api/customer/company/{companyname}', 'MstCustomerCrudController@getCustomerDetailByCompanyName')->name('api.customerCompanyName');
    Route::crud('mst-sequence', 'MstSequenceCrudController');
    Route::crud('currencies', 'CurrenciesCrudController');
    Route::crud('currency-conversion', 'CurrencyConversionCrudController');

    // accoutn setting
    Route::crud('account-setting', 'AccountSettingCrudController');
    Route::post('account-setting/image-note-configure', 'AccountSettingCrudController@saveImageNoteConfigure')->name('saveImageNoteConfigure');
    Route::post('account-setting/image-note-configure/{image_note_configure_id}', 'AccountSettingCrudController@updateImageNoteConfigure')->name('updateImageNoteConfigure');
    
    Route::crud('voucher-group-setting', 'VoucherGroupSettingCrudController');

    //Stock transfer
    Route::crud('stock-transfer', 'StockTransferCrudController');
    //Stock transfer BARCODE SESSION STORE
    Route::post('stock-transfer-barcode-details/{stockItem}/{batchId}', 'StockTransferCrudController@barcodeSessionStore')->name('custom.stock-transfer-barcode');
    Route::get('stock-transfer-get-store-except/{id}', 'StockTransferCrudController@getStoreListExcept')->name('stockTransfer.getToStore');

    Route::crud('sales-order-voucher', 'SalesOrderVoucherCrudController');
    Route::get('sales-order-voucher/{id}/Invoice', 'SalesOrderVoucherCrudController@printInvoice')->name('sales-order.pdfInvoice');
    Route::crud('sales-order-voucher-items', 'SalesOrderVoucherItemsCrudController');

    Route::get('sales-order-item/{item}', 'SalesOrderVoucherCrudController@stockItem')->name('custom.sales-order-item');
    Route::get('get-total-sales-order/{item}', 'SalesOrderVoucherCrudController@stockItem')->name('custom.get-total-sales-order');

    //Sequence number check for duplicate batch number
    Route::get('/mst-sequence/sequence-code-check', [MstSequenceCrudController::class, 'sequenceCodeCheck'])->name('sequence.code-check');
    Route::post('/mst-sequence/inline-create', [MstSequenceCrudController::class, 'inlineStore'])->name('sequence.inlineStore');
    Route::crud('activity', 'ActivityLogCrudController');
    Route::crud('session-log', 'SessionLogCrudController');
    Route::crud('activity-log', 'ActivityLogCrudController');
}); // this should be the absolute last line of this file
