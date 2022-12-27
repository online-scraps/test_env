<?php

namespace App\Http\Controllers\Admin;

use App\Base\BaseCrudController;
use App\Http\Requests\GrnItemRequest;
use App\Models\MstDiscMode;
use App\Models\MstItem;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class GrnItemCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class GrnItemCrudController extends BaseCrudController
{

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\GrnItem::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/grn-item');
        CRUD::setEntityNameStrings('grn item', 'grn items');
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
            [
                'name' => 'purchase_qty',
                'type' => 'number',
                'label' => 'Purchase Quantity',


            ],
            [
                'name' => 'received_qty',
                'type' => 'number',
                'label' => 'Received Quantity',

            ],
            $this->addSuperOrganizationColumn(),

            [
                'name' => 'free_qty',
                'type' => 'number',
                'label' => 'Free Quantity',
            ],
            [
                'name' => 'invoice_qty',
                'type' => 'number',
                'label' => 'Invoice Quantity',
            ],
            [
                'name' => 'purchase_price',
                'type' => 'number',
                'label' => 'Purchase Price'

            ],
            [

                'name' => 'discount_mode_id',
                'type' => 'select2',
                'entity' => 'discountEntity',
                'label' => 'Discount Mode',
                'attribute' => 'name_en',
                'model' => MstDiscMode::class,


            ],
            [
                'name' => 'total_qty',
                'type' => 'number',
                'label' => 'Total Quantity',
            ],
            [
                'name' => 'batch_no',
                'type' => 'number',

            ],
            [
                'name'=>'expiry_date',
                'type'=>'nepali_date',
                'label'=>'Expire Date',
                'attributes'=>[
                    'id'=>'expiry_date',
                ],

            ],

            [
                'name' => 'discount',
                'type' => 'number',

            ],
            [
                'name' => 'tax_vat',
                'type' => 'number',
                'label' => 'Tax Vat',

            ],
            [
                'name' => 'mst_items_id',
                'type' => 'select',
                'label'=>'Item Name',
                'entity'=>'itemEntity',
                'attributes'=>'name',
                'model'=> MstItem::class
            ],
            [
                'name' => 'item_amount',
                'type' => 'number',
                'label' => 'Item Amount',

            ],
            [
                'name' => 'sales_price',
                'type' => 'number',
                'label' => 'Sales Price',

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
        CRUD::setValidation(GrnItemRequest::class);

        $fields = [
            [
                'name' => 'purchase_qty',
                'type' => 'number',
                'label' => 'Purchase Quantity',
                'wrapperAttributes' => [
                    'class' => 'col-md-4'
                ],
                'attributes' => [
                    'id' => 'purchase_quantity',
                    'onKeyup' => 'INVENTORY.fetchPurchaseitem()',

                ],

            ],
            $this->addSuperOrganizationField(),
            $this->addStoreField(),

            [
                'name' => 'received_qty',
                'type' => 'number',
                'label' => 'Received Quantity',
                'wrapperAttributes' => [
                    'class' => 'col-md-4'
                ],
                'attributes' => [
                    'id' => 'receive_quantity',
                    'onKeyup' => 'INVENTORY.fetchPurchaseitem()',

                ],
            ],

            [
                'name' => 'free_qty',
                'type' => 'number',
                'label' => 'Free Quantity',
                'wrapperAttributes' => [
                    'class' => 'col-md-4'
                ],
                'attributes' => [
                    'id' => 'free_quantity',
                    'onKeyup' => 'INVENTORY.fetchPurchaseitem()',

                ],
            ],
            [
                'name' => 'invoice_qty',
                'type' => 'number',
                'label' => 'Invoice Quantity',
                'wrapperAttributes' => [
                    'class' => 'col-md-4'
                ],
                'attributes' => [
                    'id' => 'invoice_quantity',
                    'onKeyup' => 'INVENTORY.fetchPurchaseitem()',

                ],
            ],
            [
                'name' => 'purchase_price',
                'type' => 'number',
                'label' => 'Purchase Price',
                'wrapperAttributes' => [
                    'class' => 'col-md-4'
                ],
            ],
            [

                'name' => 'discount_mode_id',
                'type' => 'select2',
                'entity' => 'discountEntity',
                'label' => 'Discount Mode',
                'attribute' => 'name_en',
                'model' => MstDiscMode::class,
                'wrapperAttributes' => [
                    'class' => 'col-md-4'
                ],

            ],
            [
                'name' => 'total_qty',
                'type' => 'number',
                'label' => 'Total Quantity',
                'wrapperAttributes' => [
                    'class' => 'col-md-4'
                ],
                'attributes' => [
                    'id' => 'total_quantity',
                    'onChange' => 'INVENTORY.fetchPurchaseitem()',
                    'readonly'    => 'readonly',
                ],
            ],
            [
                'name' => 'batch_no',
                'type' => 'number',
                'wrapperAttributes' => [
                    'class' => 'col-md-4'
                ],
            ],
            [
                'name'=>'expiry_date',
                'type'=>'nepali_date',
                'label'=>'Expire Date',
                'wrapperAttributes' => [
                    'class' => 'col-md-4'
                ],

            ],

            [
                'name' => 'discount',
                'type' => 'number',
                'wrapperAttributes' => [
                    'class' => 'col-md-4'
                ],
            ],
            [
                'name' => 'tax_vat',
                'type' => 'number',
                'label' => 'Tax Vat',
                'wrapperAttributes' => [
                    'class' => 'col-md-4'
                ],
            ],
            [
                'name' => 'mst_items_id',
                'type' => 'select2',
                'label' => 'Item Name',
                'entity'=>'itemEntity',
                'attribute'=>'name',
                'model'=>MstItem::class,
                'wrapperAttributes' => [
                    'class' => 'col-md-4'
                ],
            ],
            [
                'name' => 'item_amount',
                'type' => 'number',
                'label' => 'Item Amount',
                'wrapperAttributes' => [
                    'class' => 'col-md-4'
                ],
            ],
            [
                'name' => 'sales_price',
                'type' => 'number',
                'label' => 'Sales Price',
                'wrapperAttributes' => [
                    'class' => 'col-md-4'
                ],
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
