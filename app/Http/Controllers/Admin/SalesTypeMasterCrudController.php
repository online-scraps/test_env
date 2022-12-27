<?php

namespace App\Http\Controllers\Admin;

use App\Models\SalesTypeMaster;
use App\Base\BaseCrudController;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\SalesTypeMasterRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SalesTypeMasterCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SalesTypeMasterCrudController extends BaseCrudController
{
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(SalesTypeMaster::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/sales-type-master');
        CRUD::setEntityNameStrings('', 'sales type masters');

        if(backpack_user()->isStoreUser()){
            $this->crud->addClause('where','store_id', backpack_user()->store_id);
        }
        if(backpack_user()->isOrganizationUser()){
            $this->crud->addClause('where','sup_org_id', backpack_user()->sup_org_id);
        }
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
            $this->addRowNumberColumn(),
            $this->addSuperOrganizationColumn(),
            $this->addStoreColumn(),
            [
                'name'  => 'sales_type',
                'label' => 'Sale Type',
                'type'  => 'text'
            ],
            [
                'name'    => 'sales_type',
                'label'   => 'Sales Account Info',
                'type'    => 'select_from_array',
                'options' => $this->account_info(),
            ],
            $this->addTaxTypeColumn(),
            [
                'name'  => 'tax_invoice',
                'label' => 'Tax Invoice',
                'type'  => 'boolean',
                'options' => [0 => 'No', 1 => 'Yes']
            ],
            [
                'name'  => 'skip_vat',
                'label' => 'Skip Vat',
                'type'  => 'boolean',
                'options' => [0 => 'No', 1 => 'Yes']
            ],
            $this->addRegionColumn(),
            $this->addTaxCalculationColumn(),
        ];
        $this->crud->addColumns(array_filter($cols));
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    public function create()
    {
            $this->crud->hasAccessOrFail('create');
            // prepare the fields you need to show
            $this->data['account_info'] = $this->account_info();
            $this->data['tax_type'] = array_chunk($this->tax_type(), 4, true);
            $this->data['region'] = $this->region();
            $this->data['tax_calc'] = $this->tax_calc();
            $this->data['crud'] = $this->crud;
            $this->data['saveAction'] = $this->crud->getSaveAction();
            $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.add').' '.$this->crud->entity_name;
            return view('accounts.typeMaster.form', $this->data);
    }

    public function store()
    {

        $this->crud->hasAccessOrFail('create');
        $request = $this->crud->validateRequest();

        $rules = [
            'sales_type' => 'required',
            'account_info' => 'nullable',
            'tax_type' => 'nullable',
            'tax_invoice' => 'nullable',
            'skip_vat' => 'nullable',
            'region' => 'nullable',

            'issue_st_form' => 'nullable',
            'form_issubale' => 'nullable',
            'receive_st_form' => 'nullable',
            'form_receivable' => 'nullable',
            'tax_calc' => 'nullable',

            'tax' => 'nullable',
            'sucharge' => 'nullable',
            'cess' => 'nullable',
            'freeze_tax_sales' => 'nullable',
            'freeze_tax_sales_returns' => 'nullable',
            'inv_heading' => 'nullable',
            'inv_description' => 'nullable',
        ];

        $messages = [
            'required' => ':attribute is required.',
        ];

        $attributes = [
            'sales_type' => 'Sales Type',
            'account_info' => 'Sales Account Information',
            'tax_type' => 'Taxaction Type',
            'tax_invoice' => 'Tax Invoice',
            'skip_vat' => 'Skip in VAT Reports',
            'region' => 'Region',

            'issue_st_form' => 'Issue ST Form',
            'form_issubale' => 'Form Issuable',
            'receive_st_form' => 'Receive ST Form',
            'form_receivable' => 'Form Receivable',
            'tax_calc' => 'Tax Calculation',

            'tax' => 'Tax (In %)',
            'sucharge' => 'Surcharge (In %)',
            'cess' => 'Add. (Cess In %)',
            'freeze_tax_sales' => 'Freeze Tax In Sales',
            'freeze_tax_sales_returns' => 'Freeze Tax In Sales Return',
            'inv_heading' => 'Invoice Heading',
            'inv_description' => 'Invoice Description',
        ];

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->fails()) {
            if($validator->errors()){
                foreach($validator->errors()->all() as $error){
                    Alert::error($error)->flash();
                }
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }
        // Retrieve the validated input...
        $validated = $validator->validated();
        // Retrieve a portion of the validated input...
        $validated = $validator->safe()->except(['_save_action', '_token']);
        // dd('ok',$validated, request()->all());

        if (isset($request)) {
            $type = new SalesTypeMaster();
            $type->sales_type = $validated['sales_type'];
            $type->sales_ac_info = $validated['account_info'];
            $type->taxation_type = $validated['tax_type'];
            $type->tax_invoice = $validated['tax_invoice'];
            $type->skip_vat = $validated['skip_vat'];
            $type->region = $validated['region'];
            $type->issue_st_form = $validated['issue_st_form'];
            $type->form_issubale = $validated['form_issubale'];
            $type->receive_st_form = $validated['receive_st_form'];
            $type->form_receivable = $validated['form_receivable'];
            $type->tax_calculation = $validated['tax_calc'];
            $type->tax_percent = $validated['tax'];
            $type->surcharge_percent = $validated['sucharge'];
            $type->cess_percent = $validated['cess'];
            $type->freeze_tax_sales = $validated['freeze_tax_sales'];
            $type->freeze_tax_sales_returns = $validated['freeze_tax_sales_returns'];
            $type->inv_heading = $validated['inv_heading'];
            $type->inv_description = $validated['inv_description'];
            $type->sup_org_id = $request->sup_org_id;
            $type->store_id = $request->store_id;
            $type->save();
            Alert::success('sales type created successfully')->flash();
            return redirect()->route('sales-type-master.index');
        }
    }

    public function edit($id)
    {
        $this->crud->hasAccessOrFail('update');
        $this->data['typeMaster'] = SalesTypeMaster::find($id);
        // prepare the fields you need to show
        $this->data['account_info'] = $this->account_info();
        $this->data['tax_type'] = array_chunk($this->tax_type(), 4, true);
        $this->data['region'] = $this->region();
        $this->data['tax_calc'] = $this->tax_calc();
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.add').' '.$this->crud->entity_name;
        return view('accounts.typeMaster.form', $this->data);
    }

    public function update()
    {
        $this->crud->hasAccessOrFail('update');
        $request = $this->crud->validateRequest();
        $rules = [
            'sales_type' => 'required',
            'account_info' => 'nullable',
            'tax_type' => 'nullable',
            'tax_invoice' => 'nullable',
            'skip_vat' => 'nullable',
            'region' => 'nullable',

            'issue_st_form' => 'nullable',
            'form_issubale' => 'nullable',
            'receive_st_form' => 'nullable',
            'form_receivable' => 'nullable',
            'tax_calc' => 'nullable',

            'tax' => 'nullable',
            'sucharge' => 'nullable',
            'cess' => 'nullable',
            'freeze_tax_sales' => 'nullable',
            'freeze_tax_sales_returns' => 'nullable',
            'inv_heading' => 'nullable',
            'inv_description' => 'nullable',
        ];

        $messages = [
            'required' => ':attribute is required.',
        ];

        $attributes = [
            'sales_type' => 'Sales Type',
            'account_info' => 'Sales Account Information',
            'tax_type' => 'Taxaction Type',
            'tax_invoice' => 'Tax Invoice',
            'skip_vat' => 'Skip in VAT Reports',
            'region' => 'Region',

            'issue_st_form' => 'Issue ST Form',
            'form_issubale' => 'Form Issuable',
            'receive_st_form' => 'Receive ST Form',
            'form_receivable' => 'Form Receivable',
            'tax_calc' => 'Tax Calculation',

            'tax' => 'Tax (In %)',
            'sucharge' => 'Surcharge (In %)',
            'cess' => 'Add. (Cess In %)',
            'freeze_tax_sales' => 'Freeze Tax In Sales',
            'freeze_tax_sales_returns' => 'Freeze Tax In Sales Return',
            'inv_heading' => 'Invoice Heading',
            'inv_description' => 'Invoice Description',
        ];

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->fails()) {
            if($validator->errors()){
                foreach($validator->errors()->all() as $error){
                    Alert::error($error)->flash();
                }
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }
        // Retrieve the validated input...
        $validated = $validator->validated();
        // Retrieve a portion of the validated input...
        $validated = collect($validator->safe()->except(['_save_action', '_token']));

        // dd($validated);

        $type = SalesTypeMaster::find($this->crud->getCurrentEntryId());
        $type->sales_type = $validated['sales_type'];
        $type->sales_ac_info = $validated['account_info'];
        $type->taxation_type = $validated['tax_type'];
        $type->tax_invoice = $validated['tax_invoice'];
        $type->skip_vat = $validated['skip_vat'];
        $type->region = $validated['region'];
        $type->issue_st_form = $validated['issue_st_form'];
        $type->form_issubale = $validated['form_issubale'];
        $type->receive_st_form = $validated['receive_st_form'];
        $type->form_receivable = $validated['form_receivable'];
        $type->tax_calculation = $validated['tax_calc'];
        $type->tax_percent = $validated['tax'];
        $type->surcharge_percent = $validated['sucharge'];
        $type->cess_percent = $validated['cess'];
        $type->freeze_tax_sales = $validated['freeze_tax_sales'];
        $type->freeze_tax_sales_returns = $validated['freeze_tax_sales_returns'];
        $type->inv_heading = $validated['inv_heading'];
        $type->inv_description = $validated['inv_description'];
        $type->sup_org_id = $request->sup_org_id;
        $type->store_id = $request->store_id;
        $type->save();
        Alert::success('Sales Type Updated Successfully')->flash();
        return redirect()->route('sales-type-master.index');
    }
}
