<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Sales;
use App\Models\BatchNo;
use App\Models\GrnItem;
use App\Models\MstItem;
use App\Models\MstUnit;
use App\Utils\PdfPrint;
use App\Models\SaleItems;
use App\Models\SupStatus;
use App\Models\AppSetting;
use App\Models\MstCustomer;
use App\Models\MstDiscMode;
use App\Models\MstSequence;
use App\Utils\NumberToWords;
use Illuminate\Http\Request;
use App\Models\InvoiceSequence;
use App\Base\BaseCrudController;
use App\Models\StockItemDetails;
use App\Models\SalesItemsDetails;
use App\Models\ItemQuantityDetail;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;
use App\Http\Requests\SalesRequest;
use App\Models\BatchQuantityDetail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SalesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SalesCrudController extends BaseCrudController
{

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     *
     * @return void
     */
    private $salesEntries;
    private $salesItems;
    private $barcodeDetails;
    private $multiple_barcode;

    protected $user;
    public function __construct(Sales $salesEntries, SaleItems $salesItems, StockItemDetails $barcodeDetails)
    {
        parent::__construct();

        $this->salesEntries = $salesEntries;
        $this->salesItems = $salesItems;
        $this->barcodeDetails = $barcodeDetails;
    }

    public function setup()
    {

        CRUD::setModel(\App\Models\Sales::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/sales');
        CRUD::setEntityNameStrings('', 'Billing');
        $this->user = backpack_user();
        // $this->crud->enableExportButtons();
        $this->crud->enableResponsiveTable();
        $this->multiple_barcode = backpack_user()->superOrganizationEntity->multiple_barcode;
        $this->isAllowed([
            'stockItem' => 'list',
            'getBatchDetail' => 'list',
            'getBatchItem' => 'list',
            'barcodeSessionStore' => 'list',
            'barcodeSessionFlush' => 'list',
            'setSessions' => 'list'
        ]);
        // $this->filterDataByStoreUser(["sup_org_id" => $this->user->sup_org_id, "store_id" => $this->user->store_id]);

    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $cols = [
            [
                'name' => 'customer_id',
                'type' => 'model_function',
                'label' => 'Full Name',
                'function_name' => 'getCustomerName',
            ],
            [
                'name' => 'bill_no',
                'type' => 'model_function',
                'label' => 'Bill Number',
                'function_name' => 'getBill',
            ],
            [
                'name' => 'return_bill_no',
                'type' => 'model_function',
                'label' => 'Return Number',
                'function_name' => 'getReturnNumber',
            ],
            [
                'name' => 'bill_date_ad',
                'type' => 'text',
                'label' => 'Billed Date',
            ],
            [
                'name' => 'payment_type',
                'type' => 'select_from_array',

                'label' => 'Payment Type',
                'options' => [
                    1 => "Cash",
                    '2' => 'Cheque',
                    '3' => 'Due'
                ]
            ],
            [
                'name' => 'net_amt',
                'type' => 'number',
                'label' => ' Net Amt',
            ],
            [
                'name' => 'paid_amt',
                'type' => 'number',
                'label' => ' Paid Amt',
            ],
            [
                'name' => 'due_amt',
                'type' => 'number',
                'label' => ' Due Amt',
            ],
            [
                'name' => 'status_id',
                'type' => 'select_from_array',
                'label' => ' Status',
                'options' => [
                    1 => "Saved",
                    '2' => 'Approved',
                    '3' => 'Cancelled',
                    '4' => 'Partial Return',
                    '5' => 'Full Return'
                ]
            ],
            [
                'label'     => 'Organization',
                'type'      => 'select',
                'name'      => 'sup_org_id', // the column that contains the ID of that connected entity;
                'entity'    => 'superOrganizationEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_en', // foreign key attribute that is shown to user
                'model'     => SupOrganization::class
            ],
        ];

        $this->crud->addColumns(array_filter($cols));
        $this->filterListByUserLevel();

        $this->crud->addButtonFromModelFunction('line', 'printInvoice', 'printInvoice', 'beginning');
        $this->crud->addButtonFromModelFunction('line', 'printInvoiceNoHeader', 'printInvoiceNoHeader', 'beginning');

        // if($this->crud->addClause('where','status_id', 2)){
            $this->crud->addButtonFromView('line', 'show', 'show', 'beginning');
        // }

        $this->crud->addButtonFromModelFunction('line', 'salesReturn', 'salesReturn', 'end');
        $this->crud->addButtonFromModelFunction('line', 'showReturnButton', 'showReturnButton', 'end');
        $this->crud->addButtonFromModelFunction('line', 'printReturnsInvoice', 'printReturnsInvoice', 'end');
        $this->crud->addButtonFromModelFunction('line', 'printReturnsInvoiceNoHeader', 'printReturnsInvoiceNoHeader', 'end');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @return void
     */

    public function create()
    {
        session()->forget('barcode');

        $this->crud->hasAccessOrFail('create');
        $item_lists = $this->getItemList();
        $discount_modes = MstDiscMode::all();
        $discount_approver = User::where('sup_org_id', $this->user->sup_org_id)
            ->where('store_id', $this->user->store_id)
            ->where('is_discount_approver', true)->get();
        $due_approver = User::where('sup_org_id', $this->user->sup_org_id)
            ->where('store_id', $this->user->store_id)
            ->where('is_due_approver', true)->get();

        $this->data['crud'] = $this->crud;
        $this->data['item_lists'] = $item_lists;

        $this->data['discount_modes'] = $discount_modes;
        $this->data['discount_approver'] = $discount_approver;
        $this->data['due_approver'] = $due_approver;
        $this->data['multiple_barcode'] = $this->multiple_barcode;

        $this->data['customers'] = MstCustomer::where([['is_customer', true], ['is_active', true], ['is_coorporate', false]])->get();
        $this->data['coorporateCustomers'] = MstCustomer::where([['is_customer', true], ['is_active', true], ['is_coorporate', true]])->get()->unique('company_name');

        // Json Categories
        $this->data['jsonCustomers'] = json_encode(
            MstCustomer::where([['is_customer', true], ['is_active', true]])
                ->pluck('name_en')
        );

        $this->data['jsonCustomersCompanyName'] = json_encode(
            MstCustomer::where([['is_customer', true], ['is_active', true], ['is_coorporate', true]])
                ->pluck('company_name')
        );

        $this->data['sequenceCodes'] = $this->sequence_type();
        $this->data['invoiceSequences'] = $this->getsequenceCode(3);

        return view('customAdmin.sales.sales_form', $this->data);
    }

    public function store()
    {
        $request = $this->crud->validateRequest();

        $this->crud->hasAccessOrFail('create');
        $request = $this->crud->validateRequest();
        if (isset($request)) {
            $salesInput = $request->only([
                'bill_date_ad',
                'bill_no',
                'discount_type',
                'item_id',
                'batch_qty_detail_id',
                'item_qty_detail_id',
                'total_qty',
                'batch_no',
                'batch_qty',
                'item_price',
                'tax_vat',
                'item_discount',
                'item_total',
                'store_id',
                'discount',
                'receipt_amt',
                'discount_amt',
                'gross_amt',
                'taxable_amt',
                'total_tax_vat',
                'net_amt',
                'paid_amt',
                'refund',
                'remarks',
                'payment_type',
                'discount_approver_id',
                'status_id',
                'transaction_date_ad',
                'bank_name',
                'cheque_number',
                'ac_holder_name',
                'branch_name',
                'cheque_date',
                'cheque_upload',
                'due_approver_id'
            ]);

            $customerInput = $request->only([
                'bill_type',
                'full_name',
                'hidden_bill_type',
                'hidden_gender_id',
                'customer_id',
                'address',
                'contact_number',
                'company_name',
                'pan_vat',
            ]);

            $sequenceCodes = $request->only('invoice_sequence');

            $statusCheck = $request->status_id == SupStatus::APPROVED;
            if ($statusCheck) {
                if(!array_key_exists('invoice_sequence', $sequenceCodes)){
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Failed to approve sale. Invoice Sequence is not created."
                    ]);
                }
                $salesInput['bill_no'] = $sequenceCodes['invoice_sequence'];
                $salesInput['bill_date_ad'] = dateToday();
                $salesInput['bill_date_bs'] = convert_bs_from_ad($salesInput['bill_date_ad']);
            }

            $salesInput['store_id'] = $this->user->store_id;
            $salesInput['sup_org_id'] = $this->user->sup_org_id;
            if ($request->payment_type == 2) {
                $salesInput['discount_approver_id'] = $request->discount_approver_id;
                $salesInput['due_approver_id'] = null;
            } elseif ($request->payment_type == 3) {
                $salesInput['due_approver_id'] = $request->due_approver_id;
                $salesInput['discount_approver_id'] = null;
            } else {
                $salesInput['discount_approver_id'] = null;
                $salesInput['due_approver_id'] = null;
            }
            if($customerInput['bill_type'] == 1){
                $bill_type = false;
            }else{
                $bill_type = true;
            }

            try {
                DB::beginTransaction();
                if($customerInput['customer_id']){
                    $customer = MstCustomer::find($customerInput['customer_id']);
                }else{
                    $customer = new MstCustomer();
                }
                $customer->is_customer = true;
                $customer->is_coorporate = $bill_type;
                $customer->name_en = $customerInput['full_name'];
                $customer->address = $customerInput['address'];
                $customer->contact_number = $customerInput['contact_number'];
                $customer->company_name = $customerInput['company_name'];
                $customer->pan_no = $customerInput['pan_vat'];
                $customer->sup_org_id = $this->user->sup_org_id;
                $customer->save();
                $salesInput['customer_id'] = $customer->id;
                $stock = $this->salesEntries->create($salesInput);
                $saleItem = [];
                foreach ($request->item_id as $key => $val) {
                    $unit = MstUnit::where('name_en', $request->unit_id[$key])->first();
                    $selectedBatch = $request->batch_no[$key];
                    $selectedBatchQty = $request->batch_qty[$key];
                    $sequenceId = MstSequence::where('sequence_code', $selectedBatch)->first()->id;
                    $selectedItem = $request->itemSalesHidden[$key];
                    $batchQty = BatchQuantityDetail::where('item_id', $selectedItem)->where('batch_no', $sequenceId)->select('id', 'batch_qty')->first();
                    // dd($batchQty->id, $selectedBatchQty, $batchQty->batch_qty);
                    $itemArr = [
                        'sales_id' => $stock->id,
                        'item_id' => $selectedItem ,
                        'batch_no' => $sequenceId,
                        'batch_qty_detail_id' => $batchQty->id,
                        'total_qty' => !$this->multiple_barcode ? $request->total_qty[$key]
                            : ($request->session()->has('barcode.barcode-' . $selectedItem ) ?
                                count($request->session()->get('barcode.barcode-' . $selectedItem )) : 0),
                        'unit_id' => $unit->id,
                        'item_discount' => $request->item_discount[$key],
                        'tax_vat' => $request->tax_vat[$key],
                        'item_total' => $request->item_total[$key],
                        'batch_qty' => $selectedBatchQty,
                        'item_price' => $request->unit_cost_price[$key],
                    ];


                    if ($request->status_id == SupStatus::APPROVED) {
                        $totalQty = $request->total_qty[$key];
                        $newQty = $batchQty->batch_qty - $totalQty;
                        if ($newQty < 0) {
                            return response()->json([
                                'status' => 'failed',
                                'message' => " ERROR updating. Please contact your administrator"
                            ]);
                        }

                        $newBatch = BatchQuantityDetail::find($batchQty->id);
                        $salesQty = ItemQuantityDetail::where('item_id', $selectedItem )->where('store_id', backpack_user()->store_id)->where('sup_org_id', backpack_user()->sup_org_id)->select('id', 'item_qty')->first();
                        $new_sales = $salesQty->item_qty - $totalQty;
                        if ($new_sales < 0) {
                            return response()->json([
                                'status' => 'failed',
                                'message' => " ERROR updating. Please contact your administrator"
                            ]);
                        }
                        $salesQty->item_qty = $new_sales;
                        $newBatch->batch_qty = $newQty;
                        $newBatch->update();
                        $salesQty->update();
                    }

                    $stockItem =  $this->salesItems->create($itemArr);
                    if ($request->session()->has('barcode.barcode-' . $selectedItem )) {
                        $stockBarcodeDetails = $request->session()->get('barcode.barcode-' . $selectedItem );
                        foreach ($stockBarcodeDetails as $barcode => $barcodeItem) {
                            $barcodeId = $this->barcodeDetails::where('barcode_details', $barcode)->pluck('id')->first();
                            $currentPo = $this->barcodeDetails->find($barcodeId);
                            $currentPo->update([
                                'is_active' => $statusCheck ? false : true,
                                'sales_item_id' => $stockItem->id,
                            ]);
                        }
                    }
                }

                DB::commit();

                Artisan::call('barcode-list:generate', [
                    'super_org_id' => $this->user->sup_org_id
                ]);
                session()->forget('barcode');

                return response()->json([
                    'status' => 'success',
                    'message' => 'Sales added successfully',
                    'route' => url($this->crud->route)
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => 'failed',
                    'message' => $e->getMessage()
                ]);
            }
        }
    }

    public function edit($id)
    {
        $sales = $this->salesEntries->find($id);

        if (!isset($sales))
            abort(404);
        $this->crud->allowAccess('edit');
        $this->setSessions($sales->saleItems);
        $discount_modes = MstDiscMode::all();
        $discount_approver = User::where('sup_org_id', $this->user->sup_org_id)->where('store_id', $this->user->store_id)->where('is_discount_approver', true)->get();
        $due_approver = User::where('sup_org_id', $this->user->sup_org_id)->where('store_id', $this->user->store_id)->where('is_due_approver', true)->get();
        $this->data['crud'] = $this->crud;
        $item_lists = $this->getItemList();
        $this->data['item_lists'] = $item_lists;
        $this->data['sales'] = $sales;
        $this->data['discount_modes'] = $discount_modes;
        $this->data['discount_approver'] = $discount_approver;
        $this->data['due_approver'] = $due_approver;
        $this->data['multiple_barcode'] = $this->multiple_barcode;
        $this->data['customers'] = MstCustomer::where([['is_customer', true], ['is_active', true], ['is_coorporate', false]])->get();
        $this->data['coorporateCustomers'] = MstCustomer::where([['is_customer', true], ['is_active', true], ['is_coorporate', true]])->get()->unique('company_name');
        $this->data['sequenceCodes'] = $this->sequence_type();
        $this->data['invoiceSequences'] = $this->getsequenceCode(3);

        return view('customAdmin.sales.edit', $this->data);
    }

    public function update()
    {
        $this->crud->allowAccess('update');
        $request = $this->crud->validateRequest();
        $statusCheck = $request->status_id == SupStatus::APPROVED;

        $salesInput = $request->only([
            'bill_date_ad',
            'bill_no',
            'discount_type',
            'item_id',
            'batch_qty_detail_id',
            'item_qty_detail_id',
            'total_qty',
            'batch_no',
            'batch_qty',
            'item_price',
            'tax_vat',
            'item_discount',
            'item_total',
            'store_id',
            'discount',
            'receipt_amt',
            'discount_amt',
            'gross_amt',
            'taxable_amt',
            'total_tax_vat',
            'net_amt',
            'paid_amt',
            'refund',
            'remarks',
            'payment_type',
            'discount_approver_id',
            'status_id',
            'transaction_date_ad',
            'bank_name',
            'cheque_number',
            'ac_holder_name',
            'branch_name',
            'cheque_date',
            'cheque_upload',
            'due_approver_id'
        ]);

        $customerInput = $request->only([
            'bill_type',
            'full_name',
            'hidden_bill_type',
            'hidden_gender_id',
            'customer_id',
            'address',
            'contact_number',
            'company_name',
            'pan_vat',
        ]);

        $sequenceCodes = $request->only('invoice_sequence');
        if ($request->status_id == SupStatus::APPROVED) {
            if(!array_key_exists('invoice_sequence',$sequenceCodes)){
                return response()->json([
                    'status' => 'failed',
                    'message' => "Failed to approve sale. Invoice Sequence is not created."
                ]);
            }
            $salesInput['bill_no'] = $sequenceCodes['invoice_sequence'];
            $salesInput['bill_date_ad'] = dateToday();
        }
        $salesInput['sup_org_id'] = $this->user->sup_org_id;
        if ($request->payment_type == 2) {
            $salesInput['discount_approver_id'] = $request->discount_approver_id;
            $salesInput['due_approver_id'] = null;
        } elseif ($request->payment_type == 3) {
            $salesInput['due_approver_id'] = $request->due_approver_id;
            $salesInput['discount_approver_id'] = null;
        } else {
            $salesInput['discount_approver_id'] = null;
            $salesInput['due_approver_id'] = null;
        }
        if($customerInput['bill_type'] == 1){
            $bill_type = false;
        }else{
            $bill_type = true;
        }

        try {
            DB::beginTransaction();
            $currentSales = $this->salesEntries->find($this->crud->getCurrentEntryId());
            if($customerInput['customer_id']){
                $customer = MstCustomer::find($customerInput['customer_id']);
            }else{
                $customer = new MstCustomer();
            }
            $customer->is_customer = true;
            $customer->is_coorporate = $bill_type;
            $customer->name_en = $customerInput['full_name'];
            $customer->address = $customerInput['address'];
            $customer->contact_number = $customerInput['contact_number'];
            $customer->company_name = $customerInput['company_name'];
            $customer->pan_no = $customerInput['pan_vat'];
            $customer->sup_org_id = $this->user->sup_org_id;
            $customer->save();

            $salesInput['customer_id'] = $customer->id;


            // For cancelling the bill
            if ($salesInput['status_id'] == 3) {
                $currentSales->update(['status_id' => SupStatus::CANCELLED]);
                $saleItem = [];
                foreach ($request->item_id as $key => $val) {

                    $selectedItem = $request->itemSalesHidden[$key];
                    $selectedBatch = $request->batch_no[$key];
                    $totalQty = !$this->multiple_barcode ? $request->total_qty[$key]
                        : ($request->session()->has('barcode.barcode-' . $request->itemSalesHidden[$key]) ?
                            count($request->session()->get('barcode.barcode-' . $request->itemSalesHidden[$key])) : 0);
                    $batchQty = BatchQuantityDetail::where('item_id', $request->itemSalesHidden[$key])->where('id', $selectedBatch)->select('id', 'batch_qty')->first();
                    $newQty = $batchQty->batch_qty + $totalQty;
                    $newBatch = BatchQuantityDetail::find($batchQty->id);
                    $salesQty = ItemQuantityDetail::where('item_id', $request->itemSalesHidden[$key])->where('store_id', backpack_user()->store_id)->where('sup_org_id', backpack_user()->sup_org_id)->select('id', 'item_qty')->first();
                    $new_sales = $salesQty->item_qty + $totalQty;
                    $salesQty->item_qty = $new_sales;
                    $newBatch->batch_qty = $newQty;
                    $newBatch->update();
                    $salesQty->update();
                }
            }

            $currentSales->update($salesInput);

            $this->barcodeDetails
                ->whereIn('sales_item_id', $currentSales->saleItems->pluck('id')->toArray())
                ->update(['sales_item_id' => null]);
            $this->salesItems->destroy($currentSales->saleItems->pluck('id'));

            $saleItem = [];
            foreach ($request->item_id as $key => $val) {
                $unit = MstUnit::where('name_en', $request->unit_id[$key])->first();

                $itemArr = [
                    'sales_id' => $this->crud->getCurrentEntryId(),
                    'item_id' => $request->itemSalesHidden[$key],
                    'batch_no' => $request->batch_no[$key],
                    'batch_qty_detail_id' => $request->batch_no[$key],
                    'total_qty' => !$this->multiple_barcode ? $request->total_qty[$key]
                        : ($request->session()->has('barcode.barcode-' . $request->itemSalesHidden[$key]) ?
                            count($request->session()->get('barcode.barcode-' . $request->itemSalesHidden[$key])) : 0),
                    'unit_id' => $unit->id,
                    'item_discount' => $request->item_discount[$key],
                    'tax_vat' => $request->tax_vat[$key],
                    'item_total' => $request->item_total[$key],
                    'batch_qty' => $request->batch_qty[$key],
                    'item_price' => $request->unit_cost_price[$key],
                ];

                // array_push($saleItem, $itemArr);
                if ($request->status_id == SupStatus::APPROVED) {
                    $selectedItem = $request->itemSalesHidden[$key];
                    $selectedBatch = $request->batch_no[$key];
                    $totalQty = !$this->multiple_barcode ? $request->total_qty[$key]
                        : ($request->session()->has('barcode.barcode-' . $request->itemSalesHidden[$key]) ?
                            count($request->session()->get('barcode.barcode-' . $request->itemSalesHidden[$key])) : 0);
                    $batchQty = BatchQuantityDetail::where('item_id', $request->itemSalesHidden[$key])->where('id', $selectedBatch)->select('id', 'batch_qty')->first();

                    $itemArr['batch_qty_detail_id'] = $batchQty->id;

                    $newQty = $batchQty->batch_qty - $totalQty;
                    if ($newQty < 0) {
                        return response()->json([
                            'status' => 'failed',
                            'message' => " ERROR updating. Please contact your administrator"
                        ]);
                    }
                    $newBatch = BatchQuantityDetail::find($batchQty->id);
                    $salesQty = ItemQuantityDetail::where('item_id', $request->itemSalesHidden[$key])->where('store_id', backpack_user()->store_id)->where('sup_org_id', backpack_user()->sup_org_id)->select('id', 'item_qty')->first();
                    $new_sales = $salesQty->item_qty - $totalQty;
                    if ($new_sales < 0) {
                        return response()->json([
                            'status' => 'failed',
                            'message' => " ERROR updating. Please contact your administrator"
                        ]);
                    }
                    $salesQty->item_qty = $new_sales;
                    $newBatch->batch_qty = $newQty;
                    $newBatch->save();
                    $salesQty->save();
                }
                $saleb =  $this->salesItems->create($itemArr);
                if ($request->session()->has('barcode.barcode-' . $request->itemSalesHidden[$key])) {

                    $stockBarcodeDetails = $request->session()->get('barcode.barcode-' . $request->itemSalesHidden[$key]);
                    foreach ($stockBarcodeDetails as $barcode => $item_id) {
                        $barcodeId = $this->barcodeDetails::where('barcode_details', $barcode)->first();
                        $barcodeId->update([
                            'is_active' => $statusCheck ? false : true,
                            'sales_item_id' => $statusCheck ? $saleb->id : Null,
                        ]);
                    }
                }
            }

            DB::commit();
            // Artisan::call('barcode-list:generate', ['super_org_id' => $this->user->sup_org_id]);
            Artisan::call('barcode-list:generate', [
                'super_org_id' => $this->user->sup_org_id
            ]);
            session()->forget('barcode');

            return response()->json([
                'status' => 'success',
                'message' => 'Updated successfully',
                'route' => url($this->crud->route)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            if($e->getMessage() == 'Undefined array key "bill_no"'){
                return response()->json([
                    'status' => 'failed',
                    'message' => "Stock Adjustment Sequence Already Exists. Try Another One."
                ]);
            }
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $this->crud->hasAccessOrFail('show');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        $data = [];
        // get the info for that entry (include softDeleted items if the trait is used)
        if ($this->crud->get('show.softDeletes') && in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->crud->model))) {
            $data['entry'] = $this->crud->getModel()->withTrashed()->findOrFail($id);
        } else {
            $data['entry'] = $this->crud->getEntry($id);
        }

        $data['entry'] = Sales::where('bill_no', $data['entry']->bill_no)->where('status_id', 2)->first();

        $data['items'] = $data['entry']->saleItems;

        $data['crud'] = $this->crud;

        return view('customAdmin.sales.show', [
            'entry' => $data['entry'],
            'items' => $data['items'],
            'crud' => $data['crud'],
        ]);
    }

    public function showReturn($id)
    {
        $this->crud->hasAccessOrFail('show');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        $data = [];
        // get the info for that entry (include softDeleted items if the trait is used)
        if ($this->crud->get('show.softDeletes') && in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->crud->model))) {
            $data['entry'] = $this->crud->getModel()->withTrashed()->findOrFail($id);
        } else {
            $data['entry'] = $this->crud->getEntry($id);
        }

        $data['items'] = $data['entry']->saleReturnItems();

        $data['crud'] = $this->crud;

        return view('customAdmin.sales.show', [
            'entry' => $data['entry'],
            'items' => $data['items'],
            'crud' => $data['crud'],
        ]);
    }

    public function stockItem(MstItem $item)
    {
        $taxRate = $item->tax_vat;
        $is_price_editable = $item->is_price_editable;
        $unit = $item->mstUnitEntity->name_en;
        $availableQty = ItemQuantityDetail::select('id', 'item_qty')
            ->where('item_id', $item->id)
            ->where('store_id', $this->user->store_id)
            ->where('sup_org_id', $this->user['sup_org_id'])
            ->orderBy('id', 'desc')
            ->first();
        if (!$availableQty || $availableQty->item_qty <= 0) {
            return response()->json([
                'status' => 'failed',
                'message' => 'This item is out of stock'
            ]);
        }
        $batch_detail = BatchQuantityDetail::where('item_id', $item->id)
            ->where('store_id', $this->user->store_id)
            ->where('sup_org_id', $this->user['sup_org_id'])
            ->orderBy('id', 'desc')
            ->first();

        return response()->json([
            'taxRate' => $taxRate,
            'availableQty' => $availableQty->item_qty,
            'batch_detail' => $batch_detail,
            'unit' => $unit,
            'is_price_editable' => $is_price_editable
        ]);
    }

    public function getBatchItem($itemId)
    {
        $batchNumber = BatchQuantityDetail::where('item_id', $itemId)
            ->where('sup_org_id', $this->user['sup_org_id'])
            ->where('store_id', $this->user->store_id)
            ->where('batch_qty', '<>', 0)
            ->pluck('batch_no');

        $batchNumber = MstSequence::findMany($batchNumber)->pluck('sequence_code');

        return response()->json([
            'batchNumber' => $batchNumber
        ]);
    }

    public function getBatchDetail($itemId, $batchId)
    {
        $sequenceId = MstSequence::where('sequence_code', $batchId)->first()->id;
        $batchDetail = BatchQuantityDetail::where('item_id', $itemId)
            ->where('batch_no', $sequenceId)
            ->where('sup_org_id', $this->user['sup_org_id'])
            ->where('store_id', $this->user->store_id)
            ->orderBy('id', 'desc')
            ->first();

        return response()->json([
            'batchDetail' => $batchDetail
        ]);
    }

    public function getSalesHistoryDetails($detail, $to, $from)
    {
        $data = $this->salesEntries::with('saleItems')
            ->where('bill_no', $detail)
            ->orWhere('contact_number', $detail)
            ->whereBetween('bill_date_ad', [$from, $to])
            ->get();

        return view('customAdmin.sales.partials.history', compact('data'));
    }

    public function barcodeSessionStore(Request $request, $itemId, $batchId)
    {
        $sequenceId = MstSequence::where('sequence_code', $batchId)->first()->id;
        try {
            $count = 0;
            $barcodeDetails = array();
            $barcodeDetails_inside = array();
            if($request->barcode_details == null){
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Current barcode is not found in this Batch!!!'
                ]);
            }
            foreach($request->barcode_details as $barcode_dtl){
                $first_four_char = substr($barcode_dtl,0,4);
                $count = substr_count($barcode_dtl, $first_four_char);
                if($count > 3){
                    $sub_bar_code_details = explode($first_four_char,$barcode_dtl);
                    $sub_bar_code_details = array_values(array_filter($sub_bar_code_details));
                    foreach($sub_bar_code_details as $barcodeDetail){
                        $barcode[] = $first_four_char.$barcodeDetail;
                    }
                    $barcodeDetails_inside = $barcode;
                }else{
                    $barcodeDetails[] = $barcode_dtl;
                }
            }
            $barcodeDetails = array_merge($barcodeDetails, $barcodeDetails_inside);
            $db_exist_barcode = DB::table('stock_items_details')->select('barcode_details')->where('batch_no',$sequenceId)->where('item_id',$itemId)->get()->toArray();
            $db_exist_barcode_arr = [];
            foreach($db_exist_barcode as $barcode){
                $db_exist_barcode_arr[] = $barcode->barcode_details;
            }

            $barcodeDetails = array_intersect($db_exist_barcode_arr, $barcodeDetails);

            $count = count($barcodeDetails);
            if($count == 0){
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Failed to save barcodes. '
                ]);

            }
            $barcodeArrayWithId = [];
            foreach ($barcodeDetails as $detail)
                $barcodeArrayWithId['barcode-' . $itemId][$detail] = $itemId;

            if ($request->session()->has('barcode')) {
                $barcodeArray = $request->session()->pull('barcode');
                $barcodeArrayWithId = array_merge($barcodeArrayWithId, $barcodeArray);
                $request->session()->put('barcode', $barcodeArrayWithId);
            } else {
                $barcodeArray = ['barcode' => $barcodeArrayWithId];
                $request->session()->put($barcodeArray);
            }

            return response()->json([
                'status' => 'success',
                'count' => $count,
                'barcodeList' => getBarcodeJson($this->user->sup_org_id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to save barcodes. ' . $e->getMessage()
            ]);
        }
    }

    //!! Sales Return Methods (Return View)
    public function editSalesReturn($id)
    {
        $this->crud->allowAccess('edit');
        $sales = $this->salesEntries->find($id);
        if (!isset($sales))
            abort(404);
        $this->setSessions($sales->saleItems);
        $discount_modes = MstDiscMode::all();
        $discount_approver = User::where('sup_org_id', $this->user->sup_org_id)->where('store_id', $this->user->store_id)->where('is_discount_approver', true)->get();
        $due_approver = User::where('sup_org_id', $this->user->sup_org_id)->where('store_id', $this->user->store_id)->where('is_due_approver', true)->get();

        $this->data['crud'] = $this->crud;
        $item_lists = $this->getItemList();

        $this->data['item_lists'] = $item_lists;
        $this->data['sales'] = $sales;
        $this->data['discount_modes'] = $discount_modes;
        $this->data['discount_approver'] = $discount_approver;
        $this->data['due_approver'] = $due_approver;
        $this->data['multiple_barcode'] = $this->multiple_barcode;
        $this->data['edit_sales'] = true;

        $this->data['customers'] = MstCustomer::where([['is_customer', true], ['is_active', true], ['is_coorporate', false]])->get();
        $this->data['coorporateCustomers'] = MstCustomer::where([['is_customer', true], ['is_active', true], ['is_coorporate', true]])->get()->unique('company_name');

        $this->data['sequenceCodes'] = $this->sequence_type();
        $this->data['returnSequences'] = $this->getsequenceCode(7);

        return view('customAdmin.sales.sales_return', $this->data);
    }

    //!! Sales return Functionality
    public function storeSalesReturn(Request $request, $id)
    {
        $split_barcodes = explode(',', $request->returnModel);

        $salesInput = $request->only([
            'return_model',
            'bill_date_ad',
            'discount_type',
            'item_id',
            'batch_qty_detail_id',
            'item_qty_detail_id',
            'total_qty',
            'batch_no',
            'batch_qty',
            'item_price',
            'tax_vat',
            'item_discount',
            'item_total',
            'store_id',
            'discount',
            'receipt_amt',
            'discount_amt',
            'gross_amt',
            'taxable_amt',
            'total_tax_vat',
            'net_amt',
            'paid_amt',
            'refund',
            'remarks',
            'payment_type',
            'discount_approver_id',
            'status_id',
            'transaction_date_ad',
            'bank_name',
            'cheque_number',
            'ac_holder_name',
            'branch_name',
            'cheque_date',
            'cheque_upload',
            'due_approver_id',
            'return_type',
        ]);

        $customerInput = $request->only([
            'bill_type',
            'full_name',
            'hidden_bill_type',
            'hidden_gender_id',
            'customer_id',
            'address',
            'contact_number',
            'company_name',
            'pan_vat',
        ]);

        $sequenceCodes = $request->only('return_sequence');

        // $statusCheck = $request->status_id == SupStatus::APPROVED;

        if(!array_key_exists('return_sequence',$sequenceCodes)){
            return response()->json([
                'status' => 'failed',
                'message' => "Failed to approve sale. Invoice Sequence is not created."
            ]);
        }
        $salesInput['bill_no'] = $sequenceCodes['return_sequence'];

        if (isset($salesInput['return_type'])) {
            $return_type = 1;
            $status_id =  SupStatus::FULL_RETURN;
        } else {
            $return_type = 0;
            $status_id =  SupStatus::PARTIAL_RETURN;
        }

        try {
            DB::beginTransaction();

            $stock = Sales::find($id);
            $stock->is_return = true;
            $stock->save();

            $stock_replica = $stock->replicate();

            $stock_replica->return_bill_no = $salesInput['bill_no'];
            $stock_replica->status_id = $status_id;
            $stock_replica->receipt_amt = $request->receipt_amt;
            $stock_replica->gross_amt = $request->gross_amt;
            $stock_replica->discount_amt = $request->discount_amt;
            $stock_replica->taxable_amt = $request->taxable_amt;
            $stock_replica->total_tax_vat = $request->total_tax_vat;
            $stock_replica->net_amt = $request->net_amt;
            $stock_replica->paid_amt = $request->paid_amt;
            $stock_replica->refund = $request->refund;

            $stock_replica->save();
            foreach ($request->item_id as $key => $val) {
                $unit = MstUnit::where('name_en', $request->unit_id[$key])->first();

                $return_qty = intval($request->return_qty[$key]);
                $multiple_return_qty = count($request->session()->get('barcode.barcode-' . $request->itemSalesHidden[$key]));

                $initial_total_qty = intval($request->total_qty[$key]);
                // $initial_multiple_total_qty = count($request->session()->get('barcode.barcode-' . $request->itemSalesHidden[$key]));

                $final_total_qty = $initial_total_qty - $return_qty;
                // $final_multiple_total_qty = $initial_multiple_total_qty - $multiple_return_qty;

                $itemArr = [
                    'sales_id' => $stock_replica->id,
                    'item_id' => $request->itemSalesHidden[$key],
                    'batch_no' => $request->batch_no[$key],
                    'batch_qty_detail_id' => $request->batch_no[$key],
                    // 'total_qty' => !$this->multiple_barcode ? $request->total_qty[$key]
                    //     : ($request->session()->has('barcode.barcode-' . $request->itemSalesHidden[$key]) ?
                    //         count($request->session()->get('barcode.barcode-' . $request->itemSalesHidden[$key])) : 0),
                    'unit_id' => $unit->id,
                    'item_discount' => $request->item_discount[$key],
                    'tax_vat' => $request->tax_vat[$key],
                    'item_total' => $request->item_total[$key],
                    'batch_qty' => $request->batch_qty[$key],
                    'item_price' => $request->unit_cost_price[$key],
                ];

                // $itemArr['return_qty'] = !$this->multiple_barcode ? $return_qty
                //     : ($request->session()->has('barcode.barcode-' . $request->itemSalesHidden[$key]) ?
                //         $multiple_return_qty : 0);


                // $itemArr['total_qty'] = !$this->multiple_barcode ? $final_total_qty
                //     : ($request->session()->has('barcode.barcode-' . $request->itemSalesHidden[$key]) ?
                //         $final_multiple_total_qty : 0);

                $itemArr['return_qty'] = $return_qty;
                $itemArr['total_qty'] = $final_total_qty;
                $stockItem =  $this->salesItems->create($itemArr);
                $selectedItem = $request->itemSalesHidden[$key];

                $selectedBatch = $request->batch_no[$key];
                $totalQty = $request->return_qty[$key];

                $batchQty = BatchQuantityDetail::where('item_id', $request->itemSalesHidden[$key])
                    ->where('id', $selectedBatch)
                    ->select('id', 'batch_qty')
                    ->first();

                $itemArr['batch_qty_detail_id'] = $batchQty->id;

                $newBatch = BatchQuantityDetail::find($batchQty->id);

                $salesQty = ItemQuantityDetail::where('item_id', $request->itemSalesHidden[$key])
                    ->where('store_id', backpack_user()->store_id)
                    ->where('sup_org_id', backpack_user()->sup_org_id)
                    ->select('id', 'item_qty')
                    ->first();

                $custom_qty = $salesQty->item_qty;

                $newQty = $batchQty->batch_qty + $totalQty;
                $new_sales = $custom_qty + $totalQty;
                $salesQty->item_qty = $new_sales;
                $newBatch->batch_qty = $newQty;
                $newBatch->save();
                $salesQty->save();

                //**Partial Sales Return */
                if ($return_type == 0) {
                    if ($request->return_qty[$key] != null) {
                        // $stockItem =  $this->salesItems->create($itemArr);
                        foreach ($split_barcodes as $barcode) {
                            $barcodeId = $this->barcodeDetails::where('barcode_details', $barcode)->pluck('id')->first();
                            $currentPo = $this->barcodeDetails->find($barcodeId);
                            $currentPo->update([
                                'is_active' => true,
                                'sales_item_id' => null,
                            ]);
                        }
                    }
                }

                //**Full Sales Return */
                elseif ($return_type == 1) {
                    // $stockItem =  $this->salesItems->create($itemArr);
                    if ($request->session()->has('barcode.barcode-' . $request->itemSalesHidden[$key])) {
                        $stockBarcodeDetails = $request->session()->get('barcode.barcode-' . $request->itemSalesHidden[$key]);
                        foreach ($stockBarcodeDetails as $barcode => $barcodeItem) {
                            $barcodeId = $this->barcodeDetails::where('barcode_details', $barcode)->pluck('id')->first();
                            $currentPo = $this->barcodeDetails->find($barcodeId);

                            //Remove sales_item_id from stock_items_details
                            $currentPo->update([
                                'is_active' => true,
                                'sales_item_id' => null,
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            Artisan::call('barcode-list:generate', [
                'super_org_id' => $this->user->sup_org_id
            ]);
            session()->forget('barcode');

            return response()->json([
                'status' => 'success',
                'message' => 'Sales added successfully',
                'route' => url($this->crud->route)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            if($e->getMessage() == 'Undefined array key "bill_no"'){
                return response()->json([
                    'status' => 'failed',
                    'message' => "Sales Return Sequence Already Exists. Try Another One."
                ]);
            }
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function retrunSessionStore(Request $request, $itemId)
    {
        try {
            if (!$request->has('barcode_details'))
                throw new \Exception('Please scan through a barcode reader.');
            $barcodeDetails = $request->barcode_details;
            $count = count($barcodeDetails);
            return response()->json([
                'status' => 'success',
                'count' => $count,
                'barcodeList' => $barcodeDetails
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to save barcodes. ' . $e->getMessage()
            ]);
        }
    }

    public function barcodeSessionFlush($key)
    {
        if (!session()->has('barcode.' . $key)) {

            return response()->json([
                'status' => 'failed',
            ]);
        }
        session()->forget('barcode.' . $key);

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * @param $items
     * @return void
     */
    private function setSessions($items)
    {
        $arr = ['barcode' => []];
        foreach ($items as $item) {
            $arr['barcode']['barcode-' . $item->item_id] = $item->barcodeDetails->pluck('item_id', 'barcode_details')->toArray();
        }
        session()->put($arr);
    }

    public function printInvoice($id)
    {
        $sales = DB::select($this->salesInvoiceQueryString(),[$id]);
        $sales_items = DB::select($this->salesItemsInvoiceQueryString(),[$id]);
        $sales_items_total = $sales_items[0]->item_price * $sales_items[0]->total_qty;
        $sales_items[0]->total_payable_price = $sales_items_total;
        $sales[0]->netAmtWords = NumberToWords::ConvertToEnglishWord($sales[0]->net_amt);
        $sales_bill = Sales::find($id);
        $bill_no = $sales_bill->bill_no;
        $sup_id = $sales_bill->sup_org_id;
        $store_id = $sales_bill->store_id;
        $header_footer_data = AppSetting::where('sup_org_id', $sup_id)->where('store_id', $store_id)->first();
        if (isset($header_footer_data->background)) {
            //background
            $background_encoded = "";
            $background_path = public_path('storage/uploads/' . $header_footer_data->background);
            // Read image path, convert to base64 encoding
            $imageData = base64_encode(file_get_contents($background_path));
            // Format the image SRC:  data:{mime};base64,{data};
            $background_encoded = 'data: ' . mime_content_type($background_path) . ';base64,' . $imageData;
        }

        if (isset($header_footer_data->logo)) {
            $logo_encoded = "";
            $logo_path = public_path('storage/uploads/' . $header_footer_data->logo);
            // Read image path, convert to base64 encoding
            $logoImageData = base64_encode(file_get_contents($logo_path));
            // Format the image SRC:  data:{mime};base64,{data};
            $logo_encoded = 'data: ' . mime_content_type($logo_path) . ';base64,' . $logoImageData;
        }

        // $background_image =  '/storage/uploads/' . $header_footer_data->background;

        $sales = $sales[0];
        $view = 'pdfPages.invoiceBill';

        if ((isset($header_footer_data->logo)) && (isset($header_footer_data->background))) {
            $html = view($view, compact('sales', 'sales_items', 'header_footer_data', 'background_encoded', 'logo_encoded'))->render();
        } elseif (isset($header_footer_data->logo)) {
            $html = view($view, compact('sales', 'sales_items', 'header_footer_data', 'logo_encoded'))->render();
        } elseif (isset($header_footer_data->background)) {
            $html = view($view, compact('sales', 'sales_items', 'header_footer_data', 'background_encoded'))->render();
        } else {
            $html = view($view, compact('sales', 'sales_items', 'header_footer_data'))->render();
        }
        $file_name = 'Invoice - ' . $bill_no . '.pdf';

        $res = PdfPrint::printPortrait($html, $file_name);
        return $res;
    }

    public function printInvoiceNoHeader($id)
    {
        $sales = DB::select($this->salesInvoiceQueryString(),[$id]);
        $sales_items = DB::select($this->salesItemsInvoiceQueryString(),[$id]);
        $sales_items_total = $sales_items[0]->item_price * $sales_items[0]->total_qty;
        $sales_items[0]->total_payable_price = $sales_items_total;
        $sales[0]->netAmtWords = NumberToWords::ConvertToEnglishWord($sales[0]->net_amt);
        $sales_bill = Sales::find($id);
        $bill_no = $sales_bill->bill_no;
        $sup_id = $sales_bill->sup_org_id;
        $store_id = $sales_bill->store_id;
        $header_footer_data = AppSetting::where('sup_org_id', $sup_id)->where('store_id', $store_id)->first();

        if (isset($header_footer_data->background)) {
            //background
            $background_encoded = "";
            $background_path = public_path('storage/uploads/' . $header_footer_data->background);
            // Read image path, convert to base64 encoding
            $imageData = base64_encode(file_get_contents($background_path));
            // Format the image SRC:  data:{mime};base64,{data};
            $background_encoded = 'data: ' . mime_content_type($background_path) . ';base64,' . $imageData;
        }

        $sales = $sales[0];
        $view = 'pdfPages.noHeaderInvoiceBill';
        if ((isset($header_footer_data->background))) {
            $html = view($view, compact('sales', 'sales_items', 'header_footer_data', 'background_encoded'))->render();
        } else {
            $html = view($view, compact('sales', 'sales_items', 'header_footer_data'))->render();
        }
        $file_name = 'Invoice - ' . $bill_no . '.pdf';

        $res = PdfPrint::printPortrait($html, $file_name);
        return $res;
    }

    public function printSalesReturnInvoice($id)
    {
        $sales = DB::select($this->salesReturnInvoiceQueryString(),[$id]);
        $sales_items = DB::select($this->salesItemsReturnInvoiceQueryString(),[$id]);
        $sales_items_total = $sales_items[0]->item_price * $sales_items[0]->total_qty;
        $sales_items[0]->total_payable_price = $sales_items_total;
        $sales[0]->netAmtWords = NumberToWords::ConvertToEnglishWord($sales[0]->net_amt);
        $sales_bill = Sales::find($id);
        $bill_no = $sales_bill->bill_no;
        $sup_id = $sales_bill->sup_org_id;
        $store_id = $sales_bill->store_id;
        $header_footer_data = AppSetting::where('sup_org_id', $sup_id)->where('store_id', $store_id)->first();

        if (isset($header_footer_data->background)) {
            //background
            $background_encoded = "";
            $background_path = public_path('storage/uploads/' . $header_footer_data->background);
            // Read image path, convert to base64 encoding
            $imageData = base64_encode(file_get_contents($background_path));
            // Format the image SRC:  data:{mime};base64,{data};
            $background_encoded = 'data: ' . mime_content_type($background_path) . ';base64,' . $imageData;
        }

        if (isset($header_footer_data->logo)) {
            $logo_encoded = "";
            $logo_path = public_path('storage/uploads/' . $header_footer_data->logo);
            // Read image path, convert to base64 encoding
            $logoImageData = base64_encode(file_get_contents($logo_path));
            // Format the image SRC:  data:{mime};base64,{data};
            $logo_encoded = 'data: ' . mime_content_type($logo_path) . ';base64,' . $logoImageData;
        }

        // $background_image =  '/storage/uploads/' . $header_footer_data->background;

        $sales = $sales[0];
        $view = 'pdfPages.invoiceBill';

        if ((isset($header_footer_data->logo)) && (isset($header_footer_data->background))) {
            $html = view($view, compact('sales', 'sales_items', 'header_footer_data', 'background_encoded', 'logo_encoded'))->render();
        } elseif (isset($header_footer_data->logo)) {
            $html = view($view, compact('sales', 'sales_items', 'header_footer_data', 'logo_encoded'))->render();
        } elseif (isset($header_footer_data->background)) {
            $html = view($view, compact('sales', 'sales_items', 'header_footer_data', 'background_encoded'))->render();
        } else {
            $html = view($view, compact('sales', 'sales_items', 'header_footer_data'))->render();
        }
        $file_name = 'Invoice - ' . $bill_no . '.pdf';

        $res = PdfPrint::printPortrait($html, $file_name);
        return $res;
    }

    public function printSalesReturnInvoiceNoHeader($id)
    {
        $sales = DB::select($this->salesReturnInvoiceQueryString(),[$id]);
        $sales_items = DB::select($this->salesItemsReturnInvoiceQueryString(),[$id]);
        $sales_items_total = $sales_items[0]->item_price * $sales_items[0]->total_qty;
        $sales_items[0]->total_payable_price = $sales_items_total;
        $sales[0]->netAmtWords = NumberToWords::ConvertToEnglishWord($sales[0]->net_amt);
        $sales_bill = Sales::find($id);
        $bill_no = $sales_bill->bill_no;
        $sup_id = $sales_bill->sup_org_id;
        $store_id = $sales_bill->store_id;
        $header_footer_data = AppSetting::where('sup_org_id', $sup_id)->where('store_id', $store_id)->first();

        if (isset($header_footer_data->background)) {
            //background
            $background_encoded = "";
            $background_path = public_path('storage/uploads/' . $header_footer_data->background);
            // Read image path, convert to base64 encoding
            $imageData = base64_encode(file_get_contents($background_path));
            // Format the image SRC:  data:{mime};base64,{data};
            $background_encoded = 'data: ' . mime_content_type($background_path) . ';base64,' . $imageData;
        }

        $sales = $sales[0];

        $view = 'pdfPages.noHeaderInvoiceBill';

        if ((isset($header_footer_data->background))) {
            $html = view($view, compact('sales', 'sales_items', 'header_footer_data', 'background_encoded'))->render();
        } else {
            $html = view($view, compact('sales', 'sales_items', 'header_footer_data'))->render();
        }
        $file_name = 'Invoice - ' . $bill_no . '.pdf';

        $res = PdfPrint::printPortrait($html, $file_name);
        return $res;
    }

    public function listPdfDownload()
    {
        $i = 1;
        $sales = Sales::where('deleted_uq_code', 1)->get();
        $view = 'pdfPages.listOperations.sales';
        $html = view($view, compact('sales', 'i'))->render();
        $file_name = 'Sales List.pdf';
        $res = PdfPrint::printPortrait($html, $file_name);
        return $res;
    }

    public function salesInvoiceQueryString()
    {
        return "
            SELECT
            sl.id,
            sl.bill_date_ad as bill_date_ad,
            sl.bill_date_bs as bill_date_bs,
            sl.transaction_date_ad as transaction_date,
            sl.payment_type as payment_type,
            ct.name_en as buyer_name,
            ct.company_name as buyer_company_name,
            ct.address as buyer_address,
            ct.pan_no as buyer_pan,
            ct.contact_number as contact_number,
            sl.receipt_amt as receipt_amt,
            sl.gross_amt as gross_amt,
            sl.discount_amt as discount_amt,
            sl.taxable_amt as taxable_amt,
            sl.total_tax_vat as total_tax_vat,
            sl.net_amt as net_amt,
            sl.paid_amt as paid_amt,
            sl.refund as refund,
            sl.created_by,
            sl.transaction_date_ad as transaction_date_ad,
            sl.bill_no as bill_no,
            ss.id,

            u.name as user_name,
            ms.name_lc as store_name,
            ms.email as store_email,
            ms.phone_no as store_phone

            FROM sales as sl

            LEFT JOIN sup_status as ss on sl.status_id = ss.id
            LEFT JOIN users as u on sl.created_by = u.id
            LEFT JOIN mst_stores as ms on sl.store_id = ms.id
            LEFT JOIN mst_suppliers as ct on sl.customer_id = ct.id
            WHERE sl.id = ?
        ";
    }

    public function salesItemsInvoiceQueryString(){
        return "
            SELECT
            si.sales_id as sales_items_id,
            si.item_id as item_id,
            si.unit_id as unit_id,
            si.batch_no as batch_no,

            mi.name as item_name,

            bqd.batch_no as batch_no,

            si.total_qty as total_qty,
            si.tax_vat as tax_amount,

            mu.name_lc as unit_name,

            si.item_price as item_price,

            si.item_total as item_total

            FROM sales_items as si

            LEFT JOIN sales as sl on si.sales_id = sl.id
            LEFT JOIN mst_items as mi on si.item_id = mi.id
            LEFT JOIN mst_units as mu on si.unit_id = mu.id
            LEFT JOIN batch_qty_detail as bqd on si.batch_qty_detail_id = bqd.id

            WHERE si.sales_id = ?
        ";
    }

    public function salesReturnInvoiceQueryString()
    {
        return "
            SELECT
            sl.id,
            sl.bill_date_ad as bill_date_ad,
            sl.bill_date_bs as bill_date_bs,
            sl.transaction_date_ad as transaction_date,
            sl.payment_type as payment_type,
            ct.name_en as buyer_name,
            ct.company_name as buyer_company_name,
            ct.address as buyer_address,
            ct.pan_no as buyer_pan,
            ct.contact_number as contact_number,
            sl.receipt_amt as receipt_amt,
            sl.gross_amt as gross_amt,
            sl.discount_amt as discount_amt,
            sl.taxable_amt as taxable_amt,
            sl.total_tax_vat as total_tax_vat,
            sl.net_amt as net_amt,
            sl.paid_amt as paid_amt,
            sl.refund as refund,
            sl.created_by,
            sl.transaction_date_ad as transaction_date_ad,
            sl.bill_no as bill_no,
            sl.return_bill_no as return_bill_no,
            ss.id,

            u.name as user_name,
            ms.name_lc as store_name,
            ms.email as store_email,
            ms.phone_no as store_phone

            FROM sales as sl

            LEFT JOIN sup_status as ss on sl.status_id = ss.id
            LEFT JOIN users as u on sl.created_by = u.id
            LEFT JOIN mst_stores as ms on sl.store_id = ms.id
            LEFT JOIN mst_suppliers as ct on sl.customer_id = ct.id


            WHERE sl.id = ?
        ";
    }

    public function salesItemsReturnInvoiceQueryString(){
        return "
            SELECT
            si.sales_id as sales_items_id,
            si.item_id as item_id,
            si.unit_id as unit_id,
            si.batch_no as batch_no,

            mi.name as item_name,

            bqd.batch_no as batch_no,

            si.return_qty as total_qty,
            si.tax_vat as tax_amount,

            mu.name_lc as unit_name,

            si.item_price as item_price,

            si.item_total as item_total

            FROM sales_items as si

            LEFT JOIN sales as sl on si.sales_id = sl.id
            LEFT JOIN mst_items as mi on si.item_id = mi.id
            LEFT JOIN mst_units as mu on si.unit_id = mu.id
            LEFT JOIN batch_qty_detail as bqd on si.batch_qty_detail_id = bqd.id

            WHERE si.sales_id = ?
        ";
    }

}
