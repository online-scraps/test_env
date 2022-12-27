<?php

namespace App\Http\Controllers\Admin;

use App\Models\GrnTypes;
use App\Base\BaseCrudController;
use App\Http\Requests\GrnTypesRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class GrnTypesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class GrnTypesCrudController extends BaseCrudController
{

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\GrnTypes::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/grn-types');
        CRUD::setEntityNameStrings('GRN Types', 'GRN Types');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $cols=[
            $this->addRowNumberColumn(),
            //            $this->addCodeColumn(),
            $this->addNameEnColumn(),
            $this->addNameLcColumn(),
            $this->addDescriptionColumn(),
            $this->addIsActiveColumn(),
            $this->addSuperOrganizationColumn()
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
        CRUD::setValidation(GrnTypesRequest::class);

        $fields=[
            $this->addReadOnlyCodeField(),
            $this->addPlainHtml(),
            $this->addNameEnField(),
            $this->addNameLcField(),
            $this->addDescriptionField(),
            $this->addIsActiveField(),

        ];
        $this->crud->addFields(array_filter($fields));

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
