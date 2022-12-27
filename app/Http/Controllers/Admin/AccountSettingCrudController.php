<?php

namespace App\Http\Controllers\Admin;

use App\Models\AccountSetting;
use App\Base\BaseCrudController;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;
use App\Http\Requests\AccountSettingRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AccountSettingCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AccountSettingCrudController extends BaseCrudController
{
    public function setup()
    {
        CRUD::setModel(\App\Models\AccountSetting::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/account-setting');
        CRUD::setEntityNameStrings('account setting', 'account settings');
    }

    protected function setupListOperation()
    {
        $cols = [
            $this->addRowNumberColumn(),
            $this->addSuperOrganizationColumn(),
            [
                'name' => 'bill_by_bill',
                'label' => 'Bill By Bill',
                'type' => 'check',
            ]
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
            return redirect($this->crud->route.'/create');
        }
    }

    public function create(){
        $this->crud->hasAccessOrFail('create');
        
        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.add').' '.$this->crud->entity_name;

        $user = backpack_user()->sup_org_id;
        $setting = AccountSetting::whereSupOrgId($user)->get();

        if(count($setting) > 0){
            return redirect($this->crud->route . '/' . $setting[0]->id . '/edit');
        }else{
            return view('accounts.account_setting.from', $this->data);
        }
    }

    public function store(){
        $this->crud->hasAccessOrFail('create');

        $request = $this->crud->validateRequest();

        if(isset($request)){
            $accountSetting = $request->only([
                'sup_org_id',
                'bill_by_bill',
                'credit_limits',
                'targets',
                'cost_centers',
                'ac_wise_intrest_rate',
                'ledger_reconciliation',
                'show_ac_current_balance',
                'balance_sheet_stock_updation',
                'single_entry',
                'posting_in_ac',
                'party_dashboard',
                'dashboard_after_selecting_party',
                'maintain_ac_category',
                'ac_category_caption',
                'salesman_broker_reporting',
                'budgets',
                'royalty_calculation',
                'company_act_depreciation',
                'maintain_sub_ledgers',
                'maintain_multiple_ac',
                'multiple_currency',
                'decimal_place',
                'maintain_image_note',
                'bank_reconciliation',
                'bank_instrument_detail',
                'post_dated_cheque',
                'cheque_printing',
            ]);

            DB::beginTransaction();
            
            try{
                AccountSetting::create($accountSetting);

                DB::commit();

                \Alert::success(trans('backpack::crud.insert_success'))->flash();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Account Setting added successfully',
                    'route' => url($this->crud->route),
                ]);
            }catch(\Throwable $th){
                DB::rollback();
                dd($th);
            }
        }
    }

    public function edit($id){
        $this->crud->hasAccessOrFail('update');
        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        $this->crud->setOperationSetting('fields', $this->crud->getUpdateFields());

        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.edit').' '.$this->crud->entity_name;

        $this->data['id'] = $id;
        $this->data['image_note_configure'] = DB::table('maintain_image_note_configure')->where('account_setting_id', $id)->first();

        $this->data['account_settings'] = AccountSetting::whereId($id)->first();

        return view('accounts.account_setting.from', $this->data);
    }

    public function update(){
        $this->crud->hasAccessOrFail('update');
        $request = $this->crud->validateRequest();
        $id = $this->crud->getCurrentEntryId();

        if(isset($request)){
            $accountSetting = $request->only([
                'sup_org_id',
                'bill_by_bill',
                'credit_limits',
                'targets',
                'cost_centers',
                'ac_wise_intrest_rate',
                'ledger_reconciliation',
                'show_ac_current_balance',
                'balance_sheet_stock_updation',
                'single_entry',
                'posting_in_ac',
                'party_dashboard',
                'dashboard_after_selecting_party',
                'maintain_ac_category',
                'ac_category_caption',
                'salesman_broker_reporting',
                'budgets',
                'royalty_calculation',
                'company_act_depreciation',
                'maintain_sub_ledgers',
                'maintain_multiple_ac',
                'multiple_currency',
                'decimal_place',
                'maintain_image_note',
                'bank_reconciliation',
                'bank_instrument_detail',
                'post_dated_cheque',
                'cheque_printing',
            ]);

            DB::beginTransaction();
            
            try{
                AccountSetting::whereId($id)->update([
                    'sup_org_id' => $request->sup_org_id,
                    'bill_by_bill' => isset($request->bill_by_bill) ? true : false,
                    'credit_limits' => isset($request->credit_limits) ? true : false,
                    'targets' => isset($request->targets) ? true : false,
                    'cost_centers' => isset($request->cost_centers) ? true : false,
                    'ac_wise_intrest_rate' => isset($request->ac_wise_intrest_rate) ? true : false,
                    'ledger_reconciliation' => isset($request->ledger_reconciliation) ? true : false,
                    'show_ac_current_balance' => isset($request->show_ac_current_balance) ? true : false,
                    'balance_sheet_stock_updation' => isset($request->balance_sheet_stock_updation) ? $request->balance_sheet_stock_updation : null,
                    'single_entry' => isset($request->single_entry) ? true : false,
                    'posting_in_ac' => isset($request->posting_in_ac) ? true : false,
                    'party_dashboard' => isset($request->party_dashboard) ? true : false,
                    'dashboard_after_selecting_party' => isset($request->dashboard_after_selecting_party) ? $request->dashboard_after_selecting_party : null,
                    'maintain_ac_category' => isset($request->maintain_ac_category) ? true : false,
                    'ac_category_caption' => isset($request->ac_category_caption) ? $request->ac_category_caption : null,
                    'salesman_broker_reporting' => isset($request->salesman_broker_reporting) ? true : false,
                    'budgets' => isset($request->budgets) ? true : false,
                    'royalty_calculation' => isset($request->royalty_calculation) ? true : false,
                    'company_act_depreciation' => isset($request->company_act_depreciation) ? true : false,
                    'maintain_sub_ledgers' => isset($request->maintain_sub_ledgers) ? true : false,
                    'maintain_multiple_ac' => isset($request->maintain_multiple_ac) ? true : false,
                    'multiple_currency' => isset($request->multiple_currency) ? true : false,
                    'decimal_place' => isset($request->decimal_place) ? $request->decimal_place : null,
                    'maintain_image_note' => isset($request->maintain_image_note) ? true : false,
                    'bank_reconciliation' => isset($request->bank_reconciliation) ? true : false,
                    'bank_instrument_detail' => isset($request->bank_instrument_detail) ? true : false,
                    'post_dated_cheque' => isset($request->post_dated_cheque) ? true : false,
                    'cheque_printing' => isset($request->cheque_printing) ? true : false,
                ]);

                DB::commit();

                \Alert::success(trans('backpack::crud.update_success'))->flash();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Account Setting updated successfully',
                    'route' => url($this->crud->route),
                ]);
            }catch(\Throwable $th){
                DB::rollback();
                dd($th);
            }
        }
    }

    public function saveImageNoteConfigure(){
        $request = $this->crud->validateRequest();
        $account_setting_id = AccountSetting::whereSupOrgId(backpack_user()->id)->pluck('id')->first();

        DB::beginTransaction();
        try{
            if($account_setting_id){
                AccountSetting::whereId($account_setting_id)->update([
                    'maintain_image_note' => $request->maintain_image_note,
                ]);
            }else{
                $accountSetting = AccountSetting::create([
                    'sup_org_id' => $request->sup_org_id,
                    'maintain_image_note' => $request->maintain_image_note,
                ]);
                $account_setting_id = $accountSetting->id;
            }

            DB::table('maintain_image_note_configure')->insert([
                'account_setting_id' => $account_setting_id,
                'image_with_account_master' => isset($request->image_with_account_master) ? true : false,
                'note_with_account_master' => isset($request->note_with_account_master) ? true : false,
                'account_master_char' => isset($request->account_master_char) ? $request->account_master_char : null,
                'account_note_in_data_entry' => isset($request->account_note_in_data_entry) ? true : false,
                'image_with_account_voucher' => isset($request->image_with_account_voucher) ? true : false,
                'note_with_account_voucher' => isset($request->note_with_account_voucher) ? true : false,
                'account_voucher_char' => isset($request->account_voucher_char) ? $request->account_voucher_char : null,
                'created_by' => backpack_user()->id,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Image Note Congiguration added successfully',
            ]);
        }catch(\Throwable $th){
            DB::rollback();
            dd($th);
        }
    }

    public function updateImageNoteConfigure(){
        $request = $this->crud->validateRequest();
        $id = $this->crud->getCurrentEntryId();
        $account_setting_id = DB::table('maintain_image_note_configure')->where('id', $id)->pluck('account_setting_id')->first();

        DB::beginTransaction();
        try{

            AccountSetting::whereId($account_setting_id)->update([
                'maintain_image_note' => $request->maintain_image_note,
                'sup_org_id' => $request->sup_org_id,
            ]);

            DB::table('maintain_image_note_configure')->where('id',$id)->update([
                'image_with_account_master' => isset($request->image_with_account_master) ? true : false,
                'note_with_account_master' => isset($request->note_with_account_master) ? true : false,
                'account_master_char' => isset($request->account_master_char) ? $request->account_master_char : null,
                'account_note_in_data_entry' => isset($request->account_note_in_data_entry) ? true : false,
                'image_with_account_voucher' => isset($request->image_with_account_voucher) ? true : false,
                'note_with_account_voucher' => isset($request->note_with_account_voucher) ? true : false,
                'account_voucher_char' => isset($request->account_voucher_char) ? $request->account_voucher_char : null,
                'updated_by' => backpack_user()->id,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Image Note Congiguration updated successfully',
            ]);
        }catch(\Throwable $th){
            DB::rollback();
            dd($th);
        }
    }
}
