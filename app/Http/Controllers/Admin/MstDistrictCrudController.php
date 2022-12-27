<?php

namespace App\Http\Controllers\Admin;

use App\Base\BaseCrudController;
use App\Http\Requests\MstDistrictRequest;
use App\Models\MstProvince;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MstDistrictCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MstDistrictCrudController extends BaseCrudController
{

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\MstDistrict::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/mst-district');
        CRUD::setEntityNameStrings('', 'Districts');
        // $this->setFilters();
        $this->isAllowed();

    }

    public function setFilters()
    {
        $this->crud->addFilter(
            [ // Name(en) filter`
                'label' => trans('Province'),
                'type' => 'select2',
                'name' => 'province_id', // the db column for the foreign key
            ],
            function () {
                return (new MstProvince())->getFilterComboOptions();
            },
            function ($value) { 
                $this->crud->addClause('where', 'province_id', $value);
            }
        );

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
            $this->addProvinceColumn(),
            $this->addNameEnColumn(),
            $this->addNameLcColumn(),
            
            $this->addIsActiveColumn(),

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
        CRUD::setValidation(MstDistrictRequest::class);

        $arr = [
            $this->addReadOnlyCodeField(),
            $this->addProvinceField(),
            $this->addPlainHtml(),
            $this->addNameEnField(),
            $this->addNameLcField(),
            $this->addIsActiveField()
        ];
        $this->crud->addFields($arr);


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
