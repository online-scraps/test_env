<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstFiscalYear;
use App\Base\BaseCrudController;
use App\Http\Requests\SeriesNumberRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AutoGenerateNumberCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SeriesNumberCrudController extends BaseCrudController
{
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\SeriesNumber::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/series-number');
        CRUD::setEntityNameStrings('', 'series numbers');
        $this->user = backpack_user();
        if($this->user->isStoreUser()){
            $this->crud->addClause('where','store_id', $this->user->store_id);
        }

        if($this->user->isOrganizationUser()){
            $this->crud->addClause('where','sup_org_id', $this->user->sup_org_id);
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
                // 1-n relationship
                'label'     => 'Terminal', // Table column heading
                'name'      => 'terminal_id', // the column that contains the ID of that connected entity;
                'type'    => 'select_from_array',
                'options' => $this->terminal_list(),
            ],
            [
                'name' => 'description',
                'type' => 'text',
                'label' => 'Description'
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
            [
                'name' => 'starting_no',
                'type' => 'number',
                'label' => 'Starting Number'
            ],
            [
                'name' => 'starting_word',
                'type' => 'text',
                'label' => 'Starting Word'
            ],
            [
                'name' => 'starting_no',
                'type' => 'number',
                'label' => 'Starting Number'
            ],
            [
                'name' => 'padding_length',
                'type' => 'number',
                'label' => 'Padding Length'
            ],
            [
                'name' => 'padding_no',
                'type' => 'number',
                'label' => 'Padding Char/No.'
            ],
            $this->addIsActiveColumn()
        ];
        $this->crud->addColumns(array_filter($cols));


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
        CRUD::setValidation(SeriesNumberRequest::class);

        $today_date_bs = convert_bs_from_ad();
        $current_fiscal_year = get_fiscal_year_from_date($today_date_bs);

        $fields = [
            $this->addSuperOrgField(),
            $this->addStoreField(),
            $this->addPlainHtml(),
            [
                'type' => 'custom_html',
                'name' => 'plain_html_2',
                'value' => '<legend class="px-2 bg-default">Series Number : </legend>',
            ],
            [   // Tax Calculation
                'name'        => 'terminal_id', // the name of the db column
                'label'       => 'Terminal', // the input label
                'type'        => 'select2_from_array',
                // optional - force the related options to be a custom query, instead of all();
                'options'   => $this->terminal_list(), //  you can use this to filter the results show in the select
                'wrapperAttributes'   => [
                    'class'      => 'form-group col-md-6'
                ]
            ],
            [
                'name' => 'description',
                'type' => 'text',
                'label' => 'Description',
                'wrapper'   => [
                    'class'      => 'form-group col-md-6'
                ],
            ],
            [
                'name' => 'starting_word',
                'type' => 'text',
                'label' => 'Starting Word',
                'wrapper'   => [
                    'class'      => 'form-group col-md-6'
                ],
            ],
            [
                'name' => 'starting_no',
                'type' => 'number',
                'label' => 'Starting Number',
                'wrapper'   => [
                    'class'      => 'form-group col-md-6'
                ],
            ],
            [
                'name' => 'ending_word',
                'type' => 'text',
                'label' => 'Ending Word',
                'attributes' => [
                    'class'       => 'form-control col-md-6',
                ],
                'wrapper'   => [
                    'class'      => 'form-group col-md-12'
                ],
            ],
            [
                'name' => 'padding_length',
                'type' => 'number',
                'label' => 'Padding Length',
                'wrapper'   => [
                    'class'      => 'form-group col-md-6'
                ],
            ],
            [
                'name' => 'padding_no',
                'type' => 'number',
                'label' => 'Padding Char/No.',
                'wrapper'   => [
                    'class'      => 'form-group col-md-6'
                ],
            ],
            [  // Select2
                'label'     => "Fiscal Year",
                'type'      => 'select2',
                'name'      => 'fiscal_year_id', // the db column for the foreign key
                // optional
                'entity'    => 'fiscalYearEntity', // the method that defines the relationship in your Model
                'model'     => MstFiscalYear::class, // foreign key model
                'attribute' => 'code', // foreign key attribute that is shown to user
                // 'value' => $current_fiscal_year,
                // 'default' => $current_fiscal_year,
                 // also optional
                'options'   => (function ($query) {
                    return $query->orderBy('id', 'ASC')->get();
                }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
                'wrapper'   => [
                    'class'      => 'form-group col-md-6'
                ],
            ],
            $this->addIsActiveField(),
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
