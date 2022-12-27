<?php

namespace App\Http\Controllers\Admin;

use App\Base\BaseCrudController;
use App\Http\Requests\AppSettingRequest;
use App\Models\AppSetting;
use App\Models\MstStore;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AppSettingCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AppSettingCrudController extends BaseCrudController
{
   
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\AppSetting::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/app-setting');
        CRUD::setEntityNameStrings('', 'app settings');
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
            $this->addSuperOrganizationColumn(),
            $this->addFiscalYearColumn(),
            [
				'name' => 'logo',
				'type' => 'image',
				'label' => trans('common.logo'),
				'disk'   => 'uploads', 
			],
            [
				'name' => 'background',
				'type' => 'image',
				'label' => 'Background',
				'disk'   => 'uploads', 
			],
            [
                'name'=>'pan_vat',
                'type'=>'text',
                'label'=>'Pan/Vat'
            ],
            [
                'name' => 'display_pan_vat',
                'label' => 'Display Pan/Vat',
                'type' => 'radio',
                'options' =>
                [
                    1 => 'Yes',
                    0 => 'No',
                ],
            ],
			$this->addIsActiveColumn(),
		];

		$this->crud->addColumns(array_filter($columns));
    }

    public function index(){
        $this->crud->hasAccessOrFail('list');

        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? mb_ucfirst($this->crud->entity_name_plural);

        if(backpack_user()->hasRole('superadmin')){
            return view($this->crud->getListView(), $this->data);
        }else{
            return redirect($this->crud->route . '/create');
        }
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    public function create()
    {
        $this->crud->hasAccessOrFail('create');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.add').' '.$this->crud->entity_name;

        $setting = AppSetting::whereSupOrgId(backpack_user()->sup_org_id)->get();
        if(count($setting) > 0){
            return redirect($this->crud->route . '/' . $setting[0]->id . '/edit');

        }else{
            return view($this->crud->getCreateView(), $this->data);
        }
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(AppSettingRequest::class);

        
        $arr = [
            $this->addSuperOrgField(),
            // $this->addStoreField(),
            $this->addFiscalYearField(),
            [
                'name' => 'number_system',
                'type' => 'select2_from_array',
                'label' => trans('common.number_system'),
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4',
                ],
                'options' => $this->numberSystem(),
            ],
            // [
            //     'name' => 'store_id',
            //     'type' => 'select',
            //     'entity' => 'storeEntity',
            //     'label' => 'Store Name',
            //     'attribute' => 'name_en',
            //     'model' => MstStore::class,
            //     'default'=> backpack_user()->store_id,
            //     'wrapperAttributes' => [
            //         'class' => 'form-group col-md-4',
            //     ],
            //     'options' => (function ($query) {
			// 		// return $query->where('sup_org_id', backpack_user()->sup_org_id)->get();
            //         switch (backpack_user()->user_level)
            //         {
            //             case config('users.user_level.super_user'):
            //                 return $query->where('is_active', true)
            //                     ->get();
            
            //             case config('users.user_level.organization_user'):
            //                 return $query->where('is_active', true)
            //                     ->whereSupOrgId(backpack_user()->sup_org_id)
            //                     ->get();
            
            //             case config('users.user_level.store_user'):
            //                 return $query->where('is_active', true)
            //                     ->whereId(backpack_user()->store_id)
            //                     ->get();
            
            //         }
            //     }),
            // ],
            [
				'name' => 'logo',
				'type' => 'image',
				'label' => trans('common.logo'),
				'disk'   => 'uploads', 
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4',
                ],
			],
            [
				'name' => 'background',
				'type' => 'image',
				'label' => 'Background Image',
				'disk'   => 'uploads',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4',
                ],
			],
            [  
                'name'          => 'header',
                'label'         => 'Header',
                'type'          => 'ckeditor',
            ],
           
            [  
                'name'          => 'footer',
                'label'         => 'Footer',
                'type'          => 'ckeditor',
            
                // optional:
                // 'extra_plugins' => ['oembed', 'widget'],
                // 'options'       => [
                //     'autoGrow_minHeight'   => 200,
                //     'autoGrow_bottomSpace' => 50,
                //     'removePlugins'        => 'resize,maximize',
                // ]
            ],
            [
				'name' => 'pan_vat',
				'type' => 'text',
				'label' => 'Pan/Vat',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4',
                ],
			],
            [
				'name' => 'display_pan_vat',
				'type' => 'boolean',
				'label' => 'Display Pan/Vat',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4',
                ],
			],
			$this->addIsActiveField(),
        ];
        $this->crud->addFields(array_filter($arr));

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
