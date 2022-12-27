<?php

namespace App\Http\Controllers\Admin;

use App\Models\Currencies;
use App\Base\BaseCrudController;
use App\Http\Requests\CurrenciesRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CurrenciesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CurrenciesCrudController extends BaseCrudController
{


    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Currencies::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/currencies');
        CRUD::setEntityNameStrings('currencies', 'currencies');
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
			
            // $this->addSuperOrganizationColumn(),
            [
				'name' => 'symbol',
				'type' => 'text',
				'label' => 'Currency Symbol',
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'currency',
				'type' => 'text',
				'label' => 'Currency Name',
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'sub_currency',
				'type' => 'text',
				'label' => 'Sub Currency',
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'no_format',
				'type' => 'text',
				// 'type' => 'select_from_array',
				'label' => 'Number Format',
                // 'model_function' => ;
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
            [
				'name' => 'decimal_places',
				'type' => 'number',
				'label' => 'Decimal Places',
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			$this->addIsActiveColumn(),
		];

		$this->crud->addColumns(array_filter($columns));

        // CRUD::column('code');
        // CRUD::column('symbol');
        // CRUD::column('currency');
        // CRUD::column('sub_currency');
        // CRUD::column('no_format');
        // CRUD::column('decimal_places');
        // CRUD::column('sup_org_id');
        // CRUD::column('is_active');
        // CRUD::column('created_at');
        // CRUD::column('updated_at');
        // CRUD::column('created_by');
        // CRUD::column('updated_by');
        // CRUD::column('deleted_by');
        // CRUD::column('deleted_uq_code');
        // CRUD::column('deleted_at');

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
        CRUD::setValidation(CurrenciesRequest::class);
        $fields = [

			
			$this->addSuperOrgField(),
            $this->addStoreField(),
			[
				'name' => 'symbol',
				'type' => 'text',
				'label' => 'Currency Symbol',
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'currency',
				'type' => 'text',
				'label' => 'Currency Name',
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'sub_currency',
				'type' => 'text',
				'label' => 'Sub Currency',
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'no_format',
				'type' => 'text',
				// 'type' => 'select_from_array',
				'label' => 'Number Format',
                // 'model_function' => ;
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
            [
				'name' => 'decimal_places',
				'type' => 'number',
				'label' => 'Decimal Places',
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],

			$this->addIsActiveField()

		];

		$this->crud->addFields(array_filter($fields));

        // CRUD::field('code');
        // CRUD::field('symbol');
        // CRUD::field('currency');
        // CRUD::field('sub_currency');
        // CRUD::field('no_format');
        // CRUD::field('decimal_places');
        // CRUD::field('sup_org_id');
        // CRUD::field('is_active');
        // CRUD::field('created_by');
        // CRUD::field('updated_by');
        // CRUD::field('deleted_by');
        // CRUD::field('deleted_uq_code');

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
