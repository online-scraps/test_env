<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstStore;
use App\Models\MstGender;
use App\Models\MstCountry;
use App\Models\MstCustomer;
use App\Models\MstDistrict;
use App\Models\MstProvince;
use App\Base\BaseCrudController;
use App\Http\Requests\MstCustomerRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MstCustomerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MstCustomerCrudController extends BaseCrudController
{
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(MstCustomer::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/mst-customer');
        CRUD::setEntityNameStrings('', 'customers');
        $this->isAllowed();
        $this->data['script_js'] = $this->getScripts();
        $this->user = backpack_user();
    }

    public function getScripts()
    {
        return
            "
            function showHideCoorporateFields(){
                var coorporate = $('#coorporate').val();
                if(coorporate == 1){
                    $('#company_name').show();
                    $('#pan_no').show();
                    // $('#gender_id option[value=\"3\"]').attr('selected', 'selected');
                    // $('#gender_id').val(3);
                    // var data = $('gender_id option:selected').text();
                    // $('#gender_id').text(data);
                }else{
                    $('#company_name').hide();
                    $('#pan_no').hide();
                    // $('#gender_id').val(null).trigger('change');
                }
            }
            $(document).ready(function() {
                showHideCoorporateFields();
                $('#coorporate').on('change', function(){
                    showHideCoorporateFields();
                });
            });
            ";
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
            //            $this->addCodeColumn(),
            $this->addNameEnColumn(),
            $this->addNameLcColumn(),
            $this->addSuperOrganizationColumn(),
            // [
            //     'label'     => 'Country',
            //     'type'      => 'select',
            //     'name'      => 'country_id', // the column that contains the ID of that connected entity;
            //     'entity'    => 'countryEntity', // the method that defines the relationship in your Model
            //     'attribute' => 'name_en', // foreign key attribute that is shown to user
            //     'model'     => MstCountry::class
            // ],
            // [
            //     'label'     => 'Province',
            //     'type'      => 'select',
            //     'name'      => 'province_id', // the column that contains the ID of that connected entity;
            //     'entity'    => 'provinceEntity', // the method that defines the relationship in your Model
            //     'attribute' => 'name_en', // foreign key attribute that is shown to user
            //     'model'     => MstProvince::class
            // ],
            // [
            //     'label'     => 'District',
            //     'type'      => 'select',
            //     'name'      => 'district_id', // the column that contains the ID of that connected entity;
            //     'entity'    => 'districtEntity', // the method that defines the relationship in your Model
            //     'attribute' => 'name_en', // foreign key attribute that is shown to user
            //     'model'     => MstDistrict::class
            // ],
            [
                'name' => 'address',
                'type' => 'text',
                'label' => 'Address',
            ],
            [
                'name' => 'email',
                'type' => 'email',
                'label' => 'Email',
            ],
            // [
            //     'name' => 'company_name',
            //     'type' => 'text',
            //     'label' => 'Company Name',
            // ],
            [
                'name' => 'contact_number',
                'type' => 'text',
                'label' => 'Contact Number',
            ],

            $this->addIsActiveColumn(),
        ];
        $this->crud->addColumns(array_filter($cols));
        $this->filterListByUserLevel(false);

        //Add this clause to only display Customers
        $this->crud->addClause('where','is_customer', true);

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
        CRUD::setValidation(MstCustomerRequest::class);
        $fields = [
            $this->addReadOnlyCodeField(),
            $this->addPlainHtml(),
            $this->addSuperOrgField(),
            $this->addStoreField(),
            $this->addNameEnField(),
            $this->addNameLcField(),
            [
                'name' => 'is_customer',
                'type' => 'hidden',
                'value' => true
            ],
            [  // Select
                'label'     => 'Customer Type',
                'type' => 'select2_from_array',
                'name' => 'is_coorporate',
                'options'     => [false => 'Individual', true => 'Business / Coorporate'],
                'allows_null' => false,
                'default'     => false,
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6',
                ],
                'attributes' => [
                    'id' => 'coorporate',
                    'required' => 'required'
                ],
            ],
            // [  // Select
            //     'label'     => 'Gender',
            //     'type' => 'select2',
            //     'name' => 'gender_id',
            //     'method' => 'GET',
            //     'entity' => 'genderEntity',
            //     'attribute' => 'name_en',
            //     'options'   => (function ($query) {
            //         return (new MstGender())->getFieldComboOptions($query);
            //     }),
            //     'model' => MstGender::class,
            //     'wrapper' => [
            //         'class' => 'form-group col-md-6',
            //     ],
            //     'attributes' => [
            //         'id' => 'gender_id',
            //         'required' => 'required'
            //     ],
            // ],
            [
                'name'  => 'company_name',
                'label' => 'Company Name',
                'type'  => 'text',
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                    'id' => 'company_name'
                ],
            ],
            [
                'name'  => 'pan_no',
                'label' => 'PAN Number',
                'type'  => 'number',
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                    'id' => 'pan_no'
                ],
            ],
            [
                'name'  => 'country_id',
                'label' => 'Country',
                'type' => 'select2',
                'entity' => 'countryEntity',
                'attribute' => 'name_en',
                'model' => MstCountry::class,
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
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
                'name'  => 'address',
                'label' => 'Address',
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => 'Email',
                'type'  => 'email',
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
            ],
            [
                'name'  => 'contact_number',
                'label' => 'Contact Number',
                'type'  => 'number',
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
            ],
            $this->addDescriptionField(),
            $this->addIsActiveField()
        ];
        $this->crud->addFields(array_filter($fields));
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

    public function getCustomerDetailById($id)
    {
        $customers = MstCustomer::where([['id', $id], ['is_active', true]])->first();
        return response()->json([
            'status' => 'success',
            'customer' => $customers
        ]);
    }

    public function getCustomerDetailByName($name)
    {
        // $customers = MstCustomer::where([['name_en', $name], ['sup_org_id',  $this->user->sup_org_id_id], ['is_active', true]])->get();
        // if(count($customers) == 1){
        //     return response()->json([
        //         'status' => 'success',
        //         'customer' => $customers
        //     ]);
        // }else{
        //     return response()->json([
        //         'status' => 'error',
        //         'customer' => 'No customer found'
        //     ]);
        // }

        return response()->json([
            'status' => 'error',
            'customer' => 'No customer found'
        ]);
    }

    public function getCustomerDetailByCompanyName($name)
    {
        $customers = MstCustomer::where([['company_name', ltrim($name)], ['is_active', true]])->get();
        // dd(ltrim($name), $customers);
        // if(count($customers) == 1){
            return response()->json([
                'status' => 'success',
                'customer' => $customers
            ]);
        // }else{
        //     return response()->json([
        //         'status' => 'success',
        //         'customer' => $customers
        //     ]);
        // }

        // return response()->json([
        //     'status' => 'success',
        //     'customer' => 'No customer found'
        // ]);
    }
}
