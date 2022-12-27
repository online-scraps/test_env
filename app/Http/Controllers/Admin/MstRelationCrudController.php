<?php

namespace App\Http\Controllers\Admin;

use App\Base\BaseCrudController;
use App\Http\Requests\MstRelationRequest;
use App\Base\Operations\InlineCreateOperation;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MstRelationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MstRelationCrudController extends BaseCrudController
{
    use InlineCreateOperation;
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\MstRelation::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/mst-relation');
        CRUD::setEntityNameStrings('', ' relations');
        $this->isAllowed();
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        $cols =  [
            $this->addRowNumberColumn(),
            $this->addNameEnColumn(),
            $this->addNameLcColumn(),
            $this->addSuperOrganizationColumn(),
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
        CRUD::setValidation(MstRelationRequest::class);

        $fields =  [
            $this->addReadOnlyCodeField(),
            $this->addSuperOrgField(),
            $this->addStoreField(),
            $this->addNameEnField(),
            $this->addNameLcField(),
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
