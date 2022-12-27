<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstFiscalYear;
use App\Base\BaseCrudController;
use App\Http\Requests\MstFiscalYearRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MstFiscalYearCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MstFiscalYearCrudController extends BaseCrudController
{
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(MstFiscalYear::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/mst-fiscal-year');
        CRUD::setEntityNameStrings('', 'mst fiscal years');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(MstFiscalYearRequest::class);
        $arr = [
            $this->addCodeField(),
               [
                'type' => 'custom_html',
                'name'=>'plain_html_1',
                'value' => '<div class="form-group col-md-12"></div>',
            ],
            [
                'name' => 'from_date_bs',
                'type' => 'nepali_date',
                'label' => 'From Date (BS)',
                 'attributes'=>
                  [
                    'id'=>'from_date_bs',
                    'relatedId'=>'from_date_ad',
                    'maxlength' =>'10',
                 ],
                 'wrapperAttributes' => [
                     'class' => 'form-group col-md-3',
                 ],
            ],

            [
                'name' => 'from_date_ad',
                'type' => 'date',
                'label' => 'From Date (AD)',
                'attributes'=>
                [
                'id'=>'from_date_ad',
                ],
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-3',
                ],
            ],
            [
                'name' => 'to_date_bs',
                'type' => 'nepali_date',
                'label' => 'To Date (BS)',
                'attributes'=>
                [
                    'id'=>'to_date_bs',
                    'relatedId'=>'to_date_ad',
                    'maxlength' =>'10',
                ],
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-3',
                ],
            ],
            [
                'name' => 'to_date_ad',
                'type' => 'date',
                'label' => 'To Date (Ad)',
                'attributes'=>[
                    'id'=>'to_date_ad'
                ],
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-3',
                ],
            ],
            $this->addDisplayOrderField(),

                $this->addRemarksField(),
        ];

        $this->crud->addFields($arr);


        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    protected function setupListOperation()
    {
        $col = [
            $this->addRowNumberColumn(),
            [
                'name' => 'code',
                'type' => 'text',
                'label' => 'Code',
            ],
            [
                // 'name'=>'from_date_bs',
                // 'type' => 'modal_function',
                // 'label'=> 'From (BS)',
                'name'  => 'from_date_bs',
                'label' => 'From Date (BS)', // Table column heading
                'type'  => 'model_function',
                'function_name' => 'getFromDateBs',
            ],
            [
                // 'name'=>'to_date_bs',
                // 'label'=> 'To (BS)',
                'name'  => 'to_date_bs',
                'label' => 'To Date (BS)', // Table column heading
                'type'  => 'model_function',
                'function_name' => 'getToDateBs',
            ],
            $this->addDisplayOrderColumn()

        ];
            $this->crud->addColumns($col);
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
