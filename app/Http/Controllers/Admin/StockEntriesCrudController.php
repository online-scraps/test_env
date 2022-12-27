<?php

namespace App\Http\Controllers\Admin;


use Carbon\Carbon;
use App\Models\Sales;
use http\Env\Response;
use App\Models\BatchNo;
use App\Models\MstItem;
use App\Utils\PdfPrint;
use App\Models\MstStore;
use App\Models\SaleItems;
use App\Models\SupStatus;
use App\Models\StockItems;
use App\Models\MstSequence;
use App\Models\StockEntries;
use Illuminate\Http\Request;
use App\Base\BaseCrudController;
use App\Models\StockItemDetails;
use App\Models\StockAdjustmentNo;
use App\Exports\StockEntriesExcel;
use App\Models\ItemQuantityDetail;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;
use App\Models\BatchQuantityDetail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;


use App\Imports\StockEntriesExcelImport;
use Illuminate\Support\Facades\Validator;
use Backpack\CRUD\app\Library\CrudPanel\Traits\Input;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class StockEntriesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class StockEntriesCrudController extends BaseCrudController
{
    /**
     * @var StockEntries
     */
    private $stockEntries;
    /**
     * @var StockItems
     */
    private $stockItems;

    /**
     * @var Backpack User
     */
    private $user;
    /**
     * @var StockItemDetails
     */
    private $barcodeDetails;
    /**
     * @var ItemQuantityDetail
     */
    private $itmQtyDtl;
    /**
     * @var BatchQuantityDetail
     */
    private $batchQtyDtl;

    private $multiple_barcode;

    /**
     * @param StockEntries $stockEntries
     * @param StockItems $stockItems
     * @param StockItemDetails $barcodeDetails
     */
    public function __construct(
        StockEntries $stockEntries,
        StockItems $stockItems,
        StockItemDetails $barcodeDetails,
        ItemQuantityDetail $itmQtyDtl,
        BatchQuantityDetail $batchQtyDtl
    ) {
        parent::__construct();

        $this->stockEntries = $stockEntries;
        $this->stockItems = $stockItems;
        $this->barcodeDetails = $barcodeDetails;
        $this->itmQtyDtl = $itmQtyDtl;
        $this->batchQtyDtl = $batchQtyDtl;
    }

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\StockEntries::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/stock-entries');
        CRUD::setEntityNameStrings('', 'stock entries');
        $this->user = backpack_user();
        // $this->crud->enableExportButtons();
        $this->multiple_barcode = backpack_user()->superOrganizationEntity->multiple_barcode;
        $this->filterDataByStoreUser(["sup_org_id" => $this->user->sup_org_id, "store_id" => $this->user->store_id]);
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
            [
                'label' => 'Store',
                'type' => 'select',
                'name' => 'store_id', // the column that contains the ID of that connected entity;
                'entity' => 'mstStore', // the method that defines the relationship in your Model
                'attribute' => 'name_en', // foreign key attribute that is shown to user
                'model' => MstStore::class
            ],
            [
                'label' => 'Stock Status',
                'type' => 'model_function',
                'function_name' => 'getStockStatus', // the method that defines the relationship in your Model
            ],
            [
                'label' => 'Batch No',
                'type' => 'model_function',
                'function_name' => 'getBatchNo', // the method that defines the relationship in your Model
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
        $this->crud->addButtonFromView('top', 'excelImport', 'excelImport', 'end');
        $this->crud->addButtonFromModelFunction('top', 'stockEntriesSampleExcel', 'stockEntriesSampleExcel', 'end');
        // $this->crud->removeButton('create');
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
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
        $batchNumbers = $this->getsequenceCode(1);
        $adjustmentNumbers = $this->getsequenceCode(6);
        $sequenceCodes = $this->sequence_type();
        $item_lists = $this->getItemList();

        $multiple_barcode = $this->multiple_barcode;
        return view('customAdmin.stockMgmt.create', compact('crud', 'item_lists', 'mstStoreName', 'multiple_barcode', 'storeList', 'batchNumbers', 'adjustmentNumbers', 'sequenceCodes'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        $request = $this->crud->validateRequest();

        $stockInput = $request->only([
            'comments',
            'gross_total',
            'total_discount',
            'taxable_amount',
            'tax_total',
            'net_amount',
            'sup_status_id',
            'store_id',
        ]);

        $sequenceCodes = $request->only(['batch_number', 'adjustment_no']);

        $stockInput['sup_org_id'] = $this->user->sup_org_id;
        $stockInput['created_by'] = $this->user->id;

        $statusCheck = $request->sup_status_id == SupStatus::APPROVED;

        if ($statusCheck) {
            if (!$this->user->is_stock_approver) abort(401);

            if(empty($sequenceCodes)){
                return response()->json([
                    'status' => 'failed',
                    'message' => "Failed to approve stock. Sequence Codes are not available"
                ]);
            }elseif(!array_key_exists('adjustment_no',$sequenceCodes)){
                return response()->json([
                    'status' => 'failed',
                    'message' => "Failed to approve stock. Stock Adjustment Sequence is not created."
                ]);
            }

            $stockInput['adjustment_no'] = $sequenceCodes['adjustment_no'];
            $stockInput['entry_date_bs'] = $request->entry_date_bs;
            $stockInput['entry_date_ad'] = $request->entry_date_ad;
            $stockInput['approved_by'] = $this->user->id;
        }
        if (!$request->itemWiseDiscount) {
            $stockInput['flat_discount'] = $request->flat_discount;
            $itemDiscount = null;
        } else {
            $stockInput['flat_discount'] = null;
        }
        try {
            DB::beginTransaction();
            $stock = $this->stockEntries->create($stockInput);
            $counter = null;

            foreach ($request->mst_item_id as $key => $val) {
                if ($request->mst_item_id[$key]) {
                    $itemArr = [
                        'stock_id' => $stock->id,
                        'sup_org_id' => $this->user->sup_org_id,
                        'mst_item_id' => $request->itemStockHidden[$key],
                        'available_total_qty' => $request->available_total_qty[$key],
                        'add_qty' => !$this->multiple_barcode ? $request->add_qty[$key]
                            : ($request->session()->has('barcode.barcode-' . $request->itemStockHidden[$key]) ?
                                count($request->session()->get('barcode.barcode-' . $request->itemStockHidden[$key])) : 0),
                        'total_qty' => $request->total_qty[$key],
                        'free_item' => !$this->multiple_barcode ? $request->free_item[$key] : null,
                        'discount' => isset($itemDiscount) ? $itemDiscount : (isset($request->discount[$key]) ? $request->discount[$key] : null),
                        'unit_cost_price' => $request->unit_cost_price[$key],
                        'unit_sales_price' => $request->unit_sales_price[$key],
                        'expiry_date' => $request->expiry_date[$key],
                        'tax_vat' => $request->tax_vat[$key],
                        'item_total' => $request->item_total[$key],
                        'store_id' => $request->store_id,
                    ];

                    if ($statusCheck) {
                        if(!array_key_exists('batch_number',$sequenceCodes)){
                            return response()->json([
                                'status' => 'failed',
                                'message' => "Failed to approve stock. Batch Number is not created."
                            ]);
                        }

                        $itemArr['batch_no'] = $sequenceCodes['batch_number'];

                        $this->saveQtyDetail($this->batchQtyDtl, $itemArr, 'batchQty');
                        $this->saveQtyDetail($this->itmQtyDtl, $itemArr, 'itemQty');
                    }

                    $stockItem = $this->stockItems->create($itemArr);
                    if ($request->session()->has('barcode.barcode-' . $request->itemStockHidden[$key])) {
                        $stockBarcodeDetails = $request->session()->get('barcode.barcode-' . $request->itemStockHidden[$key]);
                        $barcodeInsertArr = [];
                        foreach ($stockBarcodeDetails as $barcode => $barcodeItem) {
                            $barcodeArr = [
                                'stock_item_id' => $stockItem->id,
                                'barcode_details' => $barcode,
                                'item_id' => $barcodeItem,
                                'is_active' => $statusCheck ? true : false,
                                'sup_org_id' => $this->user->sup_org_id,
                                'store_id' => $request->store_id,
                            ];

                            if ($statusCheck) {
                                $barcodeArr['batch_no'] = $sequenceCodes['batch_number'];
                            }
                            array_push($barcodeInsertArr, $barcodeArr);
                        }
                        $this->barcodeDetails->insert($barcodeInsertArr);
                        $counter++;
                    }
                }
            }
            if (!$counter && $this->multiple_barcode) {
                return response()->json([
                    'status' => 'failed',
                    'message' => "Qty Zero, Please Enter Stock Quantity"
                ]);
            }
            DB::commit();

            Artisan::call('barcode-list:generate', [
                'super_org_id' => $this->user->sup_org_id
            ]);

            session()->forget('barcode');

            return response()->json([
                'status' => 'success',
                'message' => 'Stock added successfully',
                'route' => url($this->crud->route)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'failed',
                'message' => "Failed to create stock." . $e->getMessage()
            ]);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {

        $this->crud->allowAccess('update');

        $stock = $this->stockEntries->find($id);
        if (!isset($stock))
            abort(404);

        $this->setSessions($stock->items);

        // DD($stock->items);


        $storeList = $this->getFilteredStoreList();

        $batchNumbers = $this->getsequenceCode(1);
        $adjustmentNumbers = $this->getsequenceCode(6);
        $sequenceCodes = $this->sequence_type();

        /** Todo: Additional stock entry after approved */
        //        $batchList = $this->getFilteredBatchList();

        $crud = $this->crud;
        $item_lists = $this->getItemList();
        $multiple_barcode = $this->multiple_barcode;
        return view('customAdmin.stockMgmt.edit', compact('crud', 'item_lists', 'storeList', 'stock', 'multiple_barcode', 'batchNumbers', 'adjustmentNumbers', 'sequenceCodes'));
    }

    /**
     * @return bool|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update()
    {
        $this->crud->allowAccess('update');
        $request = $this->crud->validateRequest();

        $stockInput = $request->only([
            'comments',
            'gross_total',
            'total_discount',
            'flat_discount',
            'taxable_amount',
            'tax_total',
            'net_amount',
            'sup_status_id',
            'store_id'
        ]);

        $sequenceCodes = $request->only(['batch_number', 'adjustment_no']);

        try {
            DB::beginTransaction();
            $currentStock = $this->stockEntries->find($this->crud->getCurrentEntryId());
            $initialSupStatus = $currentStock->sup_status_id;

            $statusCheck = $request->sup_status_id == SupStatus::APPROVED;

            if (!$this->user->is_stock_approver) abort(401);

            if ($statusCheck && $currentStock->sup_status_id != SupStatus::APPROVED) {

                if(empty($sequenceCodes)){
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Failed to approve stock. Sequence Codes are not available"
                    ]);
                }elseif(!array_key_exists('adjustment_no',$sequenceCodes)){
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Failed to approve stock. Stock Adjustment Sequence is not created."
                    ]);
                }

                $stockInput['adjustment_no'] = $sequenceCodes['adjustment_no'];
                $stockInput['entry_date_bs'] = $request->entry_date_bs;
                $stockInput['entry_date_ad'] = $request->entry_date_ad;
                $stockInput['store_id'] = $request->store_id;
                $stockInput['approved_by'] = $this->user->id;
            }

            $currentStock->update($stockInput);

            $this->stockItems->destroy($currentStock->items->pluck('id'));

            foreach ($request->mst_item_id as $key => $val) {
                $itemArr = [
                    'stock_id' => $this->crud->getCurrentEntryId(),
                    'sup_org_id' => $this->user->sup_org_id,
                    'mst_item_id' => $request->itemStockHidden[$key],
                    'available_total_qty' => $request->available_total_qty[$key],
                    'add_qty' => !$this->multiple_barcode ? $request->add_qty[$key]
                        : ($request->session()->has('barcode.barcode-' . $request->itemStockHidden[$key]) ?
                            count($request->session()->get('barcode.barcode-' . $request->itemStockHidden[$key])) : 0),                    'total_qty' => $request->total_qty[$key],
                    'free_item' => !$this->multiple_barcode ? $request->free_item[$key] : null,
                    'discount' => $request->discount[$key],
                    'unit_cost_price' => $request->unit_cost_price[$key],
                    'unit_sales_price' => $request->unit_sales_price[$key],
                    'expiry_date' => $request->expiry_date[$key],
                    'tax_vat' => $request->tax_vat[$key],
                    'item_total' => $request->item_total[$key],
                    'store_id' => $request->store_id,
                ];
                if ($statusCheck && $initialSupStatus != SupStatus::APPROVED) {

                    if(!array_key_exists('batch_number',$sequenceCodes)){
                        return response()->json([
                            'status' => 'failed',
                            'message' => "Failed to approve stock. Batch Number is not created."
                        ]);
                    }

                    $itemArr['batch_no'] = $sequenceCodes['batch_number'];
                    $this->saveQtyDetail($this->batchQtyDtl, $itemArr, 'batchQty');
                    $this->saveQtyDetail($this->itmQtyDtl, $itemArr, 'itemQty');
                }
                $stockItem = $this->stockItems->create($itemArr);

                if ($request->session()->has('barcode.barcode-' . $request->itemStockHidden[$key])) {
                    $stockBarcodeDetails = $request->session()->get('barcode.barcode-' . $request->itemStockHidden[$key]);
                    $barcodeInsertArr = [];
                    foreach ($stockBarcodeDetails as $barcode => $barcodeItem) {
                        $barcodeArr = [
                            'stock_item_id' => $stockItem->id,
                            'barcode_details' => $barcode,
                            'item_id' => $barcodeItem,
                            'is_active' => $statusCheck ? true : false,
                            'sup_org_id' => $this->user->sup_org_id,
                            'store_id' => $request->store_id,
                        ];
                        if ($statusCheck) {
                            $barcodeArr['batch_no'] = $sequenceCodes['batch_number'];
                        }
                        array_push($barcodeInsertArr, $barcodeArr);
                    }
                    $this->barcodeDetails->insert($barcodeInsertArr);
                }
            }

            DB::commit();

            Artisan::call('barcode-list:generate', ['super_org_id' => $this->user->sup_org_id]);

            session()->forget('barcode');

            return response()->json([
                'status' => 'success',
                'message' => 'Stock added successfully',
                'route' => url($this->crud->route)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to update stock. Please contact your administrator' . $e->getMessage()
            ]);
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');
        try {
            DB::beginTransaction();

            $id = $this->crud->getCurrentEntryId() ?? $id;
            $stock = $this->stockEntries->find($id);

            $batchNo = $stock->items->pluck('batch_no');

            foreach ($batchNo as $btch) {
                $bqd = BatchQuantityDetail::where(['batch_no' => $btch, 'sup_org_id' => $this->user->sup_org_id])->first();
                $iqd = ItemQuantityDetail::where(['sup_org_id' =>  $this->user->sup_org_id, 'store_id' => $this->user->store_id, 'item_id' => $bqd->item_id])->first();
                $iqd->item_qty = $iqd->item_qty - $bqd->batch_qty;
                $iqd->save();
                $bqd->delete();
            }

            $relatedStockItemIds = $stock->items->pluck('id');
            $this->stockEntries->destroy($id);
            $this->stockItems->destroy($relatedStockItemIds);
            // Artisan::call('barcode-list:generate', [
            //     'super_org_id' => $this->user->sup_org_id
            // ]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
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

        $data['items'] = $data['entry']->items;

        $data['crud'] = $this->crud;

        return view('customAdmin.stockMgmt.show', [
            'entry' => $data['entry'],
            'items' => $data['items'],
            'crud' => $data['crud'],
        ]);
    }

    /**
     * @param MstItem $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function stockItem(MstItem $item)
    {
        $taxRate = $item->tax_vat;
        $availableQty = ItemQuantityDetail::select('id', 'item_qty')
            ->where([
                'sup_org_id' => $this->user->sup_org_id,
                'store_id' =>  $this->user->store_id,
                'item_id' => $item->id
            ])
            ->orderBy('id', 'desc')
            ->first()
            ->item_qty ?? 0;
        return response()->json([
            'taxRate' => $taxRate,
            'availableQty' => $availableQty
        ]);
    }

    /**
     * @param $id
     * @param $from
     * @param $to
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function StockItemHistory($id, $from, $to)
    {
        $historyData = DB::table('stock_entries as se')
            ->join('stock_items as si', 'se.id', 'si.stock_id')
            ->where('si.mst_item_id', $id)
            ->where('se.sup_status_id', SupStatus::APPROVED)
            ->whereBetween('se.entry_date_ad', [$from, $to])
            ->select('si.*', 'se.entry_date_ad as entry_date', 'se.created_by', 'se.approved_by')
            ->get();

        $item = MstItem::find($id);
        $itemName = $item->name;

        return view('customAdmin.stockMgmt.partials.history', compact('historyData', 'itemName'));
    }

    /**
     * @param Request $request
     * @param $itemId
     * @return \Illuminate\Http\JsonResponse
     */

    public function barcodeSessionStore(Request $request, $itemId)
    {
        try {
            $barcodeDetails = array();
            $barcodeDetails_inside = array();
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
            // dd($barcodeDetails);
            // dd($barcodeDetails,$barcodeDetails_inside);
            $db_exist_barcode = DB::table('stock_items_details')->select('barcode_details')->get()->toArray();
            $db_exist_barcode_arr = [];
            foreach ($db_exist_barcode as $barcode) {

                $db_exist_barcode_arr[] = $barcode->barcode_details;
            }

            $barcodeDetails = array_diff($barcodeDetails, $db_exist_barcode_arr);

            $count = count($barcodeDetails);
            // dd($barcodeDetails,$count);
            // dd($db_exist_barcode_arr, $barcodeDetails, $count);


            // dd($barcodeDetails,$count);

            $barcodeArrayWithId = [];
            foreach ($barcodeDetails as $detail)
                $barcodeArrayWithId['barcode-' . $itemId][$detail] = $itemId;


            // dd($barcodeArrayWithId);
            if ($request->session()->has('barcode')) {
                $barcodeArray = $request->session()->pull('barcode');


                $barcodeArrayWithId = array_merge($barcodeArrayWithId, $barcodeArray);
                $request->session()->put('barcode', $barcodeArrayWithId);
            } else {
                $barcodeArray = ['barcode' => $barcodeArrayWithId];
                $request->session()->put($barcodeArray);
            }
            if ($count > 0) {
                return response()->json([
                    'status' => 'success',
                    'count' => $count,
                    'barcodeList' => getBarcodeJson($this->user->sup_org_id)
                ]);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'You must have at least one item'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to save barcodes. ' . $e->getMessage()
            ]);
        }
    }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return string
    //  */
    // public function destroy($id)
    // {
    //     $this->crud->hasAccessOrFail('delete');

    //     // get entry ID from Request (makes sure its the last ID for nested resources)
    //     $id = $this->crud->getCurrentEntryId() ?? $id;

    //     return $this->crud->delete($id);
    // }

    /**
     * @param $key
     * @return \Illuminate\Http\JsonResponse
     */
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
            $arr['barcode']['barcode-' . $item->mst_item_id] = $item->barcodeDetails->pluck('item_id', 'barcode_details')->toArray();
        }
        session()->put($arr);
    }

    /**
     * @param $qtyDtl
     * @param array $itemArr
     * @param $type
     * @return void
     * @throws \Exception
     */
    private function saveQtyDetail($qtyDtl, array $itemArr, $type)
    {
        $arr = [
            'sup_org_id' => $this->user->sup_org_id,
            'store_id' => $this->user->store_id,
            'item_id' => $itemArr['mst_item_id'],
            'created_by' => $this->user->id,
        ];


        $flag = false;
        if ($type == 'batchQty') {
            $arr['batch_no'] = $itemArr['batch_no'];
            $arr['batch_qty'] = $itemArr['add_qty'] + $itemArr['free_item'];
            $arr['batch_price'] = $itemArr['unit_sales_price'];
            $arr['batch_from'] = 'stock-mgmt';

            /** Todo: Additional stock entry after approved */
            //            $existingQtyDtl = $qtyDtl
            //                ->where('batch_no', $arr['batch_no'])
            //                ->where('batch_from', 'stock-mgmt')
            //                ->first();
            //            if($existingQtyDtl){
            //
            //            }
        } else if ($type == 'itemQty') {
            $arr['item_qty'] = $itemArr['total_qty'];
            $existingItemQty = $qtyDtl->where([
                'sup_org_id' => $this->user->sup_org_id,
                'store_id' => $this->user->store_id,
                'item_id' => $itemArr['mst_item_id'],
            ])->first();

            $flag = $existingItemQty ?? false;
        } else {
            throw new \Exception('Stock details could not be updated');
        }

        if ($flag) {
            $flag->item_qty = $itemArr['total_qty'];
            $flag->save();
        } else {
            $qtyDtl->create($arr);
        }
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function stockStatus()
    {
        // $data = ItemQuantityDetail::join('batch_qty_detail as bqd', 'bqd.item_id', '=', 'item_qty_detail.item_id')
        //     ->where('bqd.sup_org_id', $this->user->sup_org_id)
        //     ->where('bqd.store_id', $this->user->store_id)
        //     ->where('item_qty_detail.sup_org_id', $this->user->sup_org_id)
        //     ->where('item_qty_detail.store_id', $this->user->store_id)
        //     ->where('bqd.batch_qty', '>', 0)
        //     ->orderBy('bqd.created_at', 'desc')
        //     ->get();
        $sum_total = null;
        $dataArr = [];
        $data = StockItemDetails::where('is_active', true)
                ->groupBy("item_id")
                ->groupBy("store_id")
                ->select('item_id')
                ->addSelect('store_id')
                ->addSelect(DB::raw('count(*) as count'));

        $data = $this->filterQueryByUser($data);
        $data = $data->get()->toArray();

        foreach ($data as $key => $value) {
            $item = MstItem::find($value['item_id']);
            $batchQty = BatchQuantityDetail::Where(['item_id' => $value['item_id'],['batch_qty', '>', 0]]);
            $batchQty = $this->filterQueryByUser($batchQty);
            $batchQty = $batchQty->get();

            $salesQty = SaleItems::select(DB::RAW('SUM(total_qty) as total_sold'))->where('item_id', $value['item_id']);
            // $salesQty = $this->filterQueryByUser($salesQty);
            // $salesQty = $salesQty->get();
            if ($this->user->isSystemUser()) {
                $salesQty = $salesQty->get();
            }
            elseif($this->user->isOrganizationUser()) {
                $salesQty = $salesQty->whereRelation('sales', 'sup_org_id', '=', $this->user->sup_org_id)->get();
            }
            elseif($this->user->isStoreUser()) {
                $salesQty = $salesQty->whereRelation('sales', 'store_id', '=', $this->user->store_id)->get();
            }

            $arrData = [
                'item_qty' =>  $value['count'],
                'item' => $item,
            ];
            $arrData['item']['batchQty'] = $batchQty;
            $arrData['item']['soldQty'] = $salesQty;
            array_push($dataArr, $arrData);
            $sum_total += $value['count'];
        }
        $data = $dataArr;
        return view('stock_status', compact('data', 'sum_total'));
    }

    public function barcodeReport()
    {
        return view('customAdmin.dhansharReports.barcodeReport');
    }

    public function getBarcodeDetail($num)
    {
        $data = StockItemDetails::where('barcode_details', $num)->with(['stockItem', 'salesItem'])->first();

        // dd($data);
        return view('customAdmin.dhansharReports.partials.barcode_report_data', compact('data'));
    }

    public function listEntriesPdfDownload()
    {
        $stocks = StockEntries::where('sup_org_id', $this->user->sup_org_id)
            ->get();
        $view = 'pdfPages.listOperations.stockEntries';
        $html = view($view, compact('stocks'))->render();
        $file_name = 'Stock Entries.pdf';
        $res = PdfPrint::printPortrait($html, $file_name);
        return $res;
    }

    public function listStatusPdfDownload()
    {
        $stocks = ItemQuantityDetail::join('batch_qty_detail as bqd', 'bqd.item_id', '=', 'item_qty_detail.item_id')
            ->where('bqd.sup_org_id', $this->user->sup_org_id)
            ->where('item_qty_detail.sup_org_id', $this->user->sup_org_id)
            ->where('bqd.batch_qty', '>', 0)
            ->get();

        $view = 'pdfPages.listOperations.stockStatus';
        $html = view($view, compact('stocks'))->render();
        $file_name = 'Stock Status.pdf';
        $res = PdfPrint::printPortrait($html, $file_name);
        return $res;
    }

    public function listStatusExcelDownload()
    {
        $date = Carbon::now()->toDateString();
        return Excel::download(new StockEntriesExcel, 'stock-entries - ' . $date . '.xlsx');
    }

    // split barcode and push
    public function getSplitedBarcode(Request $request)
    {
        $data = $request->data;
        return response()->json();
    }

    public function stockEntriesImportExcel(Request $request)
    {
        $total_errors = [];

        $validator = Validator::make($request->all(), [
            'stockExcelFileName' => 'required',
            'flatDiscountAmount' => 'integer|nullable'
        ]);

        try {

            $stockImport = new StockEntriesExcelImport;
            Excel::import($stockImport, request()->file('stockExcelFileName'));

            //!! Barcode error from system json file and excel barcode row
            if (!empty($stockImport->barcode_errors)) {

                array_push($total_errors, array_filter($stockImport->barcode_errors));

                $barcode_errors = $stockImport->barcode_errors;
                return view('excel-errors', compact('barcode_errors'));
            }

            //!! different data error for same item
            if (!empty($stockImport->differeent_data_errors)) {

                array_push($total_errors, array_filter($stockImport->differeent_data_errors));

                // $differeent_data_errors = $stockImport->differeent_data_errors;
                // return view('excel-errors', compact('differeent_data_errors'));

            }

            //!! Item doesnot exists in databse errors
            if (!empty($stockImport->item_exists_error)) {

                array_push($total_errors, array_filter($stockImport->item_exists_error));

                // $item_errors = $stockImport->item_exists_error;
                // return view('excel-errors', compact('item_errors'));
            }

            if (!empty(array_filter($total_errors))) {
                return view('excel-errors', compact('total_errors'));
            }

            return 1;
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

            //!! validation Errors
            $database_validation_errors = $e->failures();
            return view('excel-errors', compact('database_validation_errors'));
        }
    }
}
