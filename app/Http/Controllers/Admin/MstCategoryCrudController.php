<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Base\BaseCrudController;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\MstCategoryRequest;
use Illuminate\Support\Facades\Validator;
use App\Imports\CategoryEntriesExcelImport;
use App\Base\Operations\InlineCreateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MstCategoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MstCategoryCrudController extends BaseCrudController
{
    use InlineCreateOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\MstCategory::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/mst-category');
        CRUD::setEntityNameStrings('', 'Categories');
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
        $cols = [
            $this->addRowNumberColumn(),
            //            $this->addCodeColumn(),
            $this->addNameEnColumn(),
            $this->addSuperOrganizationColumn(),
            $this->addIsActiveColumn(),
        ];
        $this->crud->addColumns(array_filter($cols));
        $this->filterListByUserLevel(false);
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
        CRUD::setValidation(MstCategoryRequest::class);

        $fields = [
            $this->addReadOnlyCodeField(),
            $this->addPlainHtml(),
            $this->addSuperOrgField(),
            $this->addStoreField(),
            $this->addNameEnField(),
            $this->addNameLcField(),
            $this->addDescriptionField(),
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
