<?php

namespace App\Http\Controllers\Admin;

use App\Models\BillSundry;
use App\Base\BaseCrudController;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Support\Facades\Request;
use App\Http\Requests\BillSundryRequest;
use Illuminate\Support\Facades\Validator;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class BillSundryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BillSundryCrudController extends BaseCrudController
{
    public function setup()
    {
        CRUD::setModel(BillSundry::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/bill-sundry');
        CRUD::setEntityNameStrings('Bill Sundry', 'Bill Sundry');

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
                'name'  => 'name',
                'label' => 'Name',
                'type'  => 'text'
            ],
            [
                'name'  => 'alias',
                'label' => 'Alias',
                'type'  => 'text'
            ],
            [
                'name'  => 'print_name',
                'label' => 'Print Name',
                'type'  => 'text',
            ],
            [
                'name'  => 'sundry_type',
                'label' => 'Sundry Type',
                'type'  => 'select_from_array',
                'options' => [0 => '-', 1 => 'Additive', 2 => 'Subtractive']
            ],
            // $this->addRegionColumn(),
            // $this->addTaxCalculationColumn(),
        ];
        $this->crud->addColumns(array_filter($cols));

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    public function create(){
        $this->crud->hasAccessOrFail('create');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.add').' '.$this->crud->entity_name;
        $this->data['accounting_material'] = $this->accounting_material();
        $this->data['amount_bill_sundry_fed'] = $this->amount_bill_sundry_fed();
        $this->data['bill_sundry_percentage_of'] = $this->bill_sundry_percentage_of();
        $this->data['bill_sundry_calculated_on'] = $this->bill_sundry_calculated_on();
        return view('accounts.bill_sundry.form', $this->data);
    }

    public function store()
    {


        $this->crud->hasAccessOrFail('create');
        $request = $this->crud->validateRequest();

        $rules = [
            'name' => 'required',
        ];

        $messages = [
            'required' => ':attribute is required.',
        ];

        $attributes = [
            'name' => 'Name',
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

        $sundry = new BillSundry();
        $sundry->name = $request->name;
        $sundry->alias = $request->alias;
        $sundry->print_name = $request->print_name;
        $sundry->sundry_type = $request->bill_sundry_type;
        $sundry->sundry_nature = $request->bill_sundry_nature;
        $sundry->default_value = $request->default_value;
        $sundry->sub_total_heading = $request->sub_total_heading;
        $sundry->account_sale = $request->accounting_in_sale;
        $sundry->account_purchase = $request->accounting_in_purchase;
        $sundry->affects_good_sales = $request->affects_the_cost_of_goods_in_sale;
        $sundry->affects_good_purchase = $request->affects_the_cost_of_goods_in_purchase;
        $sundry->affects_good_material_issue = $request->affects_the_cost_of_goods_in_material_issue;
        $sundry->affects_good_material_receipt = $request->affects_the_cost_of_goods_in_material_receipt;
        $sundry->affects_good_stock_transfer = $request->affects_the_cost_of_goods_in_stock_transfer;
        $sundry->affects_accounting_sale = $request->sales_affects_acconting;
        $sundry->adjust_amount_sale = $request->sales_adjust_in_purchase_amount;
        $sundry->account_head_sale = $request->sales_purhcase_amount_account_htp;
        $sundry->adjust_party_amount_sale = $request->sales_adjust_in_party_account;
        $sundry->account_head_party_sale = $request->sales_party_account_htp;
        $sundry->post_over_sale = $request->sales_post_over_and_above;
        $sundry->impact_zero_tax_sale = $request->sales_impact_zero_tax_items;
        $sundry->affects_accounting_purchase = $request->purchase_affects_accounting;
        $sundry->adjust_amount_purchase = $request->purchase_adjust_in_purchase_amount;
        $sundry->account_head_purchase = $request->purchase_amount_account_htp;
        $sundry->adjust_party_amount_purchase = $request->purchase_adjust_in_party_account;
        $sundry->account_head_party_purchase = $request->purchase_party_account_htp;
        $sundry->post_over_purchase = $request->purchase_post_over_and_above;
        $sundry->impact_zero_tax_purchase = $request->purchase_impact_zero_tax_items;
        $sundry->accounting_material = $request->accounting_in;
        $sundry->bill_sundry_fed = $request->amount_of_bill_sundry_fed;
        $sundry->percentage_of = $request->fed_as_of;
        $sundry->selective_calc = $request->selective_calculation;
        $sundry->no_bill_sundry = $request->no_of_bill_sundry;
        $sundry->cal_type = $request->bill_sundry_to_be_calculated_on;
        $sundry->consolidate_amount = $request->consolidate_bill_sundries_amount;



        $sundry->round_off = $request->round_off_bill_sundry_amount;
        $sundry->round_off_nearest = $request->rounding_off_nearest_to;

        $sundry->sup_org_id = $request->sup_org_id;
        $sundry->store_id = $request->store_id;
        $sundry->save();

        Alert::success('Bill Sundry Created successfully')->flash();
        return redirect()->route('bill-sundry.index');
    }

    public function edit($id)
    {
        $this->crud->hasAccessOrFail('update');
        $this->data['billSundry'] = BillSundry::find($id);
        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.add').' '.$this->crud->entity_name;
        $this->data['accounting_material'] = $this->accounting_material();
        $this->data['amount_bill_sundry_fed'] = $this->amount_bill_sundry_fed();
        $this->data['bill_sundry_percentage_of'] = $this->bill_sundry_percentage_of();
        $this->data['bill_sundry_calculated_on'] = $this->bill_sundry_calculated_on();
        return view('accounts.bill_sundry.form', $this->data);
    }

    public function update()
    {
        $request = $this->crud->validateRequest();

        $this->crud->hasAccessOrFail('update');

        $sundry = BillSundry::find($this->crud->getCurrentEntryId());
        $sundry->name = $request->name;
        $sundry->alias = $request->alias;
        $sundry->print_name = $request->print_name;
        $sundry->sundry_type = $request->bill_sundry_type;
        $sundry->sundry_nature = $request->bill_sundry_nature;
        $sundry->default_value = $request->default_value;
        $sundry->sub_total_heading = $request->sub_total_heading;
        $sundry->account_sale = $request->accounting_in_sale;
        $sundry->account_purchase = $request->accounting_in_purchase;
        $sundry->affects_good_sales = $request->affects_the_cost_of_goods_in_sale;
        $sundry->affects_good_purchase = $request->affects_the_cost_of_goods_in_purchase;
        $sundry->affects_good_material_issue = $request->affects_the_cost_of_goods_in_material_issue;
        $sundry->affects_good_material_receipt = $request->affects_the_cost_of_goods_in_material_receipt;
        $sundry->affects_good_stock_transfer = $request->affects_the_cost_of_goods_in_stock_transfer;
        $sundry->affects_accounting_sale = $request->sales_affects_acconting;
        $sundry->adjust_amount_sale = $request->sales_adjust_in_purchase_amount;
        $sundry->account_head_sale = $request->sales_purhcase_amount_account_htp;
        $sundry->adjust_party_amount_sale = $request->sales_adjust_in_party_account;
        $sundry->account_head_party_sale = $request->sales_party_account_htp;
        $sundry->post_over_sale = $request->sales_post_over_and_above;
        $sundry->impact_zero_tax_sale = $request->sales_impact_zero_tax_items;
        $sundry->affects_accounting_purchase = $request->purchase_affects_accounting;
        $sundry->adjust_amount_purchase = $request->purchase_adjust_in_purchase_amount;
        $sundry->account_head_purchase = $request->purchase_amount_account_htp;
        $sundry->adjust_party_amount_purchase = $request->purchase_adjust_in_party_account;
        $sundry->account_head_party_purchase = $request->purchase_party_account_htp;
        $sundry->post_over_purchase = $request->purchase_post_over_and_above;
        $sundry->impact_zero_tax_purchase = $request->purchase_impact_zero_tax_items;
        $sundry->accounting_material = $request->accounting_in;
        $sundry->bill_sundry_fed = $request->amount_of_bill_sundry_fed;
        $sundry->percentage_of = $request->fed_as_of;
        $sundry->selective_calc = $request->selective_calculation;
        $sundry->no_bill_sundry = $request->no_of_bill_sundry;
        $sundry->cal_type = $request->bill_sundry_to_be_calculated_on;
        $sundry->consolidate_amount = $request->consolidate_bill_sundries_amount;

        $sundry->round_off = $request->round_off_bill_sundry_amount;
        $sundry->round_off_nearest = $request->rounding_off_nearest_to;

        $sundry->sup_org_id = $request->sup_org_id;
        $sundry->store_id = $request->store_id;
        $sundry->save();

        Alert::success('Bill Sundry Updated successfully')->flash();
        return redirect()->route('bill-sundry.index');
    }
}
