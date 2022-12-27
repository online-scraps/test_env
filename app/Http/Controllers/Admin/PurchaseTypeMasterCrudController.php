<?php

namespace App\Http\Controllers\Admin;

use App\Base\BaseCrudController;
use App\Models\PurchaseTypeMaster;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\PurchaseTypeMasterRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PurchaseTypeMasterCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PurchaseTypeMasterCrudController extends BaseCrudController
{
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(PurchaseTypeMaster::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/purchase-type-master');
        CRUD::setEntityNameStrings('', 'purchase type masters');

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
                'name'  => 'purchase_type',
                'label' => 'Purchase Type',
                'type'  => 'text'
            ],
            [
                'name'    => 'purchase_ac_info',
                'label'   => 'Purchase Account Info',
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
            // [
            //     'name'  => 'capital_purchase',
            //     'label' => 'Capital Purchase',
            //     'type'  => 'boolean',
            //     'options' => [0 => 'No', 1 => 'Yes']
            // ],
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

        // dd($request->sup_org_id, $request->store_id);

        $rules = [
            'purchase_type' => 'required',
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
            'freeze_tax_purchase' => 'nullable',
            'freeze_tax_purchase_returns' => 'nullable',
            'inv_heading' => 'nullable',
            'inv_description' => 'nullable',
        ];

        $messages = [
            'required' => ':attribute is required.'
        ];

        $attributes = [
            'purchase_type' => 'Purchase Type',
            'account_info' => 'Purchase Account Information',
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
            'freeze_tax_purchase' => 'Freeze Tax In Purchase',
            'freeze_tax_purchase_returns' => 'Freeze Tax In Purchase Return',
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


        if (isset($request)) {
            $type = new PurchaseTypeMaster();
            $type->purchase_type = $validated['purchase_type'];
            $type->purchase_ac_info = $validated['account_info'];
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
            $type->freeze_tax_purchase = $validated['freeze_tax_purchase'];
            $type->freeze_tax_purchase_returns = $validated['freeze_tax_purchase_returns'];
            $type->inv_heading = $validated['inv_heading'];
            $type->inv_description = $validated['inv_description'];
            $type->sup_org_id = $request->sup_org_id;
            $type->store_id = $request->store_id;
            $type->save();
            Alert::success('Purchase Type Created Successfully')->flash();
            return redirect()->route('purchase-type-master.index');
        }
    }

    public function edit($id)
    {
        $this->crud->hasAccessOrFail('update');
        $this->data['typeMaster'] = PurchaseTypeMaster::find($id);
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
            'purchase_type' => 'required',
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
            'freeze_tax_purchase' => 'nullable',
            'freeze_tax_purchase_returns' => 'nullable',
            'inv_heading' => 'nullable',
            'inv_description' => 'nullable',
        ];

        $messages = [
            'required' => ':attribute is required.',
        ];

        $attributes = [
            'purchase_type' => 'Purchase Type Field',
            'account_info' => 'Purchase Account Information',
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
            'freeze_tax_purchase' => 'Freeze Tax In Purchase',
            'freeze_tax_purchase_returns' => 'Freeze Tax In Purchase Return',
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

        $type = PurchaseTypeMaster::find($this->crud->getCurrentEntryId());
        $type->purchase_type = $validated['purchase_type'];
        $type->purchase_ac_info = $validated['account_info'];
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
        $type->freeze_tax_purchase = $validated['freeze_tax_purchase'];
        $type->freeze_tax_purchase_returns = $validated['freeze_tax_purchase_returns'];
        $type->inv_heading = $validated['inv_heading'];
        $type->inv_description = $validated['inv_description'];
        $type->sup_org_id = $request->sup_org_id;
        $type->store_id = $request->store_id;
        $type->save();
        Alert::success('Purchase Type Updated Successfully')->flash();
        return redirect()->route('purchase-type-master.index');
    }
}
