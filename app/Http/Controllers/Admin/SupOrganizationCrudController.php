<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\MstDistrict;
use App\Models\SupOrganization;
use App\Base\BaseCrudController;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;
use App\Http\Requests\SupOrganizationRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SupOrganizationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SupOrganizationCrudController extends BaseCrudController
{

	/**
	 * Configure the CrudPanel object. Apply settings to all operations.
	 *
	 * @return void
	 */
	public function setup()
	{
		CRUD::setModel(\App\Models\SupOrganization::class);
		CRUD::setRoute(config('backpack.base.route_prefix') . '/sup-organization');
		CRUD::setEntityNameStrings('', 'Organizations');
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
			[
				'name' => 'code',
				'type' => 'text',
				'label' => trans('common.code'),
			],
			[
				'name' => 'name_en',
				'type' => 'text',
				'label' => trans('common.name_en'),
			],
			[
				'name' => 'name_lc',
				'type' => 'text',
				'label' => trans('common.name_lc'),
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
			[
				'name' => 'logo',
				'type' => 'image',
				'label' => trans('common.logo'),
				'disk'   => 'uploads',
			],
			$this->addIsActiveColumn()
		];

		$this->crud->addColumns($columns);
		


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
		CRUD::setValidation(SupOrganizationRequest::class);

		$fields = [
			[
				'name' => 'code',
				'type' => 'text',
				'label' => trans('common.code'),
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
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
				'name' => 'email',
				'type' => 'email',
				'label' => trans('common.email'),
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'phone_no',
				'type' => 'number',
				'label' => trans('common.phone_no'),
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			$this->addCountryField(),
			$this->addProvinceField(),
			[
                'name' => 'district_id',
                'type' => "select2_from_ajax",
                'method' => 'GET',
                'label' => 'District',
                'model' => MstDistrict::class,
                'entity' => "districtEntity", //relatioship which is inside the model
                'attribute' => "name_en", //the field which is needed
                'data_source' => url("api/district/province_id"), //api/modelsmallname/tableid from which state is taken
                'minimum_input_length' => 0,
                'dependencies' => ["province_id"], //id from which state is pulled
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4 current_address',
                ],
                'attributes' => [
                    'placeholder' => 'Select province first',
                    'id' => 'current_district'
                ]
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
				'name' => 'logo',
				'type' => 'image',
				'label' => trans('common.logo'),
				'upload' => true,
				'disk' => 'uploads',
				'crop' => true, // set to true to allow cropping, false to disable
				'aspect_ratio' => 0, // ommit or set to 0 to allow any aspect ratio
				'wrapperAttributes' => [
					 'class' => 'form-group col-md-4',
				 ],
			],
			[
				'name' => 'multiple_barcode',
				'label' => 'Barcode Trnx Type',
				'type' => 'radio',
				'default' => 1,
				'inline' => true,
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
				'options' =>
				[
                    1 => 'MB : 1_Item',
                    0 => '1B : M_Item',
				],
			],

			$this->addIsActiveField(),
			[
				'name' => 'description',
				'type' => 'textarea',
				'label' => trans('common.description'),
				'wrapper' => [
					'class' => 'form-group col-md-12',
				],
			],
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

	public function store()
    {
        $this->crud->hasAccessOrFail('create');
        $user = backpack_user();

        $request = $this->crud->validateRequest();

        $request->request->set('created_by', $user->id);
        $request->request->set('updated_by', $user->id);

        DB::beginTransaction();
        try {
                $item = $this->crud->create($request->except(['save_action', '_token', '_method', 'http_referrer']));
                $user = User::create([
                    'name' => $request->get('name_en') . '_admin',
                    'email' =>  $request->get('email'),
                    'password' => bcrypt('Admin@1234'),
                    'sup_org_id' => $item->id,
                    'user_level' =>config('users.user_level.organization_user'),
                ]);

                $org_admin_id = DB::table('users')->where('id', $user->id)->pluck('id')->first();
                $user->assignRoleCustom("organizationadmin",$org_admin_id);

            \Alert::success(trans('backpack::crud.insert_success'))->flash();

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollback();
            dd($th);
        }

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }
}
