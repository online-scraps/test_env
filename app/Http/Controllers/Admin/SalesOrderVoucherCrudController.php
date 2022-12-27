<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Sales;
use App\Models\MstItem;
use App\Models\MstUnit;
use App\Utils\PdfPrint;
use App\Models\SaleItems;
use App\Models\SupStatus;
use App\Models\AppSetting;
use App\Models\MstCustomer;
use App\Models\MstDiscMode;
use App\Utils\NumberToWords;
use App\Models\SupOrganization;
use App\Base\BaseCrudController;
use App\Models\StockItemDetails;
use App\Models\SalesOrderVoucher;
use App\Models\ItemQuantityDetail;
use Illuminate\Support\Facades\DB;
use App\Models\BatchQuantityDetail;
use App\Models\SalesOrderVoucherItems;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Request;
use App\Http\Requests\SalesOrderVoucherRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SalesOrderVoucherCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SalesOrderVoucherCrudController extends BaseCrudController
{
    

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */

    private $salesOrderVoucherEntries;
    private $salesOrderVoucherItems;
    private $user;

    public function __construct(SalesOrderVoucher $salesOrderVoucherEntries, SalesOrderVoucherItems $salesOrderVoucherItems, User $user)
    {
        parent::__construct();

        $this->salesOrderVoucherEntries = $salesOrderVoucherEntries;
        $this->salesOrderVoucherItems = $salesOrderVoucherItems;

    }
    public function setup()
    {
        CRUD::setModel(\App\Models\SalesOrderVoucher::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/sales-order-voucher');
        CRUD::setEntityNameStrings('sales order ', 'sales order ');

        $this->user = $user = backpack_user();

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
                // 1-n relationship
                'label'     => 'Full Name', // Table column heading
                'type'      => 'select',
                'name'      => 'customer_id', // the column that contains the ID of that connected entity;
                'entity'    => 'customerEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_en', // foreign key attribute that is shown to user
                'model'     => MstCustomer::class, // foreign key model
            ],
           
            [
                'name' => 'bill_no',
                'type' => 'model_function',
                'label' => 'Bill Number',
                'function_name' => 'getBill',
            ],
            
            [
                'name' => 'bill_date_ad',
                'type' => 'text',
                'label' => 'Billed Date',
            ],
            
            [
                'name' => 'net_amt',
                'type' => 'number',
                'label' => ' Net Amt',
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
        // $this->crud->addButtonFromModelFunction('line', 'printInvoiceNoHeader', 'printInvoiceNoHeader', 'beginning');

        // if($this->crud->addClause('where','status_id', 2)){
            $this->crud->addButtonFromView('line', 'show', 'show', 'beginning');
        // }

        // $this->crud->addButtonFromModelFunction('line', 'printReturnsInvoice', 'printReturnsInvoice', 'end');
        // $this->crud->addButtonFromModelFunction('line', 'printReturnsInvoiceNoHeader', 'printReturnsInvoiceNoHeader', 'end');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @return void
     */

    public function create()
    {
        $this->user = backpack_user();
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

        return view('customAdmin.salesordervoucher.salesorder', $this->data);
    }

    public function store()
    {
        $this->user = backpack_user();

        $request = $this->crud->validateRequest();

        $this->crud->hasAccessOrFail('create');
        $request = $this->crud->validateRequest();
        if (isset($request)) {
            $salesInput = $request->only([
                'bill_no',
                'bill_type',
                'full_name',
                'address',
                'contact_number',
                'pan_vat',
                'company_name',
                'bill_date_bs',
                'bill_date_ad',
                'discount_type',
                'discount',
                'remarks',
                'gross_amt',
                'discount_amt',
                'taxable_amt',
                'total_tax_vat',
                'net_amt',
                'transaction_date_ad',
                'sup_org_id',
                'store_id',
                'status_id',
                'discount_approver_id',
                'is_active',
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

            $statusCheck = $request->status_id == SupStatus::APPROVED;
            $billNo = $this->setMetaSequesnce('\App\Models\Sales', 3, 'bill_no');
            if ($statusCheck) {
                if($billNo['status'] == 'success'){
                    $salesInput['bill_no'] = $billNo['result'];
                }elseif($billNo['status'] == 'error'){
                    return response()->json([
                                'status' => 'failed',
                                'message' => "Failed to create sale. ".$billNo['result']
                            ]);
                }
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

                $stock = $this->salesOrderVoucherEntries->create($salesInput);

                $saleItem = [];
                foreach ($request->item_id as $key => $val) {
                    $unit = MstUnit::where('name_en', $request->unit_id[$key])->first();

                    $itemArr = [
                        'sales_order_voucher_id' => $stock->id,
                        'item_id' => $request->itemSalesHidden[$key],
                        'total_qty' => $request->total_qty[$key],
                        'unit_id' => $unit->id,
                        'item_discount' => $request->item_discount[$key],
                        'tax_vat' => $request->tax_vat[$key],
                        'item_total' => $request->item_total[$key],
                        'item_price' => $request->unit_cost_price[$key],
                    ];
                    if ($request->status_id == SupStatus::APPROVED) {
                        $totalQty = $request->total_qty[$key];
                    }

                    $stockItem =  $this->salesOrderVoucherItems->create($itemArr);
                }

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Sales order added successfully',
                    'route' => url($this->crud->route)
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                if($e->getMessage() == 'Undefined array key "bill_no"'){
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Invoice Sequence Already Exists. Try Another One."
                    ]);
                }
                return response()->json([
                    'status' => 'failed',
                    'message' => $e->getMessage()
                ]);
            }
        }
    }

    public function edit($id)
    {
        $sales = $this->salesOrderVoucherEntries->find($id);
        if (!isset($sales))
            abort(404);
        $this->crud->allowAccess('edit');
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
        $this->data['customers'] = MstCustomer::where([['is_customer', true], ['is_active', true], ['is_coorporate', false]])->get();
        $this->data['coorporateCustomers'] = MstCustomer::where([['is_customer', true], ['is_active', true], ['is_coorporate', true]])->get()->unique('company_name');

        return view('customAdmin.salesordervoucher.edit', $this->data);
    }

    public function update()
    {
        $this->crud->allowAccess('update');
        $request = $this->crud->validateRequest();
        $statusCheck = $request->status_id == SupStatus::APPROVED;

        $salesInput = $request->only([
            'bill_no',
            'bill_type',
            'full_name',
            'address',
            'customer_id',
            'contact_number',
            'pan_vat',
            'company_name',
            'bill_date_bs',
            'bill_date_ad',
            'discount_type',
            'discount',
            'remarks',
            'gross_amt',
            'discount_amt',
            'taxable_amt',
            'total_tax_vat',
            'net_amt',
            'transaction_date_ad',
            'sup_org_id',
            'store_id',
            'status_id',
            'discount_approver_id',
            'is_active',
        ]);

        $customerInput = $request->only([
            'bill_type',
            'full_name',
            'customer_id',
            'hidden_bill_type',
            'hidden_gender_id',
            'address',
            'contact_number',
            'company_name',
            'pan_vat',
        ]);

        $billNo = $this->setMetaSequesnce('\App\Models\Sales', 3, 'bill_no');
        if ($request->status_id == SupStatus::APPROVED) {
          
            if($billNo['status'] == 'success'){
                $salesInput['bill_no'] = $billNo['result'];
            }elseif($billNo['status'] == 'error'){
                return response()->json([
                            'status' => 'failed',
                            'message' => "Failed to create sale. ".$billNo['result']
                        ]);
            }
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
            $currentSales = $this->salesOrderVoucherEntries->find($this->crud->getCurrentEntryId());
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
            $currentSales->update($salesInput);
            
            $this->salesOrderVoucherItems->destroy($currentSales->saleItems->pluck('id'));

            $saleItem = [];
            foreach ($request->item_id as $key => $val) {
                $unit = MstUnit::where('name_en', $request->unit_id[$key])->first();

                $itemArr = [
                    'sales_order_voucher_id' => $this->crud->getCurrentEntryId(),
                    'item_id' => $request->itemSalesHidden[$key],
                    'total_qty' =>$request->total_qty[$key],
                    'unit_id' => $unit->id,
                    'item_discount' => $request->item_discount[$key],
                    'tax_vat' => $request->tax_vat[$key],
                    'item_total' => $request->item_total[$key],
                    'item_price' => $request->unit_cost_price[$key],
                ];
                $saleb =  $this->salesOrderVoucherItems->create($itemArr);
            }

            DB::commit();
            // Artisan::call('barcode-list:generate', ['super_org_id' => $this->user->sup_org_id]);
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

        $data['entry'] = SalesOrderVoucher::where('bill_no', $data['entry']->bill_no)->where('status_id', 2)->first();

        $data['items'] = $data['entry']->saleItems;

        $data['crud'] = $this->crud;

        return view('customAdmin.salesordervoucher.show', [
            'entry' => $data['entry'],
            'items' => $data['items'],
            'crud' => $data['crud'],
        ]);
    }

    public function stockItem(MstItem $item)
    {
        $taxRate = $item->tax_vat;
        $availableQty = ItemQuantityDetail::select('id', 'item_qty')
            ->where('item_id', $item->id)
            ->where('store_id', $this->user->store_id)
            ->where('sup_org_id', $this->user['sup_org_id'])
            ->orderBy('id', 'desc')
            ->first();
            // dd($availableQty);
        $available_qty = 0;
        if($availableQty){
            $available_qty = $availableQty->item_qty;
        }
        $is_price_editable = $item->is_price_editable;
        $unit = $item->mstUnitEntity->name_en;
        return response()->json([
            'taxRate' => $taxRate,
            'availableQty' => $available_qty,
            'unit' => $unit,
            'item_price' => $item->item_price,
            'is_price_editable' => $is_price_editable
        ]);
    }

    public function salesInvoiceQueryString()
    {
        return "
            SELECT
            sl.id,
            sl.bill_date_ad as bill_date_ad,
            sl.bill_date_bs as bill_date_bs,
            sl.transaction_date_ad as transaction_date,
            ct.name_en as buyer_name,
            ct.company_name as buyer_company_name,
            ct.address as buyer_address,
            ct.pan_no as buyer_pan,
            ct.contact_number as contact_number,
            sl.gross_amt as gross_amt,
            sl.discount_amt as discount_amt,
            sl.taxable_amt as taxable_amt,
            sl.total_tax_vat as total_tax_vat,
            sl.net_amt as net_amt,
            sl.created_by,
            sl.transaction_date_ad as transaction_date_ad,
            sl.bill_no as bill_no,
            ss.id,

            u.name as user_name,
            ms.name_lc as store_name,
            ms.email as store_email,
            ms.phone_no as store_phone

            FROM sales_order_voucher as sl

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
            si.sales_order_voucher_id as sales_items_id,
            si.item_id as item_id,
            si.unit_id as unit_id,
            mi.name as item_name,
            si.total_qty as total_qty,
            si.tax_vat as tax_amount,
            mu.name_lc as unit_name,
            si.item_price as item_price,
            si.item_total as item_total
            FROM sales_order_voucher_items as si

            LEFT JOIN sales_order_voucher as sl on si.sales_order_voucher_id = sl.id
            LEFT JOIN mst_items as mi on si.item_id = mi.id
            LEFT JOIN mst_units as mu on si.unit_id = mu.id

            WHERE si.sales_order_voucher_id = ?
        ";
    }

   

    public function printInvoice($id)
    {
        $sales = DB::select($this->salesInvoiceQueryString(),[$id]);

        $sales_items = DB::select($this->salesItemsInvoiceQueryString(),[$id]);

        // dd($sales_items);

        $sales_items_total = $sales_items[0]->item_price * $sales_items[0]->total_qty;

        $sales_items[0]->total_payable_price = $sales_items_total;

        $sales[0]->netAmtWords = NumberToWords::ConvertToEnglishWord($sales[0]->net_amt);

        $sales_bill = SalesOrderVoucher::find($id);

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
        $view = 'pdfPages.invoiceSalesOrder';

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

        $sales_bill = SalesOrderVoucher::find($id);

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

        $view = 'pdfPages.noHeaderInvoiceSalesOrder';

        if ((isset($header_footer_data->background))) {
            $html = view($view, compact('sales', 'sales_items', 'header_footer_data', 'background_encoded'))->render();
        } else {
            $html = view($view, compact('sales', 'sales_items', 'header_footer_data'))->render();
        }
        $file_name = 'Invoice - ' . $bill_no . '.pdf';

        $res = PdfPrint::printPortrait($html, $file_name);
        return $res;
    }
    

    
}
