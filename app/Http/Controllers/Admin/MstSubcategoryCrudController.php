<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstCategory;
use Illuminate\Http\Request;
use App\Models\MstSubcategory;
use App\Base\BaseCrudController;
use Illuminate\Support\Facades\DB;
use App\Base\Operations\FetchOperation;
use App\Http\Requests\MstSubcategoryRequest;
use App\Base\Operations\InlineCreateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MstSubcategoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MstSubcategoryCrudController extends BaseCrudController
{
    use InlineCreateOperation;
    use FetchOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\MstSubcategory::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/mst-subcategory');
        CRUD::setEntityNameStrings('', 'Sub categories');
        $this->isAllowed(['getSubCategoryAPI'=>'list']);
        $this->user = backpack_user();
    }

    public function fetchMstCategory()
    {
        $results = DB::select('select * from mst_categories where sup_org_id = ?', [$this->user->sup_org_id]);
        return $results;$results = DB::select('select * from mst_categories where sup_org_id = ?', [$this->user->sup_org_id]);
        return $results;
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
            [
                'label'     => 'Category',
                'type'      => 'select',
                'name'      => 'category_id', // the column that contains the ID of that connected entity;
                'entity'    => 'category', // the method that defines the relationship in your Model
                'attribute' => 'name_en', // foreign key attribute that is shown to user
                'model'     => MstCategory::class
            ],
            $this->addSuperOrganizationColumn(),
            $this->addIsActiveColumn(),

        ];
        $this->crud->addColumns(array_filter($cols));
        $this->filterListByUserLevel(false);
        if(!$this->user->isSystemUser()){
            $this->crud->addButtonFromView('top', 'fetchMasterData', 'fetchMasterData', 'end');
        }


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
        CRUD::setValidation(MstSubcategoryRequest::class);

        $fields = [
            $this->addReadOnlyCodeField(),
            $this->addPlainHtml(),
            $this->addSuperOrgField(),
            $this->addStoreField(),
            $this->addNameEnField(),
            $this->addNameLcField(),
            // [
            //     'name'  => 'category_id',
            //     'label' => 'Category',
            //     'type'  => 'select2',
            //     'entity' => 'category',
            //     'attribute' => 'name_en',
            //     'placeholder' => 'Select Category',
            //     'model' => MstCategory::class,
            //     'wrapper' => [
            //         'class' => 'form-group col-md-6',
            //     ],
            //     'options' => (function ($query){
			// 		return $query->where('sup_org_id',backpack_user()->sup_org_id)->get();
			// 	    }),
            // ],
            [
                'name' => 'category_id',
                'type' => 'relationship',
                'label' => 'Category',
                'entity' => 'category',
                'attribute' => 'name_en',
                'model' => MstCategory::class,
                'inline_create' => [
                    'entity'=>'mst-category',
                    'modal_class' => 'modal-dialog modal-xl',
                ],
                'data_source' => backpack_url('/mst-subcategory/fetch/mst-category'),
                'ajax' => true,
                'minimum_input_length' => 0,
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'options' => (function ($query){
                    return $query->where('sup_org_id',backpack_user()->sup_org_id)->get();
                }),
            ],
            $this->addDescriptionField(),
            $this->addIsActiveField(),
            $this->addIsSuperDataField(),
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
    // public function getSubCategoryAPI($category_id){
    //     dd("hgsfhsdf");
    //     return MstSubcategory::where('category_id',$category_id)->get();
    // }
    public function getSubCategoryAPI(Request $request,$value)
    {
        $search_term = $request->input('q');
        $form = collect($request->input('form'))->pluck('value', 'name');
        $page = $request->input('page');
        $options = MstSubcategory::query();//model ma query gareko
        // if no category has been selected, show no options
        if (! data_get($form, $value)) {//countryvanne table ma search gareko using id
            return [];
        }
        // if a category has been selected, only show articles in that category
        if (data_get($form, $value)) {
            if($form[$value] != 8){
                $category = MstCategory::find($form[$value]);
                $options = $options->where('category_id', $category->id);
            }
        }
        // if a search term has been given, filter results to match the search term
         if ($search_term) {
            //  dd($search_term);
            $options = $options->where('name_en', 'ILIKE', "%$search_term%");//k tannalako state ho tesaile
        }

        // dd($options->get());

        return $options->paginate(10);
    }
}
