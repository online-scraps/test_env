<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstStore;
use App\Models\MstCountry;
use App\Models\AccountSetting;
use App\Models\ChartsOfAccount;
use App\Models\SupOrganization;
use App\Base\BaseCrudController;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;
use App\Http\Requests\ChartsOfAccountRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ChartsOfAccountCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ChartsOfAccountCrudController extends BaseCrudController
{
    public function setup()
    {
        CRUD::setModel(\App\Models\ChartsOfAccount::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/charts-of-account');
        CRUD::setEntityNameStrings('Charts of Account', 'Charts of Account');
    }

    public function index(){
        $this->crud->hasAccessOrFail('list');

        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? mb_ucfirst($this->crud->entity_name_plural);
        
        $this->data['roots'] = ChartsOfAccount::whereGroupId(null)->where('ledger_type', null)->orderBy('id')->get();
        $this->data['account_setting'] = AccountSetting::whereSupOrgId(backpack_user()->sup_org_id)->first();

        return view('accounts.charts_of_account.list', $this->data);
    }

    public function create(){
        $this->crud->hasAccessOrFail('create');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.add').' '.$this->crud->entity_name;

        $this->data['account_setting'] = AccountSetting::whereSupOrgId(backpack_user()->sup_org_id)->first();

        $this->data['ledgers'] = $this->getLedgerData();
        $this->data['groups'] = $this->getGroupData();

        $this->data['stores'] = MstStore::all();
        $this->data['organizations'] = SupOrganization::all();
        $this->data['countries'] = MstCountry::get();

        return view('accounts.charts_of_account.create', $this->data);
    }

    public function store(){
        $this->crud->hasAccessOrFail('create');
        $request = $this->crud->validateRequest();

        if(isset($request)){
            if($request->group == 0){
                $request->request->set('is_ledger', true);
                // dd($request);
                $group = $request->only([
                    'name',
                    'alias',
                    'print_name',
                    'group_id',
                    'opening_balance',
                    'dr_cr',
                    'address',
                    'country_id',
                    'email',
                    'pan',
                    'mobile_no',
                    'tel_no',
                    'fax',
                    'contact_person',
                    'maintain_bill_by_bill_balance',
                    'credit_day_for_sales',
                    'credit_day_for_purchase',
                    'specify_default_sales_type',
                    'default_sales_type',
                    'specify_default_purchase_type',
                    'default_purchase_type',
                    'freeze_sale_type',
                    'freeze_purchase_type',
                    'bank_details',
                    'beneficary_name',
                    'bank_name',
                    'bank_ac_no',
                    'ifsc_code',
                    'enable_email_query',
                    'enable_sms_query',
                    'remarks',
                    'store_id',
                    'sup_org_id',
                    'is_ledger',
                    'ledger_type',
                    'ledger_id',
                ]);

                try{
                    $coaId = ChartsOfAccount::create($group);

                    DB::commit();

                    \Alert::success(trans('backpack::crud.insert_success'))->flash();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Charts of Acocunt created successfully',
                        'route' => url($this->crud->route.'/create'),
                    ]);
                }catch(\Exception $e){
                    DB::rollback();
                    dd($e);
                }
            }else{
                $request->request->set('is_group', true);
                $group = $request->only([
                    'name',
                    'alias',
                    'primary_group',
                    'group_id',
                    'is_group',
                    'store_id',
                    'sup_org_id',
                ]);

                DB::beginTransaction();
                try{

                    $coaId = ChartsOfAccount::create($group);

                    DB::commit();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Group created successfully.',
                        'id' => $coaId->id,
                    ]);
                }catch(\Exception $e){
                    DB::rollback();
                    dd($e);
                }
            }
        }
    }

    public function edit($id){
        $this->crud->hasAccessOrFail('update');
        $id = $this->crud->getCurrentEntryId() ?? $id;

        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.edit').' '.$this->crud->entity_name;
        $this->data['account_setting'] = AccountSetting::whereSupOrgId(backpack_user()->sup_org_id)->first();


        $this->data['ledgers'] = $this->getLedgerData();
        $this->data['groups'] = $this->getGroupData();

        $this->data['stores'] = MstStore::all();
        $this->data['organizations'] = SupOrganization::all();
        $this->data['countries'] = MstCountry::get();
        $this->data['data'] = ChartsOfAccount::whereId($id)->first();

        return view('accounts.charts_of_account.edit', $this->data);
    }

    public function update(){
        $this->crud->hasAccessOrFail('update');
        $request = $this->crud->validateRequest();
        $id = $this->crud->getCurrentEntryId();

        if(isset($request)){
            if($request->group == 0){
                $group = $request->only([
                    'name',
                    'alias',
                    'print_name',
                    'group_id',
                    'opening_balance',
                    'dr_cr',
                    'address',
                    'country_id',
                    'email',
                    'pan',
                    'mobile_no',
                    'tel_no',
                    'fax',
                    'contact_person',
                    'maintain_bill_by_bill_balance',
                    'credit_day_for_sales',
                    'credit_day_for_purchase',
                    'specify_default_sales_type',
                    'default_sales_type',
                    'specify_default_purchase_type',
                    'default_purchase_type',
                    'freeze_sale_type',
                    'freeze_purchase_type',
                    'bank_details',
                    'beneficary_name',
                    'bank_name',
                    'bank_ac_no',
                    'ifsc_code',
                    'enable_email_query',
                    'enable_sms_query',
                    'remarks',
                    'is_ledger',
                    'ledger_type',
                    'ledger_id',
                ]);

                try{
                    $coaId = ChartsOfAccount::whereId($id)->update($group);

                    DB::commit();

                    \Alert::success(trans('backpack::crud.update_success'))->flash();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Charts of Acocunt updated successfully',
                        'route' => url($this->crud->route),
                    ]);
                }catch(\Exception $e){
                    DB::rollback();
                    dd($e);
                }
            }else{
                $group = $request->only([
                    'name',
                    'alias',
                    'primary_group',
                    'group_id',
                ]);

                DB::beginTransaction();
                try{

                    ChartsOfAccount::whereId($id)->update($group);

                    DB::commit();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Group updated successfully.',
                    ]);
                }catch(\Exception $e){
                    DB::rollback();
                    dd($e);
                }
            }
        }
    }

    public function createGroup(){
        $data['crud'] = $this->crud;
        $data['stores'] = MstStore::all();
        $data['organizations'] = SupOrganization::all();
        $data['groups'] = $this->getGroupData();

        return view('accounts.charts_of_account.partials.edit_group', $data);
    }

    public function getGroupData(){
        if(backpack_user()->isOrganizationUser()){
            $data = ChartsOfAccount::whereIsGroup(true)->whereSupOrgId(backpack_user()->sup_org_id)->orWhere('sup_org_id',1)->get();
        }else{
            $data = ChartsOfAccount::whereIsGroup(true)->orderBy('id')->get();
        }

        return $data;
    }

    public function getLedgerData(){
        if(backpack_user()->isOrganizationUser()){
            $data = ChartsOfAccount::where('ledger_type',1)->whereSupOrgId(backpack_user()->sup_org_id)->orWhere('sup_org_id',1)->where('is_ledger',true)->get();
        }else{
            $data = ChartsOfAccount::where('is_ledger', true)->where('ledger_type',1)->orderBy('id')->get();
        }

        return $data;
    }
}