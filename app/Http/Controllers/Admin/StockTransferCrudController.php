<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\MstUnit;
use App\Models\MstStore;
use App\Models\SupStatus;
use App\Models\StockItems;
use App\Models\MstCustomer;
use App\Models\MstDiscMode;
use App\Models\StockEntries;
use Illuminate\Http\Request;
use App\Models\StockTransfer;
use App\Base\BaseCrudController;
use App\Models\StockItemDetails;
use App\Models\ItemQuantityDetail;
use App\Models\StockTransferItems;
use Illuminate\Support\Facades\DB;
use App\Models\BatchQuantityDetail;
use Illuminate\Support\Facades\Artisan;
use App\Http\Requests\StockTransferRequest;

/**
 * Class StockTransferCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class StockTransferCrudController extends BaseCrudController
{
    private $barcodeDetails;

    public function __construct(StockItemDetails $barcodeDetails, StockEntries $stockEntries, StockItems $stockItems)
    {
        parent::__construct();
        $this->stockEntries = $stockEntries;
        $this->stockItems = $stockItems;
        $this->barcodeDetails = $barcodeDetails;
    }

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        $this->crud->setModel(\App\Models\StockTransfer::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/stock-transfer');
        $this->crud->setEntityNameStrings('', 'stock transfer');
        $this->user = backpack_user();
        $this->multiple_barcode = backpack_user()->superOrganizationEntity->multiple_barcode;
        $this->filterDataByStoreUser(["sup_org_id" => $this->user->sup_org_id]);
        $this->crud->denyAccess(['update', 'delete']);
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->hasAccessOrFail('list');
        $columns = [
            $this->addRowNumberColumn(),
            [
                'label' => 'From Store',
                'type' => 'select',
                'name' => 'from_store_id', // the column that contains the ID of that connected entity;
                'entity' => 'fromStoreEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_en', // foreign key attribute that is shown to user
                'model' => MstStore::class
            ],
            [
                'label' => 'To Store',
                'type' => 'select',
                'name' => 'to_store_id', // the column that contains the ID of that connected entity;
                'entity' => 'toStoreEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_en', // foreign key attribute that is shown to user
                'model' => MstStore::class
            ],
            [
                'label'     => 'Adjustment No', // Table column heading
                'type'      => 'select',
                'name'      => 'adjustment_no', // the column that contains the ID of that connected entity;
                'entity'    => 'adjustmentSequence', // the method that defines the relationship in your Model
                'attribute' => 'sequence_code', // foreign key attribute that is shown to user
                'model'     => MstSequence::class, // foreign key model
            ],
            [
                'name' => 'entry_date_ad',
                'type' => 'date',
                'label' => 'Entry date(AD)'
            ],
            [
                'label' => 'Entry date(BS)',
                'type' => 'model_function',
                'function_name' => 'getDateString',
            ],
            [
                'name' => 'net_amount',
                'type' => 'text',
                'label' => 'Stock Amount'
            ],
        ];
        $this->crud->addColumns(array_filter($columns));
        $this->crud->addClause('where', 'from_store_id', '=', $this->user->store_id);
        $this->crud->addClause('orWhere', 'to_store_id', '=', $this->user->store_id);
        $this->crud->removeButtons(['show', 'update', 'delete']);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        session()->forget('barcode');
        $this->crud->hasAccessOrFail('create');
        $mstStoreName = $this->user->storeEntity->name_en ?? 'n/a';
        $crud = $this->crud;
        $storeList = $this->getFilteredStoreList();
        $item_lists = $this->getStockEntryItemsList();
        $batchNumbers = $this->getsequenceCode(1);
        $adjustmentNumbers = $this->getsequenceCode(6);
        $stockTransferNumbers = $this->getsequenceCode(9);
        $sequenceCodes = $this->sequence_type();
        $multiple_barcode = $this->multiple_barcode;
        return view('customAdmin.stockTransfer.create', compact('crud', 'item_lists', 'mstStoreName', 'multiple_barcode', 'storeList', 'batchNumbers', 'adjustmentNumbers', 'sequenceCodes', 'stockTransferNumbers'));
    }

    public function store()
    {
        $request = $this->crud->validateRequest();

        $this->crud->hasAccessOrFail('create');
        $request = $this->crud->validateRequest();
        if (isset($request)) {

            //Requests for stock entry and stock transfer
            $stockTransferInput = $request->only([
                'from_store_id',
                'to_store_id',
                'remarks',
                'gross_amt',
                'discount_amt',
                'taxable_amt',
                'total_tax_vat',
                'net_amt',
            ]);

            $stockTransferInput['sup_status_id'] = $request->status_id;
            $stockTransferInput['sup_org_id'] = $this->user->sup_org_id;
            $stockTransferInput['created_by'] = $this->user->id;

            $sequenceCodes = $request->only(['batch_number', 'adjustment_no', 'transfer_adjustment_no']);

            $stockInput = $request->only([
                'gross_total',
                'total_discount',
                'taxable_amount',
                'tax_total',
                'net_amount'
            ]);

            $stockInput['store_id'] = $stockTransferInput['to_store_id'];
            $stockInput['comments'] = $stockTransferInput['remarks'];
            $stockInput['sup_status_id'] = $stockTransferInput['sup_status_id'];
            $stockInput['sup_org_id'] = $stockTransferInput['sup_org_id'];
            $stockInput['created_by'] = $stockTransferInput['created_by'];
            $stockInput['gross_total'] = $stockTransferInput['gross_amt'];
            $stockInput['total_discount'] = $stockTransferInput['discount_amt'];
            $stockInput['taxable_amount'] = $stockTransferInput['taxable_amt'];
            $stockInput['tax_total'] = $stockTransferInput['total_tax_vat'];
            $stockInput['net_amount'] = $stockTransferInput['net_amt'];

            $statusCheck = $request->status_id == SupStatus::APPROVED;

            if ($statusCheck) {

                //!! For Stock Transfer
                if(empty($sequenceCodes)){
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Failed to approve stock transfer. SequenceCodes are not available"
                    ]);
                }elseif(!array_key_exists('adjustment_no',$sequenceCodes)){
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Failed to approve stock transfer. Stock Adjustment Sequence is not created."
                    ]);
                }elseif(!array_key_exists('transfer_adjustment_no',$sequenceCodes)){
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Failed to approve stock transfer. Stock Transfer Sequence is not created."
                    ]);
                }

                $stockTransferInput['adjustment_no'] = $sequenceCodes['transfer_adjustment_no'];
                $stockTransferInput['entry_date_ad'] = dateToday();
                $stockTransferInput['entry_date_bs'] = convert_bs_from_ad($stockTransferInput['entry_date_ad']);
                $stockTransferInput['approved_by'] = $this->user->id;

                //!! For stock entry
                $stockInput['adjustment_no'] = $sequenceCodes['adjustment_no'];
                $stockInput['entry_date_bs'] = $stockTransferInput['entry_date_bs'];
                $stockInput['entry_date_ad'] = $stockTransferInput['entry_date_ad'];
                $stockInput['approved_by'] = $stockTransferInput['approved_by'];
            }
            try {
                DB::beginTransaction();
                if($statusCheck){
                    $stockEntry = $this->stockEntries->create($stockInput);
                    $stockTransferInput['stock_entry_id'] = $stockEntry->id;
                }

                $stockTransfer = StockTransfer::create($stockTransferInput);

                foreach ($request->item_id as $key => $val) {
                    $unit = MstUnit::where('name_en', $request->unit_id[$key])->first();
                    //Variable assignment before db entry
                    $itemId = $request->itemSalesHidden[$key];
                    $totalStockQtyEntry = !$this->multiple_barcode ? $request->total_qty[$key] : ($request->session()->has('barcode.barcode-' . $itemId) ?
                        count($request->session()->get('barcode.barcode-' . $itemId)) : 0);

                    $itemDiscount = $request->item_discount[$key];
                    $itemTaxVat = $request->tax_vat[$key];
                    $itemTotal = $request->item_total[$key];

                    $stockTransferItemsArr = [
                        'stock_transfer_id' => $stockTransfer->id,
                        'item_id' => $itemId,
                        'from_store_id' => $stockTransferInput['from_store_id'],
                        'to_store_id' => $stockTransferInput['to_store_id'],
                        'item_qty' => $totalStockQtyEntry,
                        'unit_id' => $unit->id,
                        'item_discount' => $itemDiscount,
                        'tax_vat' => $itemTaxVat,
                        'item_total' => $itemTotal,
                        'item_price' => $request->unit_cost_price[$key],
                        'sup_org_id' => $this->user->sup_org_id,
                    ];

                    $batchQty = BatchQuantityDetail::where(['sup_org_id' => $this->user->sup_org_id, 'store_id' => $this->user->store_id, 'item_id' => $itemId])->select('id', 'batch_qty')->first();

                    $stockTransferItemsArr['batch_qty_detail_id'] = $batchQty->id;

                    $prevAvailQty = ItemQuantityDetail::where(['sup_org_id' =>  $this->user->sup_org_id, 'store_id' => $stockTransferInput['to_store_id'], 'item_id' => $itemId])->orderBy('created_at', 'DESC')->first();

                    if ($prevAvailQty) {
                        $availQty = $prevAvailQty->item_qty;
                    } else {
                        $availQty = 0;
                    }

                    if ($statusCheck) {
                        if(!array_key_exists('batch_number',$sequenceCodes)){
                            return response()->json([
                                'status' => 'failed',
                                'message' => "Failed to approve stock transfer. Batch Number is not created."
                            ]);
                        }

                        $stockEntryItemsArr = [
                            'stock_id' => $stockEntry->id,
                            'sup_org_id' => $this->user->sup_org_id,
                            'mst_item_id' => $itemId,
                            'available_total_qty' => $availQty,
                            'add_qty' => $totalStockQtyEntry,
                            'total_qty' => ($availQty + $totalStockQtyEntry),
                            'discount' => $itemDiscount,
                            'tax_vat' => $itemTaxVat,
                            'item_total' => $itemTotal,
                            'store_id' => $stockTransferInput['to_store_id'],
                            'batch_no' => $sequenceCodes['batch_number'],
                        ];

                        $totalQty = $request->total_qty[$key];
                        $newQty = $batchQty->batch_qty - $totalQty;
                        if ($newQty < 0) {
                            return response()->json([
                                'status' => 'failed',
                                'message' => " ERROR updating. Please contact your administrator"
                            ]);
                        }

                        //!! Updating From Store's Details
                        $fromBatch = BatchQuantityDetail::find($batchQty->id);
                        $itemQty = ItemQuantityDetail::where(['sup_org_id' =>  $this->user->sup_org_id, 'store_id' => $this->user->store_id, 'item_id' => $itemId])->orderBy('created_at', 'DESC')->first();
                        $transferedQty = $totalQty;
                        $remainingQty = $itemQty->item_qty - $transferedQty;
                        if ($remainingQty < 0) {
                            return response()->json([
                                'status' => 'failed',
                                'message' => " ERROR updating. Please contact your administrator"
                            ]);
                        }

                        $itemQty->item_qty = $remainingQty;
                        $fromBatch->batch_qty = $newQty;
                        $fromBatch->update();
                        $itemQty->update();

                        //!! Updating To Store's Details

                        //Batch Qty detail entry
                        $toBatch = $fromBatch->replicate();
                        $toBatch->store_id = $stockTransferInput['to_store_id'];
                        $toBatch->batch_qty = $totalQty;
                        $toBatch->batch_from = 'stock-transfer';
                        $toBatch->batch_no = $sequenceCodes['batch_number'];
                        $toBatch->created_by = $this->user->id;
                        $toBatch->save();

                        //Item Details Entry
                        $toItemQty =  $itemQty->replicate();
                        $toItemQty->store_id = $stockTransferInput['to_store_id'];
                        $toItemQty->item_qty = $transferedQty;
                        $toItemQty->item_id = $request->itemSalesHidden[$key];
                        $toItemQty->sup_org_id = $this->user->sup_org_id;
                        $toItemQty->created_by = $this->user->id;
                        $toItemQty->save();

                        $stockItem = $this->stockItems->create($stockEntryItemsArr);
                        $stockTransferItemsArr['stock_entry_item_id'] = $stockItem->id;

                        if ($request->session()->has('barcode.barcode-' . $itemId)) {
                            $stockBarcodeDetails = $request->session()->get('barcode.barcode-' . $itemId);
                            foreach ($stockBarcodeDetails as $barcode => $barcodeItem) {
                                $barcodeId = $this->barcodeDetails::where('barcode_details', $barcode)->pluck('id')->first();
                                $currentPo = $this->barcodeDetails->find($barcodeId);
                                $currentPo->update([
                                    'is_active' => $statusCheck ? true : false,
                                    'store_id' => $stockTransferInput['to_store_id'],
                                    'stock_item_id' => $stockItem->id,
                                    'batch_no' =>  $sequenceCodes['batch_number'],
                                ]);
                            }
                        }
                    }
                    $stockTransferItems =  StockTransferItems::create($stockTransferItemsArr);
                }
                DB::commit();
                Artisan::call('barcode-list:generate', [
                    'super_org_id' => $this->user->sup_org_id
                ]);
                session()->forget('barcode');
                return response()->json([
                    'status' => 'success',
                    'message' => 'Stock transferred successfully',
                    'route' => url($this->crud->route)
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Stock transfer Failed. ' . $e->getMessage()
                ]);
            }
        }
    }

    public function barcodeSessionStore(Request $request, $itemId, $batchId)
    {
        try {
            $count = 0;
            $barcodeDetails = array();
            $barcodeDetails_inside = array();
            if ($request->barcode_details == null) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Current barcode is not found in this Batch!!!'
                ]);
            }
            foreach ($request->barcode_details as $barcode_dtl) {
                $first_four_char = substr($barcode_dtl, 0, 4);
                $count = substr_count($barcode_dtl, $first_four_char);
                if ($count > 3) {
                    $sub_bar_code_details = explode($first_four_char, $barcode_dtl);
                    $sub_bar_code_details = array_values(array_filter($sub_bar_code_details));
                    foreach ($sub_bar_code_details as $barcodeDetail) {
                        $barcode[] = $first_four_char . $barcodeDetail;
                    }
                    $barcodeDetails_inside = $barcode;
                } else {
                    $barcodeDetails[] = $barcode_dtl;
                }
            }
            $barcodeDetails = array_merge($barcodeDetails, $barcodeDetails_inside);
            // $db_exist_barcode = DB::table('stock_items_details')->select('barcode_details')->where('batch_no', $batchId)->where('item_id', $itemId)->get()->toArray();
            $db_exist_barcode = DB::table('stock_items_details')->select('barcode_details')->where(['sup_org_id' => $this->user->sup_org_id, 'store_id' => $this->user->store_id, 'item_id' => $itemId])->get()->toArray();
            $db_exist_barcode_arr = [];
            foreach ($db_exist_barcode as $barcode) {
                $db_exist_barcode_arr[] = $barcode->barcode_details;
            }

            $barcodeDetails = array_intersect($db_exist_barcode_arr, $barcodeDetails);

            $count = count($barcodeDetails);
            if ($count == 0) {
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

    public function getStoreListExcept($id)
    {
        $stores = MstStore::where('id', '!=', $id)->where('sup_org_id', $this->user->sup_org_id)->get(['id', 'name_en']);
        if (count($stores)) {
            return response()->json([
                'status' => 'success',
                'data' => $stores
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Oops. No Store found to transfer stocks. Create new store now.',
            ]);
        }
    }
}
