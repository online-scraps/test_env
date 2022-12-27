<?php

namespace App\Http\Controllers\Admin;

use App\Base\BaseCrudController;
use App\Http\Requests\BatchNoRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class BatchNoCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BatchNoCrudController extends BaseCrudController
{


    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\BatchNo::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/batch-no');
        CRUD::setEntityNameStrings('', 'batch no');
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
			//            $this->addCodeColumn(),
			$this->addNameEnColumn(),
			$this->addNameLcColumn(),
			[
				'name' => 'sequence_code',
				'type' => 'text',
				'label' => trans('common.sequence_code'),
			],
            $this->addSuperOrganizationColumn(),
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
        CRUD::setValidation(BatchNoRequest::class);
        $fields = [

            $this->addReadOnlyCodeField(),
            $this->addPlainHtml(),
			$this->addSuperOrgField(),
			$this->addStoreField(),
            $this->addNameEnField(),
            $this->addNameLcField(),
            $this->addDescriptionField(),
			[
				'name' => 'sequence_code',
				'type' => 'text',
				'label' => trans('common.sequence_code'),
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
