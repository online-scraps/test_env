<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstStore;
use App\Models\AppSetting;
use App\Models\SeriesNumber;
use Illuminate\Http\Request;
use App\Models\GeneralLedger;
use App\Models\VoucherDetail;
use App\Models\AccountSetting;
use App\Models\JournalVoucher;
use App\Models\ChartsOfAccount;
use App\Models\SupOrganization;
use App\Base\BaseCrudController;
use App\Base\Traits\VoucherCommon;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\JournalVoucherRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class JournalVoucherCrudController extends BaseCrudController
{
    use VoucherCommon;

    public function __construct(JournalVoucher $voucher){
        parent::__construct();
        $this->voucher = $voucher;
    }

    public function setup()
    {
        CRUD::setModel(JournalVoucher::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/journal-voucher');
        CRUD::setEntityNameStrings('Journal Voucher', 'Journal Voucher');
        $this->crud->addClause('where','station', 1);

        if(backpack_user()->isStoreUser()){
            $this->crud->addClause('where','store_id', backpack_user()->store_id);
        }
        if(backpack_user()->isOrganizationUser() && backpack_user()->store_id == null){
            $this->crud->addClause('where','sup_org_id', backpack_user()->sup_org_id);
        }        
    }

    protected function setupListOperation(){
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

        $this->voucherCreate(2, JournalVoucher::JOURNALVOUCHER);

        return view('accounts.voucher.create', $this->data);
    }

    public function store(){
        $this->crud->hasAccessOrFail('create');

        $request = $this->crud->validateRequest();

        try{
            $this->voucherStore($request, JournalVoucher::JOURNALVOUCHER, 'JournalVoucher', JournalVoucher::class);

            \Alert::success(trans('backpack::crud.insert_success'))->flash();

            return response()->json([
                'status' => 'success',
                'message' => 'Journal Voucher added successfully',
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

        
        $this->voucherEdit($id, 2, JournalVoucher::JOURNALVOUCHER);
        
        return view('accounts.voucher.edit', $this->data);
    }

    public function update(){
        $this->crud->allowAccess('update');

        $id = $this->crud->getCurrentEntryId();
        $request = $this->crud->validateRequest();

        try{
            $this->voucherUpdate($request, 'JournalVoucher', $id, JournalVoucher::class);

            \Alert::success(trans('backpack::crud.update_success'))->flash();

            return response()->json([
                'status' => 'success',
                'message' => 'Journal Voucher updated successfully',
                'route' => url($this->crud->route),
            ]);
        }catch(\Throwable $th){
            DB::rollback();
            dd($th);
        }
    }

    public function getVoucherSeries(Request $request){
        $id = $request->id;

        $data['seriesNo'] = SeriesNumber::whereId($id)->first();
        $data['lastSeriesNo'] = JournalVoucher::select('voucher_no','series_no_id')->where('series_no_id',$id)->latest()->first();

        return $data;
    }
}