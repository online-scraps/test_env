<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstStore;
use App\Models\SeriesNumber;
use App\Models\VoucherDetail;
use App\Models\PaymentVoucher;
use App\Models\ChartsOfAccount;
use App\Models\SupOrganization;
use App\Base\BaseCrudController;
use App\Base\Traits\VoucherCommon;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;
use App\Http\Requests\PaymentVoucherRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class PaymentVoucherCrudController extends BaseCrudController
{
    use VoucherCommon;

    public function __construct(PaymentVoucher $voucher)
    {
        parent::__construct();
        $this->voucher = $voucher;
    }

    public function setup()
    {
        CRUD::setModel(\App\Models\PaymentVoucher::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/payment-voucher');
        CRUD::setEntityNameStrings('Payment Voucher', 'Payment Voucher');
        $this->crud->addClause('where', 'station', 3);
        $this->user = backpack_user();

        if (backpack_user()->isStoreUser()) {
            $this->crud->addClause('where', 'store_id', backpack_user()->store_id);
        }
        if (backpack_user()->isOrganizationUser() && backpack_user()->store_id == null) {
            $this->crud->addClause('where', 'sup_org_id', backpack_user()->sup_org_id);
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

    public function create()
    {
        $this->crud->hasAccessOrFail('create');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.add') . ' ' . $this->crud->entity_name;

        $this->voucherCreate(4, PaymentVoucher::PAYMENTVOUCHER);

        return view('accounts.voucher.create', $this->data);
    }

    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        $request = $this->crud->validateRequest();

        try {
            $this->voucherStore($request, PaymentVoucher::PAYMENTVOUCHER, 'PaymentVoucher', PaymentVoucher::class);

            \Alert::success(trans('backpack::crud.insert_success'))->flash();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment Voucher added successfully',
                'route' => url($this->crud->route),
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            dd($th);
        }
    }

    public function edit($id)
    {
        $this->crud->allowAccess('edit');

        $this->data['voucher'] = $this->voucher->find($id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.add') . ' ' . $this->crud->entity_name;

        $this->voucherEdit($id, 5, 'PaymentVoucher');

        return view('accounts.voucher.edit', $this->data);
    }

    public function update()
    {
        $this->crud->allowAccess('update');

        $id = $this->crud->getCurrentEntryId();
        $request = $this->crud->validateRequest();

        try {
            $this->voucherUpdate($request, 'PaymentVoucher', $id, PaymentVoucher::class);

            \Alert::success(trans('backpack::crud.update_success'))->flash();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment Voucher updated successfully',
                'route' => url($this->crud->route),
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            dd($th);
        }
    }
}
