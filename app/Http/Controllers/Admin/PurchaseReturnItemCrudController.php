<?php

namespace App\Http\Controllers\Admin;

use App\Base\BaseCrudController;
use App\Http\Requests\PurchaseReturnItemRequest;
use App\Models\GrnItem;
use App\Models\MstDiscMode;
use App\Models\MstItem;
use App\Models\ReturnReason;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PurchaseReturnItemCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PurchaseReturnItemCrudController extends BaseCrudController
{

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\PurchaseReturnItem::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/purchase-return-item');
        CRUD::setEntityNameStrings('purchase return item', 'purchase return item');
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
                'name' => 'mst_items_id',
                'type' => 'select2',
                'label' => 'Item Name',
                'entity'=>'itemEntity',
                'attribute'=>'name',
                'model'=>MstItem::class,
                
            ],
            $this->addSuperOrganizationColumn(),
            [
                'name' => 'purchase_qty',
                'type' => 'number',
                'label' => 'Purchase Quantity',
                
                'attributes' => [
                    'id' => 'purchase_quantity',
                    'onKeyup' => 'INVENTORY.fetchPurchaseitem()',

                ],
            ],
            [

                'name' => 'return_reason_id',
                'type' => 'select',
                'entity' => 'returnReasonEntity',
                'label' => 'Return Reason',
                'attribute' => 'name_en',
                'model' => ReturnReason::class,
                


            ],
            [
                'name' => 'free_qty',
                'type' => 'number',
                'label' => 'Free Quantity',
                
                'attributes' => [
                    'id' => 'free_quantity',
                    'onKeyup' => 'INVENTORY.fetchPurchaseitem()',

                ],
            ],
            // [
            //     [
            //         'name' => 'batch_qty_id',
            //         'type' => 'select2',
            //         'entity' => 'batchQuantityEntity',
            //         'label' => 'Item Name',
            //         'attribute' => 'batch_qty',
            //         'model' => GrnItem::class,
            //         'wrapperAttributes' => [
            //             'class' => 'col-md-4'
            //         ],
            //     ],

            // ],
           
            [
                'name' => 'return_qty',
                'type' => 'number',
                'label' => 'Return Quantity',
                
                'attributes' => [
                    'id' => 'return_quantity',
                    'onChange' => 'INVENTORY.fetchPurchaseitem()',
                    // 'readonly'    => 'readonly',
                ],
            ],
            [
                'name' => 'total_qty',
                'type' => 'number',
                'label' => 'Total Quantity',
                
                'attributes' => [
                    'id' => 'total_quantity',
                    'onChange' => 'INVENTORY.fetchPurchaseitem()',
                    'readonly'    => 'readonly',
                ],
            ],
            [

                'name' => 'batch_no_id',
                'type' => 'select',
                'entity' => 'batchNoQuantity',
                'label' => 'Batch Number',
                'attribute' => 'batch_no',
                'model' => GrnItem::class,
                


            ],
            [

                'name' => 'discount_mode_id',
                'type' => 'select',
                'entity' => 'discountEntity',
                'label' => 'Discount Mode',
                'attribute' => 'name_en',
                'model' => MstDiscMode::class,
                

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
                'name' => 'purchase_price',
                'type' => 'number',
                'label' => 'Purchase Price',
                
            ],
            [
                'name' => 'sales_price',
                'type' => 'number',
                'label' => 'Sales Price',
                
            ],
            [
                'name' => 'item_amount',
                'type' => 'number',
                'label' => 'Item Amount',
                
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
        CRUD::setValidation(PurchaseReturnItemRequest::class);

        $fields = [
            [
                'name' => 'mst_items_id',
                'type' => 'select2',
                'entity' => 'itemEntity',
                'label' => 'Item Name',
                'attribute' => 'name',
                'model' => MstItem::class,
                'wrapperAttributes' => [
                    'class' => 'col-md-4'
                ],
                
            ],
            $this->addSuperOrganizationField(),
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
            [

                'name' => 'return_reason_id',
                'type' => 'select2',
                'entity' => 'returnReasonEntity',
                'label' => 'Return Reason',
                'attribute' => 'name_en',
                'model' => ReturnReason::class,
                'wrapperAttributes' => [
                    'class' => 'col-md-4'
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
            // [
            //     [
            //         'name' => 'batch_qty_id',
            //         'type' => 'select2',
            //         'entity' => 'batchQuantityEntity',
            //         'label' => 'Item Name',
            //         'attribute' => 'batch_qty',
            //         'model' => GrnItem::class,
            //         'wrapperAttributes' => [
            //             'class' => 'col-md-4'
            //         ],
            //     ],

            // ],
           
            [
                'name' => 'return_qty',
                'type' => 'number',
                'label' => 'Return Quantity',
                'wrapperAttributes' => [
                    'class' => 'col-md-4'
                ],
                'attributes' => [
                    'id' => 'return_quantity',
                    'onChange' => 'INVENTORY.fetchPurchaseitem()',
                    // 'readonly'    => 'readonly',
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

                'name' => 'batch_no_id',
                'type' => 'select2',
                'entity' => 'batchNoQuantity',
                'label' => 'Batch Number',
                'attribute' => 'batch_no',
                'model' => GrnItem::class,
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
                'name' => 'purchase_price',
                'type' => 'number',
                'label' => 'Purchase Price',
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
            [
                'name' => 'item_amount',
                'type' => 'number',
                'label' => 'Item Amount',
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
