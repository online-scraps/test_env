<?php

namespace App\Http\Controllers\Admin;

use App\Base\BaseCrudController;
use App\Http\Requests\PurchaseOrderTypeRequest;
use App\Models\PurchaseOrderType;
use App\Utils\PdfPrint;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PurchaseOrderTypeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PurchaseOrderTypeCrudController extends BaseCrudController
{


    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\PurchaseOrderType::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/purchase-order-type');
        CRUD::setEntityNameStrings('', 'PO type');
        // $this->crud->enableExportButtons();

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

        $cols = array_filter($cols);
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
        CRUD::setValidation(PurchaseOrderTypeRequest::class);

        $fields=[
            $this->addReadOnlyCodeField(),
            $this->addPlainHtml(),
            // $this->addSuperOrganizationField(),
            $this->addStoreField(),
            $this->addNameEnField(),
            $this->addNameLcField(),
            $this->addDescriptionField(),
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

    public function listPdfDownload()
    {
        $potypes = PurchaseOrderType::all();
        $view = 'pdfPages.listOperations.poType';
        $html = view($view, compact('potypes'))->render();
        $file_name = 'Purchase Order Type.pdf';
        $res = PdfPrint::printPortrait($html, $file_name);
        return $res;
    }
}
