<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstGender;
use App\Models\HrEmployee;
use App\Models\MstCountry;
use App\Models\MstDistrict;
use App\Models\MstPosition;
use App\Models\MstProvince;
use App\Models\MstRelation;
use App\Models\MstDepartment;
use App\Base\BaseCrudController;
use App\Base\Operations\FetchOperation;
use App\Http\Requests\HrEmployeeRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class HrEmployeeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class HrEmployeeCrudController extends BaseCrudController
{
    use FetchOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\HrEmployee::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/hr-employee');
        CRUD::setEntityNameStrings('', ' employees');


        // $this->crud->addFilter([
        //     'name'  => 'position_id',
        //     'type'  => 'select2',
        //     'label' => 'Position'
        //   ], function() {
        //       return \App\Models\MstPosition::all()->pluck('name_lc', 'id')->toArray();
        //   }, function($value) { // if the filter is active
        //       $this->crud->addClause('where', 'position_id', $value);
        //   });
    }

    public function fetchMstGender(){
        return $this->fetch(MstGender::class);
    }

    public function fetchMstPosition(){
        return $this->fetch(MstPosition::class);
    }

    public function fetchMstDepartment(){
        return $this->fetch(MstDepartment::class);
    }

    public function fetchMstRelation(){
        return $this->fetch(MstRelation::class);
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
            $this->addRowNumberColumn(),
            [
                'name' => 'full_name',
                'type' => 'text',
                'label' => trans('Full Name')
            ],
            [
                'name'      => 'gender_id', // the column that contains the ID of that connected entity;
                'label'     => 'Gender',
                'type'      => 'select',
                'entity'    => 'genderEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_en', // foreign key attribute that is shown to user
                'model'     => MstGender::class
            ],
            [
                'name' => 'photo',
                'type' => 'image',
                'disk' => 'uploads'
            ],
            [
                'name' => 'address',
                'type' => 'text',
                'label' => 'Address',
            ],
            [
                'name' => 'country_id',
                'label' => 'Country',
                'type' => 'select',
                'entity' => 'countryEntity',
                'attribute' => 'name_en',
                'model' => MstCountry::class,

            ],
            [
                'name' => 'pan_number',
                'type' => 'number',
                'label' => 'Pan Number',
            ],
            [
                'name' => 'pan_photo_upload',
                'type' => 'upload',
                'disk' => 'uploads',
                'label' => 'Pan Photo'
            ],
            $this->addProvinceColumn(),
            [
                'name' => 'date_ad',
                'type' => 'date',
                'label' => 'Date Of Birth(A.D.)',
            ],
            [
                'name' => 'date_bs',
                'type' => 'date',
                'label' => 'Date Of Birh(B.S.)',
            ],
            [
                'name' => 'position_id',
                'type' => 'select',
                'entity' => 'positionEntity',
                'attribute' => 'name_lc',
                'model' => MstPosition::class,
            ],
            [
                'name' => 'department_id',
                'type' => 'select',
                'entity' => 'departmentEntity',
                'attribute' => 'name_lc',
                'model' => MstDepartment::class,
            ],

            [
                'name' => 'email',
                'type' => 'email',
                'label' => 'email',
            ],
            [
                'name' => 'contact_number',
                'type' => 'phone',
                'label' => 'Contact Number',
            ],
            [
                'name' => 'citizenship_number',
                'type' => 'number',
                'label' => 'Citizenship Number',
            ],
            [
                'name' => 'citizenship_file_upload',
                'type' => 'upload',
                'label' => 'Citizenship Photo',
                'disks' => 'uploads',
            ],
            [
                'name' => 'national_identity_file_upload',
                'type' => 'upload',
                'label' => ' National Identity Photo',
                'disk' => 'uploads',
            ],
            [
                'name' => 'photo',
                'type' => 'image',
                'disk' => 'uploads',
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
        CRUD::setValidation(HrEmployeeRequest::class);


        $fields = [
            [
                'name' => 'full_name',
                'type' => 'text',
                'label' => 'Full Name',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ]
            ],
            $this->addStoreField(),
            // [
            //     'name' => 'gender_id',
            //     'type' => 'relationship',
            //     'label' => 'Gender',
            //     'entity' => 'genderEntity',
            //     'attribute' => 'name_en',
            //     'model' => MstGender::class,
            //     'inline_create' => [
            //         'entity'=>'mst-gender',
            //         'modal_class' => 'modal-dialog modal-xl',
            //     ],
            //     'data_source' => backpack_url('/hr-employee/fetch/mst-gender'),
            //     'ajax' => true,
            //     'minimum_input_length' => 0,
            //     'wrapper' => [
            //         'class' => 'form-group col-md-4',
            //     ],
            //     'options'   => (function ($query) {
            //         return (new MstGender())->getFieldComboOptions($query);
            //     }),
            // ],

            [
                'name' => 'photo',
                'type' => 'image',
                'disk' => 'uploads',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ]

            ],
            [
                'name' => 'country_id',
                'type' => 'select2',
                'entity' => 'countryEntity',
                'attribute' => 'name_en',
                'default' => 1,
                'model' => MstCountry::class,
                'options'   => (function ($query) {
                    return (new MstCountry())->getFieldComboOptions($query);
                }),
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ]


            ],
            [
                'name' => 'province_id',
                'type' => "select2_from_ajax",
                'method' => 'GET',
                'label' => 'Province',
                'model' => MstProvince::class,
                'entity' => "provinceEntity", //relatioship which is inside the model
                'attribute' => "name_en", //the field which is needed
                'data_source' => url("api/province/country_id"), //api/modelsmallname/tableid from which state is taken
                'minimum_input_length' => 0,
                'dependencies' => ["country-id"], //id from which state is pulled
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4 current_address',
                ],
                'attributes' => [
                    'placeholder' => 'Select country first',
                    'id' => 'current_country'
                ]
            ],
            [
                'name' => 'district_id',
                'type' => "select2_from_ajax",
                'method' => 'GET',
                'label' => 'District',
                'model' => MstDistrict::class,
                'entity' => "countryEntity", //relatioship which is inside the model
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
                'name' => 'date_bs',
                'type' => 'nepali_date',
                'label' => 'Date Of Birth(B.S.)',
                'attributes'=>[
                    'id'=>'date_bs',
                    'relatedId'=>'date_ad'
                ],
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ]
            ],
            [
                'name' => 'date_ad',
                'type' => 'date',
                'label' => 'Date Of Birth(A.D.)',
                'attributes'=>[
                    'id'=>'date_ad',
                ],
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ]
            ],

            // [
            //     'name' => 'position_id',
            //     'type' => 'relationship',
            //     'label' => 'Position',
            //     'entity' => 'positionEntity',
            //     'attribute' => 'name_en',
            //     'model' => MstPosition::class,
            //     'inline_create' => [
            //         'entity'=>'mst-position',
            //         'modal_class' => 'modal-dialog modal-xl',
            //     ],
            //     'data_source' => backpack_url('/hr-employee/fetch/mst-position'),
            //     'ajax' => true,
            //     'minimum_input_length' => 0,
            //     'wrapper' => [
            //         'class' => 'form-group col-md-4',
            //     ],
            //     'options'   => (function ($query) {
            //         return (new MstPosition())->getFieldComboOptions($query);
            //     }),
            // ],

            [
                'name' => 'department_id',
                'type' => 'relationship',
                'label' => 'Department',
                'entity' => 'departmentEntity',
                'attribute' => 'name_en',
                'model' => 'App\Models\MstDepartment',
                'inline_create' => [
                    'entity'=>'mst-department',
                    'force_select' => true,
                    'modal_class' => 'modal-dialog modal-xl',
                    // 'modal_route' => route('mst-department-inline-create'), // InlineCreate::getInlineCreateModal()
                    // 'create_route' => route('**mst-department-inline-create-save**'), // InlineCreate::storeInlineCreate()
                ],
                'data_source' => backpack_url('/hr-employee/fetch/mst-department'),
                'ajax' => true,
                'minimum_input_length' => 0,
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'options'   => (function ($query) {
                    return (new MstDepartment())->getFieldComboOptions($query);
                }),
            
            ],

            [
                'name' => 'address',
                'type' => 'text',
                'label' => 'Address',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ]
            ],
            [
                'name' => 'email',
                'type' => 'email',
                'label' => 'E-mail',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ]
            ],
            [
                'name' => 'contact_number',
                'type' => 'number',
                'label' => 'Contact Number',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ]

            ],

            [
                'name' => 'pan_number',
                'type' => 'number',
                'label' => 'Pan Number',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ]
            ],
            [
                'name' => 'pan_photo_upload',
                'type' => 'upload',
                'label' => 'Upload Pan Photo',
                'disk' => 'uploads',
                'upload' => true,
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ]
            ],
            [
                'name' => 'citizenship_number',
                'type' => 'number',
                'label' => 'Citizenship Number',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ]
            ],
            [
                'name' => 'citizenship_file_upload',
                'type' => 'upload',
                'label' => 'Upload Citizenship Photo',
                'disk' => 'uploads',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ]
            ],
            [
                'name' => 'national_identity_number',
                'type' => 'number',
                'label' => 'National Identity Number',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ]
            ],
            [
                'name' => 'national_identity_file_upload',
                'type' => 'upload',
                'label' => 'Upload National Identity Photo',
                'disk' => 'uploads',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ]
            ],
    
            [
                'name' => 'legend1',
                'type' => 'custom_html',
                'value' => '<legend>Person Details:</legend>',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-12',
                ],
            ],
            [
                'name'    => 'person_full_name',
                'type'    => 'text',
                'label'   => trans('Full Name'),
                'wrapper' => ['class' => 'form-group col-md-4'],
                'attributes' => ['maxlength' => '50'],
            
            ],

            // [
            //     'name'    => 'relation_id',
            //     'type'    => 'select2',
            //     'label'   => trans('Relation'),
            //     'attribute' => 'name_lc',
            //     'entity' => 'relationEntity',
            //     'model' => MstRelation::class,
            //     'options'   => (function ($query) {
            //         return (new MstRelation())->getFieldComboOptions($query);
            //     }),
            //     'wrapper' => ['class' => 'form-group col-md-4'],
            //     'attributes' => ['maxlength' => '50'],
            // ],
            [
                'name' => 'relation_id',
                'type' => 'relationship',
                'label' => 'Relation',
                'entity' => 'relationEntity',
                'attribute' => 'name_en',
                'model' => MstRelation::class,
                'inline_create' => [
                    'entity'=>'mst-relation',
                    'modal_class' => 'modal-dialog modal-xl',
                ],
                'data_source' => backpack_url('/hr-employee/fetch/mst-relation'),
                'ajax' => true,
                'minimum_input_length' => 0,
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'options'   => (function ($query) {
                    return (new MstRelation())->getFieldComboOptions($query);
                }),
            ],

            [
                'name'    => 'person_email',
                'type'    => 'email',
                'label'   => trans('Email'),
                'wrapper' => ['class' => 'form-group col-md-4'],
                'attributes' => ['maxlength' => '50'],
                'required' => true
            ],
            [
                'name'    => 'person_contact_number',
                'type'    => 'number',
                'label'   => trans('Contact Number'),
                'wrapper' => ['class' => 'form-group col-md-4'],
                'attributes' => ['maxlength' => '50'],
                'required' => true
            ],
            [
                'name'    => 'person_address',
                'type'    => 'text',
                'label'   => trans('Address'),
                'wrapper' => ['class' => 'form-group col-md-4'],
                'attributes' => ['maxlength' => '50'],
                'required' => true
            ],
            [
                'name'    => 'person_citizenship_number',
                'type'    => 'number',
                'label'   => trans('Citizenship Number'),
                'wrapper' => ['class' => 'form-group col-md-4'],
                'attributes' => ['maxlength' => '50'],
                'required' => true
            ],
            [
                'name'    => 'person_citizenship_photo_upload',
                'type'    => 'upload',
                'label'   => trans('Upload Citizenship Photo '),
                'wrapper' => ['class' => 'form-group col-md-4'],
                'attributes' => ['maxlength' => '50'],
                'disk' => 'uploads',
                'required' => true
            ],

            $this->addIsActiveField()

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
