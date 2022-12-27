<?php

namespace App\Http\Controllers\Admin;

use App\Models\ChartsOfAccount;
use App\Base\BaseCrudController;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;
use App\Models\VoucherGroupSetting;
use App\Http\Requests\VoucherGroupSettingRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class VoucherGroupSettingCrudController extends BaseCrudController
{
    public function setup()
    {
        CRUD::setModel(\App\Models\VoucherGroupSetting::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/voucher-group-setting');
        CRUD::setEntityNameStrings('Voucher Group Setting', 'Voucher Group Setting');
    }

    protected function setupListOperation()
    {
        $cols = [
            $this->addRowNumberColumn(),
            $this->addSuperOrganizationColumn(),
            [
                'name' => 'voucher_group',
                'label' => 'Voucher Groups',
                'type' => 'custom_group_table',
                'columns' => [
                    'voucher_id' => 'Voucher',
                    'dr_cr' => 'Dr./Cr.',
                    'group_id' => 'Group',
                ],
            ],
        ];

        $this->crud->addColumns(array_filter($cols));
    }

    public function index(){
        if(backpack_user()->hasRole('superadmin')){
            $this->crud->hasAccessOrFail('list');

            $this->data['crud'] = $this->crud;
            $this->data['title'] = $this->crud->getTitle() ?? mb_ucfirst($this->crud->entity_name_plural);

            // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
            return view($this->crud->getListView(), $this->data);
        }else{
            return redirect('admin/voucher-group-setting/create');
        }
    }

    public function create(){
        $this->crud->hasAccessOrFail('create');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.add').' '.$this->crud->entity_name;

        $this->data['vouchers'] = $this->voucher_id();
        $this->data['dr_cr'] = $this->dr_cr();
        $this->data['groups'] = ChartsOfAccount::where('is_group', true)->where('primary_group', 0)->get(); 

        $user = backpack_user()->sup_org_id;
        $setting = VoucherGroupSetting::whereSupOrgId($user)->get();

        if(count($setting) > 0){
            return redirect('admin/voucher-group-setting/' . $setting[0]->id . '/edit');
        }else{
            return view('accounts.voucher_group_setting.form', $this->data);
        }
    }

    public function store(){
        $this->crud->hasAccessOrFail('create');

        $request = $this->crud->validateRequest();
        
        $voucherGroupSetting = $request->only([
            'sup_org_id',
            'voucher_id',
            'dr_cr',
            'group_id',
        ]);

        foreach($voucherGroupSetting['voucher_id'] as $key => $value){
            if(!isset($voucherGroupSetting['group_id'][$key])){
                $voucherGroupSetting['group_id'][$key] = null;
            }
        }

        $voucherGroupSetting = [
            'sup_org_id' => $voucherGroupSetting['sup_org_id'],
            'voucher_id' => json_encode($voucherGroupSetting['voucher_id']),
            'dr_cr' => json_encode($voucherGroupSetting['dr_cr']),
            'group_id' => json_encode($voucherGroupSetting['group_id']),
        ];

        DB::beginTransaction();

        try{
            VoucherGroupSetting::create($voucherGroupSetting);

            DB::commit();

            \Alert::success(trans('backpack::crud.insert_success'))->flash();

            return response()->json([
                'status' => 'success',
                'message' => 'Voucher Group Setting added successfully',
                'route' => url($this->crud->route),
            ]);
        }catch(\Throwable $th){
            DB::rollback();
            dd($th);
        }
    }

    public function edit($id){
        $this->crud->hasAccessOrFail('update');

        $id = $this->crud->getCurrentEntryId();

        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.edit').' '.$this->crud->entity_name;

        $this->data['vouchers'] = $this->voucher_id();
        $this->data['dr_cr'] = $this->dr_cr();
        $this->data['groups'] = ChartsOfAccount::select('id','name')->where('is_group', true)->where('primary_group', 0)->get();
        $this->data['voucher_group_setting'] = VoucherGroupSetting::whereId($id)->first();

        return view('accounts.voucher_group_setting.form', $this->data);
    }

    public function update(){
        $this->crud->hasAccessOrFail('update');

        $request = $this->crud->validateRequest();
        $id = $this->crud->getCurrentEntryId();

        $voucherGroupSetting = $request->only([
            'sup_org_id',
            'voucher_id',
            'dr_cr',
            'group_id',
        ]);

        foreach($voucherGroupSetting['voucher_id'] as $key => $value){
            if(!isset($voucherGroupSetting['group_id'][$key])){
                $voucherGroupSetting['group_id'][$key] = null;
            }
        }

        $voucherGroupSetting = [
            'sup_org_id' => $voucherGroupSetting['sup_org_id'],
            'voucher_id' => json_encode($voucherGroupSetting['voucher_id']),
            'dr_cr' => json_encode($voucherGroupSetting['dr_cr']),
            'group_id' => json_encode($voucherGroupSetting['group_id']),
        ];

        DB::beginTransaction();

        try{
            VoucherGroupSetting::whereId($id)->update($voucherGroupSetting);

            DB::commit();

            \Alert::success(trans('backpack::crud.update_success'))->flash();

            return response()->json([
                'status' => 'success',
                'message' => 'Voucher Group Setting added successfully',
                'route' => url($this->crud->route),
            ]);
        }catch(\Throwable $th){
            DB::rollback();
            dd($th);
        }
    }
}
