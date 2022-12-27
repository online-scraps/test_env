<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstStore;
use App\Models\SeriesNumber;
use App\Models\VoucherDetail;
use App\Models\ReceiptVoucher;
use App\Models\ChartsOfAccount;
use App\Models\SupOrganization;
use App\Base\BaseCrudController;
use App\Base\Traits\VoucherCommon;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;
use App\Http\Requests\ReceiptVoucherRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class ReceiptVoucherCrudController extends BaseCrudController
{
    use VoucherCommon;

    public function __construct(ReceiptVoucher $voucher){
        parent::__construct();
        $this->voucher = $voucher;
    }

    public function setup()
    {
        CRUD::setModel(\App\Models\ReceiptVoucher::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/receipt-voucher');
        CRUD::setEntityNameStrings('receipt voucher', 'receipt vouchers');
        $this->crud->addClause('where','station', 4);

        if(backpack_user()->isStoreUser()){
            $this->crud->addClause('where','store_id', backpack_user()->store_id);
        }
        if(backpack_user()->isOrganizationUser() && backpack_user()->store_id == null){
            $this->crud->addClause('where','sup_org_id', backpack_user()->sup_org_id);
        }
    }

    protected function setupListOperation()
    {
        $cols = [
            $this->addRowNumberColumn(),
            $this->addSuperOrganizationColumn(),
            $this->addStoreColumn(),
            [
                'name' => 'voucher_no',
                'label' => 'Voucher No.',
            ],
            [
                'name' => 'voucher_date',
                'label' => 'Voucher Date',
                'type' => 'model_function',
                'function_name' => 'getVoucherDate',
            ],
            [
                'name' => 'total_dr_amount',
                'label' => 'Total Dr. Amount',
            ],
            [
                'name' => 'total_cr_amount',
                'label' => 'Total Cr. Amount',
            ],
        ];
        $this->crud->addColumns(array_filter($cols));
    }

    public function create(){
        $this->crud->hasAccessOrFail('create');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.add').' '.$this->crud->entity_name;

        $this->voucherCreate(3, ReceiptVoucher::RECEIPTVOUCHER);

        return view('accounts.voucher.create', $this->data);
    }

    public function store(){
        $this->crud->hasAccessOrFail('create');

        $request = $this->crud->validateRequest();

        try{
            $this->voucherStore($request, ReceiptVoucher::RECEIPTVOUCHER, 'ReceiptVoucher', ReceiptVoucher::class);

            \Alert::success(trans('backpack::crud.insert_success'))->flash();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Receipt Voucher added successfully',
                'route' => url($this->crud->route),
            ]);
        }catch(\Throwable $th){
            DB::rollback();
            dd($th);
        }
    }

    public function edit($id){
        $this->crud->allowAccess('edit');
        
        $this->data['voucher'] = $this->voucher->find($id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.add').' '.$this->crud->entity_name;

        $this->voucherEdit($id, 3, ReceiptVoucher::RECEIPTVOUCHER);

        return view('accounts.voucher.edit', $this->data);
    }

    public function update(){
        $this->crud->allowAccess('update');

        $id = $this->crud->getCurrentEntryId();
        $request = $this->crud->validateRequest();

        try{
            $this->voucherUpdate($request, 'ReceiptVoucher', $id, ReceiptVoucher::class);

            \Alert::success(trans('backpack::crud.update_success'))->flash();

            return response()->json([
                'status' => 'success',
                'message' => 'Receipt Voucher updated successfully',
                'route' => url($this->crud->route),
            ]);
        }catch(\Throwable $th){
            DB::rollback();
            dd($th);
        }
    }
}
