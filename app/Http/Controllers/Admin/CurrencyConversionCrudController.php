<?php

namespace App\Http\Controllers\Admin;

use App\Models\Currencies;
use App\Base\BaseCrudController;
use App\Models\CurrencyConversion;
use App\Http\Requests\CurrencyConversionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CurrencyConversionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CurrencyConversionCrudController extends BaseCrudController
{


    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\CurrencyConversion::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/currency-conversion');
        CRUD::setEntityNameStrings('currency conversion', 'currency conversions');
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
        
        $columns = [
			$this->addRowNumberColumn(),
			
            [
				'name' => 'currency_type_id',
				'type' => 'select',
                'entity' => 'currency',
                'model' => Currencies::class,
                'attribute' => 'currency',
				'label' => 'Currency Type',
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'standard_rate',
				'type' => 'float',
				'label' => 'Standard Rate',
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'selling_rate',
				'type' => 'float',
				'label' => 'Selling Rate',
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'buying_rate',
				'type' => 'float',
				'label' => 'Buying Rate',
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			$this->addIsActiveColumn(),
		];

		$this->crud->addColumns(array_filter($columns));
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(CurrencyConversionRequest::class);
        $fields = [

			
			$this->addSuperOrgField(),
            $this->addStoreField(),
			[
				'name' => 'currency_type_id',
				'type' => 'select2',
                'entity' => 'currency',
                'model' => Currency::class,
                'attribute' => 'currency',

				'label' => 'Currency Type',
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'standard_rate',
				'type' => 'number',
				'label' => 'Standard Rate',
                'attributes' => ["step" => "0.01"],

				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'selling_rate',
				'type' => 'number',
				'label' => 'Selling Rate',
                'attributes' => ["step" => "0.01"],

				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'buying_rate',
				'type' => 'number',
				'label' => 'Buying Rate',
                'attributes' => ["step" => "0.01"],

				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			

			$this->addIsActiveField()

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
