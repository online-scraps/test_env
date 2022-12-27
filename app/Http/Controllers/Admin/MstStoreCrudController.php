<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\MstStore;
use App\Models\SupOrganization;
use App\Base\BaseCrudController;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;
use App\Http\Requests\MstStoreRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MstStoreCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MstStoreCrudController extends BaseCrudController
{


	/**
	 * Configure the CrudPanel object. Apply settings to all operations.
	 *
	 * @return void
	 */
	public function setup()
	{
		CRUD::setModel(\App\Models\MstStore::class);
		CRUD::setRoute(config('backpack.base.route_prefix') . '/mst-store');
		CRUD::setEntityNameStrings('', 'Stores');
		$this->isAllowed();
        $this->user = backpack_user();
		$this->crud->store_flag = false;
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
			$this->addSuperOrganizationColumn(),
			[
                'name' => 'parent_id',
                'label' => 'Parent Store',
                'type' => 'select',
                'entity' => 'parentStore',
                'attribute' => 'name_en',
                'model' => MstStore::class,

            ],
			[
				'name' => 'name_en',
				'type' => 'text',
				'label' => trans('common.name_en'),
			],
			[
				'name' => 'address',
				'type' => 'text',
				'label' => trans('common.address'),
			],
			[
				'name' => 'email',
				'type' => 'email',
				'label' => trans('common.email'),
			],
			[
				'name' => 'phone_no',
				'type' => 'text',
				'label' => trans('common.phone_no'),
			],

			// [
            //     'label'     => trans('common.store_user_id'),
            //     'type'      => 'select',
            //     'name'      => 'store_user_id', // the column that contains the ID of that connected entity;
            //     'entity'    => 'userEntity', // the method that defines the relationship in your Model
            //     'attribute' => 'name', // foreign key attribute that is shown to user
            //     'model'     => User::class
            // ],
			$this->addSuperOrganizationColumn(),
			$this->addIsActiveColumn(),
		];

		$this->crud->addColumns(array_filter($columns));
        $this->filterListByUserLevel();


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
		CRUD::setValidation(MstStoreRequest::class);

		$fields = [
			$this->addReadOnlyCodeField(),

			$this->addPlainHtml(),

			$this->addSuperOrgField(),


			[
				'name' => 'name_en',
				'type' => 'text',
				'label' => trans('common.name_en'),
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'name_lc',
				'type' => 'text',
				'label' => trans('common.name_lc'),
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
                'name' => 'parent_id',
                'label' => 'Parent Store',
                'type' => 'select2',
                'entity' => 'parentStore',
                'attribute' => 'name_en',
                'model' => MstStore::class,
                'options'   => (function ($query) {
                    return $query->where('sup_org_id', $this->user->sup_org_id)->orderBy('created_at', 'ASC')->get();
                }),
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],

            ],

			[
				'name' => 'address',
				'type' => 'text',
				'label' => trans('common.address'),
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'email',
				'type' => 'email',
				'label' => trans('common.email'),
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'phone_no',
				'type' => 'text',
				'label' => 'Mobile Number',
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'logo',
				'type' => 'image',
				'label' => trans('common.logo'),
				'upload' => true,
				'disk' => 'uploads',
				'crop' => true, // set to true to allow cropping, false to disable
				'aspect_ratio' => 1, // ommit or set to 0 to allow any aspect ratio
				'wrapperAttributes' => [
					'class' => 'form-group col-md-4',
				],
			],
			// [
			// 	'name'  => 'store_user_id',
			// 	'label' => trans('common.store_user_id'),
			// 	'value' =>backpack_user()->id,
			// 	'type'=> 'hidden'
			// ],


			// ],
			$this->addDescriptionField(),
			$this->addIsActiveField(),
		];

		$this->crud->addFields(array_filter($fields));

		/**
		 * Fields can be defined using the fluent syntax or array syntax:
		 * - CRUD::field('price')->type('number');
		 * - CRUD::addField(['name' => 'price', 'type' => 'number']));
		 */
	}

	// private function cloneSuperData($sup_org_id, $store_id){
	// 	// Write a proper cases from store form
    //     $this->mstUnits($sup_org_id, $store_id);
    // }

	// private function mstUnits($sup_org_id, $store_id){
	// 	$false = 'false';
	// 	$sql ="
	// 	INSERT INTO mst_units(sup_org_id,store_id,code,name_en,name_lc,is_active,is_super_data)
	// 	SELECT $sup_org_id sup_org_id, $store_id store_id, code, name_en, name_lc, is_active, $false is_super_data
	// 	FROM mst_units
	// 	WHERE is_super_data = true and deleted_uq_code = 1
	// 	ON CONFLICT (sup_org_id,store_id, code, deleted_uq_code)
	// 	DO UPDATE SET
	// 	code = mst_units.code,
	// 	name_en = mst_units.name_en,
	// 	name_lc = mst_units.name_lc,
	// 	is_super_data = $false,
	// 	is_active = mst_units.is_active";
	// 	DB::statement(DB::raw($sql));
    // }


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

	public function store()
    {
        $this->crud->hasAccessOrFail('create');




        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        // register any Model Events defined on fields
        $this->crud->registerFieldEvents();

        // insert item in the db
        $item = $this->crud->create($this->crud->getStrippedSaveRequest($request));
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }
}
