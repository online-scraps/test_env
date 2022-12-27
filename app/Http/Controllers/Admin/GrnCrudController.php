<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\BarcodeCollection;
use App\Http\Resources\BarcodeResource;
use App\Models\BatchNo;
use App\Models\BatchQuantityDetail;
use App\Models\Grn;
use App\Models\GrnItem;
use App\Models\GrnSequence;
use App\Models\ItemQuantityDetail;
use App\Models\MstItem;
use App\Models\GrnTypes;
use App\Models\MstStore;
use App\Models\SupStatus;
use App\Models\MstDiscMode;
use App\Models\PurchaseOrderType;
use App\Models\MstSupplier;
use App\Base\BaseCrudController;
use App\Http\Requests\GrnRequest;
use App\Models\PurchaseItem;
use Backpack\CRUD\app\Library\CrudPanel\Traits\Input;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;
use App\Models\PurchaseOrderDetail;
use App\Models\ReturnReason;
use App\Utils\PdfPrint;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class GrnCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class GrnCrudController extends BaseCrudController
{

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */

    protected $user;
    /**
     * @var ItemQuantityDetail
     */
    private $itmQtyDtl;
    /**
     * @var BatchQuantityDetail
     */
    private $batchQtyDtl;

    /**
     * @param GrnItem $grnItem
     * @param Grn $grnDetails
     * @param ItemQuantityDetail $itmQtyDtl
     * @param BatchQuantityDetail $batchQtyDtl
     */
    public function __construct(GrnItem             $grnItem,
                                Grn                 $grnDetails,
                                ItemQuantityDetail  $itmQtyDtl,
                                BatchQuantityDetail $batchQtyDtl)
    {
        parent::__construct();
        $this->grnItem = $grnItem;
        $this->grnDetails = $grnDetails;
        $this->itmQtyDtl = $itmQtyDtl;
        $this->batchQtyDtl = $batchQtyDtl;
    }

    public function setup()
    {
        $this->user = backpack_user();

        CRUD::setModel(\App\Models\Grn::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/grn');
        CRUD::setEntityNameStrings('', 'Goods Received Note ');
        // $this->crud->enableExportButtons();

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
                'name' => 'store_id',
                'type' => 'select',
                'entity' => 'storeEntity',
                'attribute' => 'name_en',
                'model' => MstStore::class,

            ],
//            $this->addSuperOrganizationColumn(),

            [
                'name' => 'supplier_id',
                'type' => 'select',
                'entity' => 'supplierEntity',
                'attribute' => 'name_en',
                'model' => MstSupplier::class,

            ],
//            [
//                'name' => 'po_date',
//                'label' => 'Purchase Order Date',
//                'type' => 'nepali_date',
//
//                'attributes' => [
//                    'id' => 'date_bs',
//                    'relatedId' => 'date_ad'
//                ],
//            ],
//            [
//                'name' => 'purchase_order_id',
//                'type' => 'text',
//                'label' => 'Purchase Order Id',
//
//            ],
//            [
//                'name' => 'dc_date',
//                'type' => 'nepali_date',
//                'label' => 'Dc Date',
//
//                'attributes'=>[
//                    'id'=>'date_bs',
//                    'relatedId'=>'date_ad'
//                ],
//            ],
//            [
//                'name'=>'dc_no',
//                'type'=>'number',
//                'label'=>'Dc Number',
//
//            ],
//            [
//                'name'=>'invoice_no',
//                'type'=>'number',
//                'label'=>'Invoice Number',
//
//            ],
//            [
//                'name' => 'invoice_date',
//                'type' => 'nepali_date',
//                'label' => 'Invoice Date',
//
//                'attributes'=>[
//                    'id'=>'date_bs',
//                    'relatedId'=>'date_ad'
//                ],
//            ],
            [
                'name'=>'grn_no',
                'type'=>'text',
                'label'=>'Grn Number',

            ],
            [
                'name' => 'grn_date',
                'type' => 'nepali_date',
                'label' => 'Grn Date',

                'attributes'=>[
                    'id'=>'date_bs',
                    'relatedId'=>'date_ad'
                ],
            ],
            [
                'name' => 'approved_by',
                'type' => 'select',
                'entity' => 'approvedByEntity',
                'attribute' => 'name',
                'model' => User::class,
            ],
//            [
//                'name' => 'gross_amt',
//                'type' => 'number',
//                'label' => 'Gross Amount',
//
//            ],
//            [
//                'name' => 'discount_amt',
//                'type' => 'number',
//                'label' => 'Discount Amount',
//
//            ],
//            [
//                'name' => 'tax_amt',
//                'type' => 'number',
//                'label' => 'Tax Amount',
//
//            ],

//            [
//                'name' => 'other_charges',
//                'type' => 'number',
//                'label' => 'Other Charges',
//
//            ],
//            [
//                'name' => 'round_off',
//                'type' => 'number',
//                'label' => 'Round Off',
//
//            ],

            [
                'name' => 'net_amt',
                'type' => 'number',
                'label' => 'Net Amount',

            ],
            [
                'name' => 'comments',
                'type' => 'text',
                'label' => 'Comments',

            ],
            [
                'name' => 'status_id',
                'type' => 'select',
                'entity' => 'statusEntity',
                'attribute' => 'name_en',
                'model' => SupStatus::class,
            ],
        ];
        $this->crud->addColumns($cols);
        $this->crud->addButtonFromModelFunction('line', 'purchaseReturn', 'purchaseReturn', 'end');



        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    public function create()
    {
        $this->crud->hasAccessOrFail('create');
        $this->data['crud'] = $this->crud;
        $discount_modes = MstDiscMode::all();
        $store = MstStore::where('is_active', true)
            ->whereId($this->user->store_id)
            ->select('id', 'name_en')
            ->first();

        $po_types =  PurchaseOrderType::where('is_active', true)
            ->select('id', 'name_en')->get();

        $grn_types =  GrnTypes::where('is_active', true)
            ->select('id', 'name_en')->get();

        $suppliers = MstSupplier::where('is_active', true)
            ->where('sup_org_id', $this->user['sup_org_id'])
            ->select('id', 'name_en')
            ->get();


        $item_lists = MstItem::where('is_active', true)
            ->select('id', 'name', 'code')
            ->get();

        $this->data['item_lists'] = $item_lists;
        $this->data['store'] = $store;
        $this->data['grn_types'] = $grn_types;
        $this->data['po_types'] = $po_types;
        $this->data['suppliers'] =  $suppliers;
        $this->data['discount_modes'] = $discount_modes;
        $this->data['batchNumbers'] = $this->getsequenceCode(1);
        $this->data['grnNumbers'] = $this->getsequenceCode(2);
        $this->data['sequenceCodes'] = $this->sequence_type();
        return view('customAdmin.grn.partials.create', $this->data);
    }
    public function store()
    {
        $this->crud->hasAccessOrFail('create');
        $request = $this->crud->validateRequest();
        if (isset($request)) {
            $grnDetails = $request->only([
                'purchase_order_id',
                'po_date',
                'dc_date',
                'dc_no',
                'invoice_no',
                'invoice_date',
                'grn_no',
                'gross_amt',
                'discount_amt',
                'tax_amt',
                'other_charges',
                'round_off',
                'net_amt',
                'comments',
                'store_id',
                'grn_type_id',
                'status_id',
                'supplier_id',
                'sup_org_id',
            ]);

            $sequenceCodes = $request->only(['batch_number', 'grn_no']);


            $grnDetails['sup_org_id'] = $this->user->sup_org_id;
            $grnDetails['created_by'] = $this->user->id;

            $statusCheck = $request->status_id == SupStatus::APPROVED;
            if ($statusCheck) {
                if (!$this->user->is_stock_approver) abort(401);
                if(empty($sequenceCodes)){
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Failed to approve GRN. Sequence Codes are not available"
                    ]);
                }elseif(!array_key_exists('grn_no',$sequenceCodes)){
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Failed to approve GRN. GRN Sequence is not created."
                    ]);
                }
                $grnDetails['grn_no'] = $sequenceCodes['grn_no'];
                $grnDetails['approved_by'] = $this->user->id;
                $grnDetails['grn_date'] = dateToString(Carbon::now());
            }

            DB::beginTransaction();
            try {
                $grnId = Grn::create($grnDetails);
                foreach ($request->grn_item_name_hidden as $key => $val) {
                    $itemArray = [
                        'grn_id' => $grnId->id,
                        'purchase_qty' => $request->purchase_qty[$key],
                        'received_qty' => $request->received_qty[$key]??0,
                        'free_qty' => $request->free_qty[$key],
                        'invoice_qty' => $request->invoice_qty[$key]??0,
                        'total_qty' => $request->total_qty[$key],
                        'expiry_date' => $request->expiry_date[$key],
                        'discount' => $request->discount[$key],
                        'purchase_price' => $request->purchase_price[$key],
                        'sales_price' => $request->sales_price[$key],
                        'item_amount' => $request->item_amount[$key],
                        'tax_vat' => $request->tax_vat[$key],
                        'discount_mode_id' => $request->discount_mode_id[$key],
                        'mst_items_id' => $request->grn_item_name_hidden[$key],
                        'sup_org_id' => $this->user->sup_org_id,
                    ];
                    if ($itemArray['received_qty'] > 0) {
                        if ($statusCheck) {

                            // $itemArray['batch_no'] = BatchNo::where('sup_org_id', $this->user->sup_org_id)->first()->sequence_code . '-' . $grnId->id;
                            if(!array_key_exists('batch_number',$sequenceCodes)){
                                return response()->json([
                                    'status' => 'failed',
                                    'message' => "Failed to approve GRN. Batch Number is not created."
                                ]);
                            }

                            $itemArray['batch_no'] = $sequenceCodes['batch_number'];

                            $this->saveQtyDetail($this->batchQtyDtl, $itemArray, 'batchQty');
                            $this->saveQtyDetail($this->itmQtyDtl, $itemArray, 'itemQty');
                        }

                        GrnItem::create($itemArray);
                    }

                }
                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Grn added successfully',
                    'route' => url($this->crud->route)
                ]);
            }catch (\Exception $e){
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

        $grn =  $this->grnDetails->find($id);
        if (!isset($grn))
            abort(404);
        $discount_modes = MstDiscMode::all();
        $grn_types =  GrnTypes::where('is_active', true)
            ->select('id', 'name_en')->get();
        $po_types =  PurchaseOrderType::where('is_active', true)
            ->select('id', 'name_en')->get();

        $store = MstStore::where('is_active', true)
            ->where('store_user_id', $this->user->id)
            ->select('id', 'name_en')
            ->first();

        $suppliers = MstSupplier::where('is_active', true)
            ->where('sup_org_id', $this->user['sup_org_id'])
            ->select('id', 'name_en')
            ->get();

        $mstStoreName = MstStore::where('store_user_id', auth()->user()->id)->first()->name_en ?? 'n/a';

        $crud = $this->crud;
        $item_lists = MstItem::where('is_active', true)->get(['id', 'name', 'code']);

        $batchNumbers = $this->getsequenceCode(1);
        $grnNumbers = $this->getsequenceCode(2);
        $sequenceCodes = $this->sequence_type();

        return view('customAdmin.grn.partials.edit', compact('discount_modes','po_types', 'item_lists', 'grn_types', 'store', 'suppliers', 'crud', 'item_lists', 'mstStoreName', 'grn', 'batchNumbers', 'grnNumbers', 'sequenceCodes'));
    }
    public function update()
    {
        $this->crud->allowAccess('update');
        $request = $this->crud->validateRequest();
        $grnd = $request->only([
            'purchase_order_id',
            'po_date',
            'dc_date',
            'dc_no',
            'invoice_no',
            'invoice_date',
            'grn_no',
            'gross_amt',
            'discount_amt',
            'tax_amt',
            'other_charges',
            'round_off',
            'net_amt',
            'comments',
            'store_id',
            'grn_type_id',
            'status_id',
            'supplier_id',
        ]);

        $sequenceCodes = $request->only(['batch_number', 'grn_no']);

        try {
            DB::beginTransaction();
            $currentgrn = $this->grnDetails->find($this->crud->getCurrentEntryId());

            $initialSupStatus = $currentgrn->status_id;

            $statusCheck = $request->status_id == SupStatus::APPROVED;

            if (!$this->user->is_stock_approver) abort(401);

            if ($statusCheck && $currentgrn->status_id != SupStatus::APPROVED) {
                if(empty($sequenceCodes)){
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Failed to approve GRN. Sequence Codes are not available"
                    ]);
                }elseif(!array_key_exists('grn_no',$sequenceCodes)){
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Failed to approve GRN. GRN Sequence is not created."
                    ]);
                }


                $grnd['grn_no'] = $sequenceCodes['grn_no'];
                $grnd['approved_by'] = $this->user->id;
                $grnd['grn_date'] = dateToString(Carbon::now());
            }

            $currentgrn->update($grnd);
            $this->grnItem->destroy($currentgrn->grn_items->pluck('id'));

            foreach ($request->grn_item_name_hidden as $key => $val) {
                $itemArray = [
                    'grn_id' => $this->crud->getCurrentEntryId(),
                    'purchase_qty' => $request->purchase_qty[$key],
                    'received_qty' => $request->received_qty[$key]??0,
                    'free_qty' => $request->free_qty[$key],
                    'invoice_qty' => $request->invoice_qty[$key],
                    'total_qty' => $request->total_qty[$key],
                    'expiry_date' => $request->expiry_date[$key],
                    'discount' => $request->discount[$key],
                    'purchase_price' => $request->purchase_price[$key],
                    'sales_price' => $request->sales_price[$key],
                    'item_amount' => $request->item_amount[$key],
                    'tax_vat' => $request->tax_vat[$key],
                    'discount_mode_id' => $request->discount_mode_id[$key],
                    'mst_items_id' => $request->grn_item_name_hidden[$key],
                    'sup_org_id' => $this->user->sup_org_id,
                ];

                    if ($statusCheck && $initialSupStatus != SupStatus::APPROVED) {

                        if(!array_key_exists('batch_number',$sequenceCodes)){
                            return response()->json([
                                'status' => 'failed',
                                'message' => "Failed to approve GRN. Batch Number is not created."
                            ]);
                        }

                        $itemArray['batch_no'] = $sequenceCodes['batch_number'];
                        // $itemArray['batch_no'] = BatchNo::where('sup_org_id', $this->user->sup_org_id)->first()->sequence_code . '-' . $this->crud->getCurrentEntryId();
                        $this->saveQtyDetail($this->batchQtyDtl, $itemArray, 'batchQty');
                        $this->saveQtyDetail($this->itmQtyDtl, $itemArray, 'itemQty');
                    }
                    $this->grnItem->create($itemArray);


            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Goods Received Notes Updated successfully',
                'route' => url($this->crud->route)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'Failed to update Goods Received Notes. Please contact your administrator',
                'message' => $e->getMessage()
            ]);
        }
    }
    private function saveQtyDetail($qtyDtl, array $itemArr, $type)
    {
        $arr = [
            'sup_org_id' => $this->user->sup_org_id,
            'store_id' => $this->user->store_id,
            'item_id' => $itemArr['mst_items_id'],
            'created_by' => $this->user->id,
        ];

        $flag = false;
        if ($type == 'batchQty') {
            $arr['batch_no'] = $itemArr['batch_no'];
            $arr['batch_qty'] = $itemArr['received_qty'] + $itemArr['free_qty'];
            $arr['batch_price'] = $itemArr['sales_price'];
            $arr['batch_from'] = 'grn';

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
                'item_id' => $itemArr['mst_items_id'],
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

    public function grnDetails(MstItem $item)
    {

        $taxRate = $item->tax_vat;
        $discountMode = $item->discount_mode_id;
        $itemPrice = $item->item_price;
        return response()->json([
            'taxRate' => $taxRate,
            'discountMode' => $discountMode,
            'itemPrice' => $itemPrice,
        ]);
    }

    // public function grnHistoryDetails($id, $from, $to)
    // {

    //     $data = $this->grnDetail::with('grn_items')
    //         ->join('grn_items as gi', 'grns.id', 'gi.grn_id')
    //         ->where('gi.mst_items_id', $id)
    //         ->where('grns.status_id', SupStatus::APPROVED)
    //         ->whereBetween('grns.grn_date', [$from, $to])
    //         ->get();
    //     return view('customAdmin.grn.partials.history', compact('data'));
    // }

    public function grnItemHistory($id, $from, $to)
    {
        $datas = DB::table('grns as grn')
            ->join('grn_items as gi', 'grn.id', 'gi.grn_id')
            ->where('gi.mst_items_id', $id)
            // ->where('grn.status_id', SupStatus::APPROVED)
            ->whereBetween('grn.grn_date', [$from, $to])
            ->select('gi.*', 'grn.grn_no','grn.invoice_no','grn.invoice_date','grn.grn_date','grn.created_by')
            ->get();
        // dd($datas);

        $item = MstItem::find($id);
        $itemName = $item->name;

        return view('customAdmin.grn.partials.history', compact('datas', 'itemName'));
    }


    public function poItemFetchForGrn($po_no, $from, $to, $suppliers, $po_types)
    {

        $datas = DB::table('purchase_order_details as pod')
            // ->whereBetween('pod.po_date', [$from, $to])
            ->where('pod.purchase_order_type_id', $po_types)
            // ->where('pod.supplier_id', $suppliers)
            // ->where('po.status_id', SupStatus::APPROVED)
            ->get();
        // dd($datas[0]->store_id);

        // $item = MstItem::find($po_no);
        // $itemName = $item->name;

        return view('customAdmin.grn.partials.po_fetch', compact('datas'));
    }

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');
        try {
            DB::beginTransaction();
            $id = $this->crud->getCurrentEntryId() ?? $id;
            $grn = $this->grnDetails->find($id);
            $relatedGrnItemIds = $grn->grn_items->pluck('id');
            $this->grnItem->destroy($relatedGrnItemIds);
            $this->grnDetails->destroy($id);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }


    public function listPdfDownload()
    {
        $grns = Grn::all();
        $view = 'pdfPages.listOperations.grn';
        $html = view($view, compact('grns'))->render();
        $file_name = 'Goods Received Note.pdf';
        $res = PdfPrint::printPortrait($html, $file_name);
        return $res;
    }
    public function purchaseReturn($id){


        $grn = Grn::find($id);

        $grn_items=GrnItem::where('grn_id',$id)->get();
        $reasons=ReturnReason::whereSupOrgId($this->user->sup_org_id)->whereIsActive(true)->get();

        $flag=true;


        $this->data['crud']=$this->crud;
        $this->data['grn']=$grn;
        $this->data['grn_items']=$grn_items;
        $this->data['flag']=$flag;
        $this->data['reasons']=$reasons;
        return view('customAdmin.purchaseReturn.purchase_return',$this->data);
    }
    public function fetchPODforGRN($po_no){
        $pod=PurchaseOrderDetail::where('purchase_order_num',$po_no)->first();

        if(isset($pod)){
            $discount_modes = MstDiscMode::all();
           $poItems=PurchaseItem::wherePoId($pod->id)->get();
           $data=[
               'view'=>view('customAdmin.grn.partials.tr_grn',compact('poItems','discount_modes'))->render(),
               'pod' =>$pod,

           ];
           return ($data);
        //    return response()->json([
        //     'pod' => $pod,
        //     'items'=>$poItems
        //     ]);
        }
        else{
            return response()->json([
                'nodata' => 'nodata'
            ]);
        }
    }
}
