<?php

namespace App\Http\Controllers\Admin;

use App\Base\BaseCrudController;
use App\Http\Requests\MstPositionRequest;
use App\Base\Operations\InlineCreateOperation;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MstPositionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MstPositionCrudController extends BaseCrudController
{
    use InlineCreateOperation;
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\MstPosition::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/mst-position');
        CRUD::setEntityNameStrings('', 'Positions');
        $this->isAllowed();
        $this->user = backpack_user();
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
             //            $this->addCodeColumn(),
             $this->addNameEnColumn(),
             $this->addNameLcColumn(),
             $this->addSuperOrganizationColumn(),
             $this->addIsActiveColumn()
         ];
         $this->crud->addColumns(array_filter($cols));
         if(!$this->user->isSystemUser()){
            $this->crud->addButtonFromView('top', 'fetchMasterData', 'fetchMasterData', 'end');
        }

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
        CRUD::setValidation(MstPositionRequest::class);

        $fields =  [
            $this->addReadOnlyCodeField(),
            $this->addPlainHtml(),
            $this->addSuperOrgField(),
            $this->addStoreField(),
            $this->addNameEnField(),
            $this->addNameLcField(),
            $this->addIsActiveField(),
            $this->addIsSuperDataField(),
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
