<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstFiscalYear;
use App\Base\BaseCrudController;
use App\Http\Requests\SystemConfigurationRequest;
use App\Models\SystemConfiguration;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SystemConfigurationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SystemConfigurationCrudController extends BaseCrudController
{

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\SystemConfiguration::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/system-configuration');
        CRUD::setEntityNameStrings('', 'system configurations');

        $supId = backpack_user()->sup_org_id;
        $sysConfig = SystemConfiguration::where('sup_org_id', $supId)->first();
        if($sysConfig){
            $this->crud->denyAccess('create');
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
            [
                'name' => 'date_ad',
                'type' => 'date',
                'label' => 'Date (AD)',
            ],
            [
                'name' => 'date_bs',
                'type' => 'date',
                'label' => 'Date (BS)',
            ],
            [
                // 1-n relationship
                'label'     => 'Fiscal Year', // Table column heading
                'type'      => 'select',
                'name'      => 'fiscal_year_id', // the column that contains the ID of that connected entity;
                'entity'    => 'fiscalYearEntity', // the method that defines the relationship in your Model
                'attribute' => 'code', // foreign key attribute that is shown to user
                'model'     => MstFiscalYear::class, // foreign key model
            ],
        ];
        $this->crud->addColumns($cols);
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(SystemConfigurationRequest::class);
        $ad_now = now()->toDateString();
        $bs_now = convert_bs_from_ad($ad_now);
        $fields = [
            $this->addSuperOrgField(),
            [
                'label' => 'Date in AD',
                'name' => 'date_ad',
                'type' => 'date',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4',
                ],
                'default' => $ad_now,
                'attributes' => [
                    'id' => 'date_ad',
                    'relatedId' => 'date_bs',
                    'class' => 'form-control date-en-field'
                ],
                'tab'             => 'General',
            ],
            [
                'label' => 'Date in BS',
                'name' => 'date_bs',
                'type' => 'nepali_date',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4',
                ],
                'default' => $bs_now,
                'attributes' => [
                    'id' => 'date_bs',
                    'maxlength' => '10',
                    'relatedId' => 'date_ad',
                    'placeholder' => 'yyyy-mm-dd',
                    'class' => 'form-control date-field'
                ],
                'tab'             => 'General',
            ],
            [  // Select2
                'label'     => "Fiscal Year",
                'type'      => 'select2',
                'name'      => 'fiscal_year_id', // the db column for the foreign key
                // optional
                'entity'    => 'fiscalYearEntity', // the method that defines the relationship in your Model
                'model'     => MstFiscalYear::class, // foreign key model
                'attribute' => 'code', // foreign key attribute that is shown to user
                 // also optional
                'options'   => (function ($query) {
                    return $query->orderBy('id', 'ASC')->get();
                }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
                'wrapperAttributes'   => [
                    'class'      => 'form-group col-md-4'
                ],
                'tab'             => 'General',
            ],
            [
                'name'        => 'currency_type_id',
                'label'       => "Local Currency",
                'type'        => 'select2_from_array',
                'options'     => [
                    1 => 'NPR',
                    2 => 'INR',
                    3 => 'USD'
                ],
                'allows_null' => false,
                'default'     => 1,
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-3'
                ],
                'tab'             => 'General',
            ],
            [
                'name' => 'amount',
                'type' => 'number',
                'label' => 'Amount',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-3'
                ],
                'tab'             => 'General',
            ],
            [
                'name' => 'quantity',
                'type' => 'number',
                'label' => 'Quantity',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-3'
                ],
                'tab'             => 'General',
            ],
            [
                'name' => 'currency',
                'type' => 'number',
                'label' => 'Currency',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-3'
                ],
                'tab'             => 'General',
            ],
            // [
            //     'name' => 'email',
            //     'type' => 'email',
            //     'label' => 'Email',
            //     'wrapperAttributes'   => [
            //         'class'      => 'form-group col-md-6'
            //     ],
            //     'value' => backpack_user()->email,
            //     'tab'             => 'General',
            // ],
            // [
            //     'name' => 'password',
            //     'type' => 'password',
            //     'label' => 'Password',
            //     'wrapperAttributes'   => [
            //         'class'      => 'form-group col-md-6'
            //     ],
            //     'tab'             => 'General',
            // ],
            [
                'type' => 'custom_html',
                'name' => 'plain_html_2',
                'value' => '<legend class="px-2 bg-default">Voucher Signature : </legend>',
                'tab'             => 'General',
            ],
            [  // Select2
                'label'     => "Checked By",
                'type'      => 'select2',
                'name'      => 'checked_by', // the db column for the foreign key
                // optional
                'entity'    => 'checkedByEntity', // the method that defines the relationship in your Model
                'model'     => User::class, // foreign key model
                'attribute' => 'name', // foreign key attribute that is shown to user
                 // also optional
                'options'   => (function ($query) {
                    return $query->orderBy('id', 'ASC')->get();
                }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
                'wrapperAttributes'   => [
                    'class'      => 'form-group col-lg-6'
                ],
                'tab'             => 'General',
            ],
            [
                'name' => 'checked_by_position',
                'type' => 'text',
                'label' => 'Position',
                'wrapperAttributes'   => [
                    'class'      => 'form-group col-lg-6'
                ],
                'tab'             => 'General',
            ],
            [  // Select2
                'label'     => "Approved By",
                'type'      => 'select2',
                'name'      => 'approved_by', // the db column for the foreign key
                // optional
                'entity'    => 'approvedByEntity', // the method that defines the relationship in your Model
                'model'     => User::class, // foreign key model
                'attribute' => 'name', // foreign key attribute that is shown to user
                 // also optional
                'options'   => (function ($query) {
                    return $query->orderBy('id', 'ASC')->get();
                }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
                'wrapperAttributes'   => [
                    'class'      => 'form-group col-lg-6'
                ],
                'tab'             => 'General',
            ],
            [
                'name' => 'approved_by_position',
                'type' => 'text',
                'label' => 'Position',
                'wrapperAttributes'   => [
                    'class'      => 'form-group col-lg-6'
                ],
                'tab'             => 'General',
            ],
            [  // Select2
                'label'     => "Prepared By",
                'type'      => 'select2',
                'name'      => 'prepared_by', // the db column for the foreign key
                // optional
                'entity'    => 'preparedByEntity', // the method that defines the relationship in your Model
                'model'     => User::class, // foreign key model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'value' => backpack_user()->id,
                 // also optional
                'options'   => (function ($query) {
                    return $query->orderBy('id', 'ASC')->get();
                }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
                'wrapperAttributes'   => [
                    'class'      => 'form-group col-lg-6'
                ],
                'tab'             => 'General',
            ],
            [
                'name' => 'prepared_by_position',
                'type' => 'text',
                'label' => 'Position',
                'wrapperAttributes'   => [
                    'class'      => 'form-group col-lg-6'
                ],
                'tab'             => 'General',
            ],
            [
                'name'        => 'budget_type_id',
                'label'       => "Surplus/Deficit Fund",
                'type'        => 'select2_from_array',
                'options'     => [
                    1 => 'Fund Balance',
                    2 => 'Reserve Policies',
                ],
                'allows_null' => false,
                'default'     => 1,
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6'
                ],
                'tab'             => 'Account',
            ],
            [
                'name'        => 'cash_type_id',
                'label'       => "Cash Book",
                'type'        => 'select2_from_array',
                'options'     => [
                    1 => 'Cash In Hand',
                ],
                'allows_null' => false,
                'default'     => 1,
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6'
                ],
                'tab'             => 'Account',
            ],
            [   // Checkbox
                'name'  => 'code_wise_transcation',
                'label' => 'Code Wise Transaction',
                'type'  => 'checkbox',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6'
                ],
                'tab'             => 'Account',
            ],
            [   // Checkbox
                'name'  => 'transcation_negative',
                'label' => 'Transaction In Negative',
                'type'  => 'checkbox',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6'
                ],
                'tab'             => 'Account',
            ],
            [
                'type' => 'custom_html',
                'name' => 'plain_html_3',
                'value' => '<legend class="px-2 bg-default">Enable Entry Control Options : </legend>',
                'tab'             => 'Account',
            ],
            [
                'name' => 'entry_controls_mandatory',
                'label' => 'Enable Mandatory Entry Control Options',
                'type' => 'custom_checklist_from_array',
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                ],
                'options' =>
                [
                    'Voucher Date' => 'Voucher Date',
                    'Currency' => 'Currency',
                    'Sub Ledger' => 'Sub Ledger',
                    'Narration' => 'Narration',
                    'Remarks' => 'Remarks',
                ],
                'number_of_columns' => 5,
                'tab'             => 'Account',
            ],
            [
                'type' => 'custom_html',
                'name' => 'plain_html_4',
                'value' => '<legend class="px-2 bg-default">Enable Mandatory Entry Control Options : </legend>',
                'tab'             => 'Account',
            ],
            [
                'name' => 'entry_controls_options',
                'label' => 'Enable Entry Control Options',
                'type' => 'custom_checklist_from_array',
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                ],
                'options' =>
                [
                    'Currency' => 'Currency',
                    'Sub Ledger' => 'Sub Ledger',
                    'Narration' => 'Narration',
                    'Remarks' => 'Remarks',
                ],
                'number_of_columns' => 4,
                'tab'             => 'Account',
            ],
            [
                'type' => 'custom_html',
                'name' => 'plain_html_5',
                'value' => '<legend class="px-2 bg-default">Font Setting For Printing : </legend>',
                'tab'             => 'Account',
            ],
            [
                'name'        => 'font_name',
                'label'       => "Font Name",
                'type'        => 'select2_from_array',
                'options'     => [
                    'Arial' => 'Arial',
                    'Verdana' => 'Verdana',
                    'Tahoma' => 'Tahoma',
                    'Trebuchet MS' => 'Trebuchet MS',
                    'Gill Sans' => 'Gill Sans',
                ],
                'allows_null' => false,
                'default'     => 1,
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6'
                ],
                'tab'             => 'Account',
            ],
            [
                'name'        => 'font_size',
                'label'       => "Font Size",
                'type'        => 'select2_from_array',
                'options'     => [
                    9 => '9',
                    12 => '12',
                    14 => '14',
                    16 => '16',
                ],
                'allows_null' => false,
                'default'     => 1,
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6'
                ],
                'tab'             => 'Account',
            ],
            [
                'name'        => 'paper_size',
                'label'       => "Paper Size",
                'type'        => 'select2_from_array',
                'options'     => [
                    1 => 'A4',
                    2 => 'A3',
                    3 => 'A5',
                    4 => 'B4',
                    5 => 'B5',
                ],
                'allows_null' => false,
                'default'     => 1,
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6'
                ],
                'tab'             => 'Account',
            ],
            [
                'name'  => 'printing_date',
                'label' => 'Printing Date Time',
                'type'  => 'checkbox',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6'
                ],
                'tab'             => 'Account',
            ],
        ];
        $this->crud->addFields($fields);

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
