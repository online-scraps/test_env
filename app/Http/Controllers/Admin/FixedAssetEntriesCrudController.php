<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\BatchNo;
use App\Models\MstItem;
use App\Utils\PdfPrint;
use App\Models\MstStore;
use App\Models\SaleItems;
use App\Models\SupStatus;
use App\Models\StockItems;
use App\Models\StockEntries;
use App\Models\FixedAssetItems;
use App\Base\BaseCrudController;
use App\Models\StockItemDetails;
use App\Models\FixedAssetEntries;
use App\Models\StockAdjustmentNo;
use App\Exports\StockEntriesExcel;
use App\Models\ItemQuantityDetail;
use Illuminate\Support\Facades\DB;
use App\Models\BatchQuantityDetail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request;
use App\Http\Requests\FixedAssetRequest;
use App\Imports\StockEntriesExcelImport;
use Illuminate\Support\Facades\Validator;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class FixedAssetEntriesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FixedAssetEntriesCrudController extends BaseCrudController
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
        FixedAssetEntries $stockEntries,
        FixedAssetItems $stockItems,
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
        CRUD::setModel(FixedAssetEntries::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/fixed-asset-entries');
        CRUD::setEntityNameStrings('', 'fixed assets');
        $this->user = backpack_user();
        // $this->crud->enableExportButtons();
        $this->multiple_barcode = backpack_user()->superOrganizationEntity->multiple_barcode;
        $this->filterDataByStoreUser(["sup_org_id" => $this->user->sup_org_id, "store_id" => $this->user->store_id]);
        $this->crud->isFixedAsset = true;
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
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $this->crud->hasAccessOrFail('create');
        $mstStoreName = $this->user->storeEntity->name_en ?? 'n/a';
        $crud = $this->crud;
        $storeList = $this->getFilteredStoreList();
        $item_lists = $this->getFixedAssetItemList();
        $batchNumbers = $this->getsequenceCode(1);
        $adjustmentNumbers = $this->getsequenceCode(10);
        $sequenceCodes = $this->sequence_type();
        $multiple_barcode = $this->multiple_barcode;
        return view('customAdmin.stockMgmt.fixedAsset.create', compact('crud', 'item_lists', 'mstStoreName', 'multiple_barcode', 'storeList', 'batchNumbers', 'adjustmentNumbers', 'sequenceCodes'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        $request = $this->crud->validateRequest();

        $stockInput = $request->only([
            'store_id',
            'comments',
            'gross_total',
            'total_depreciation',
            'taxable_amount',
            'tax_total',
            'net_amount',
            'upload_bill',
            'status_id',
        ]);

        $sequenceCodes = $request->only(['batch_number', 'adjustment_no']);

        if(!empty($stockInput['upload_bill'])){
            $uploadBill = $stockInput['upload_bill']->store('stockMngt/Fixed-asset', 'uploads');
        }else{
            $uploadBill = null;
        }

        $stockInput['upload_bill'] = $uploadBill;
        $stockInput['sup_org_id'] = $this->user->sup_org_id;
        $stockInput['created_by'] = $this->user->id;

        $statusCheck = $request->status_id == SupStatus::APPROVED;
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
                    'message' => "Failed to approve stock. Fixed Asset Stock Adjustment Sequence not created."
                ]);
            }
            $stockInput['adjustment_no'] = $sequenceCodes['adjustment_no'];
            $stockInput['entry_date_bs'] = $request->entry_date_bs;
            $stockInput['entry_date_ad'] = $request->entry_date_ad;
            $stockInput['approved_by'] = $this->user->id;
        }

        if (!$request->itemWiseDiscount) {
            $stockInput['flat_depreciation'] = $request->flat_depreciation;
            $itemDiscount = null;
        } else {
            $stockInput['flat_depreciation'] = null;
        }

        try {
            DB::beginTransaction();
            $stock = $this->stockEntries->create($stockInput);
            $counter = null;
            foreach ($request->mst_item_id as $key => $val) {
                if($request->mst_item_id[$key]){
                $itemArr = [
                    'fixed_asset_entry_id' => $stock->id,
                    'sup_org_id' => $this->user->sup_org_id,
                    'mst_item_id' => $request->itemStockHidden[$key],
                    'available_total_qty' => $request->available_total_qty[$key],
                    'add_qty' => !$this->multiple_barcode ? $request->add_qty[$key]
                        : ($request->session()->has('barcode.barcode-' . $request->itemStockHidden[$key]) ?
                            count($request->session()->get('barcode.barcode-' . $request->itemStockHidden[$key])) : 0),
                    'total_qty' => $request->total_qty[$key],
                    'free_item' => !$this->multiple_barcode ? $request->free_item[$key] : null,
                    'depreciation' => isset($itemDiscount) ? $itemDiscount : (isset($request->depreciation[$key]) ? $request->depreciation[$key] : null),
                    'unit_cost_price' => $request->unit_cost_price[$key],
                    'expiry_date' => $request->expiry_date[$key],
                    'tax_vat' => $request->tax_vat[$key],
                    'item_total' => $request->item_total[$key],
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
                // dd($stockItem);
                if ($request->session()->has('barcode.barcode-' . $request->itemStockHidden[$key])) {
                    $stockBarcodeDetails = $request->session()->get('barcode.barcode-' . $request->itemStockHidden[$key]);
                    $barcodeInsertArr = [];
                    foreach ($stockBarcodeDetails as $barcode => $barcodeItem) {
                        $barcodeArr = [
                            'fixed_asset_item_id' => $stockItem->id,
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
        if(!$counter && $this->multiple_barcode){
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
            'message' => "Failed to create stock. Sequence is not Set. " . $e->getMessage()
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

        // dd($stock->items);

        // dd(session()->get('barcode'));

        $this->setSessions($stock->items);

        $storeList = $this->getFilteredStoreList();
        $batchNumbers = $this->getsequenceCode(1);
        $adjustmentNumbers = $this->getsequenceCode(10);
        $sequenceCodes = $this->sequence_type();
        $item_lists = $this->getFixedAssetItemList();

        $crud = $this->crud;
        $multiple_barcode = $this->multiple_barcode;

        return view('customAdmin.stockMgmt.fixedAsset.edit', compact('crud', 'item_lists', 'storeList', 'stock', 'multiple_barcode', 'batchNumbers', 'adjustmentNumbers', 'sequenceCodes'));
    }

    /**
     * @return bool|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update()
    {
        $this->crud->allowAccess('update');
        $request = $this->crud->validateRequest();
        $previousUpdateBill = $this->crud->getCurrentEntry()->upload_bill;

        $stockInput = $request->only([
            'comments',
            'gross_total',
            'total_depreciation',
            'flat_depreciation',
            'taxable_amount',
            'tax_total',
            'net_amount',
            'status_id',
            'store_id',
        ]);

        $sequenceCodes = $request->only(['batch_number', 'adjustment_no']);

        if(!empty($stockInput['upload_bill'])){
            $uploadBill = $stockInput['upload_bill']->store('stockMngt/Fixed-asset', 'uploads');
        }else{
            $uploadBill = $previousUpdateBill;
        }
        $stockInput['upload_bill'] = $uploadBill;
        try {
            DB::beginTransaction();
            $currentStock = $this->stockEntries->find($this->crud->getCurrentEntryId());
            $initialSupStatus = $currentStock->status_id;

            $statusCheck = $request->status_id == SupStatus::APPROVED;
            if (!$this->user->is_stock_approver) abort(401);

            if ($statusCheck && $currentStock->status_id != SupStatus::APPROVED) {
                if(empty($sequenceCodes)){
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Failed to approve stock. Sequence Codes are not available"
                    ]);
                }elseif(!array_key_exists('adjustment_no',$sequenceCodes)){
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Failed to approve stock. Fixed Asset Stock Adjustment Sequence is not created."
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
                    'fixed_asset_entry_id' => $this->crud->getCurrentEntryId(),
                    'sup_org_id' => $this->user->sup_org_id,
                    'mst_item_id' => $request->itemStockHidden[$key],
                    'available_total_qty' => $request->available_total_qty[$key],
                    'add_qty' => !$this->multiple_barcode ? $request->add_qty[$key]
                        : ($request->session()->has('barcode.barcode-' . $request->itemStockHidden[$key]) ?
                            count($request->session()->get('barcode.barcode-' . $request->itemStockHidden[$key])) : 0),
                    'total_qty' => $request->total_qty[$key],
                    'free_item' => !$this->multiple_barcode ? $request->free_item[$key] : null,
                    'depreciation' => $request->depreciation[$key],
                    'unit_cost_price' => $request->unit_cost_price[$key],
                    'expiry_date' => $request->expiry_date[$key],
                    'tax_vat' => $request->tax_vat[$key],
                    'item_total' => $request->item_total[$key],
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
                            'fixed_asset_item_id' => $stockItem->id,
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
            $relatedStockItemIds = $stock->items->pluck('id');
            $this->stockEntries->destroy($id);
            $this->stockItems->destroy($relatedStockItemIds);

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

        return view('customAdmin.stockMgmt.fixedAsset.show', [
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
            if (!$request->has('barcode_details'))
                throw new \Exception('Please scan through a barcode reader.');

            $barcodeDetails = $request->barcode_details;

            // dd($barcodeDetails);
            $count = count($barcodeDetails);

            // dd($barcodeDetails, $count);


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
            // $arr['batch_price'] = $itemArr['unit_sales_price'];
            $arr['batch_from'] = 'stock-mgmt';

            /** Todo: Additional stock entry after approved */
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
            $salesQty = $this->filterQueryByUser($salesQty);
            $salesQty = $salesQty->get();

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
        return view('customAdmin.dhansharReports.partials.barcode_report_data', compact('data'));
    }
}
