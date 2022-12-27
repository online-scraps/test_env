<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstItem;
use App\Models\MstUnit;
use App\Models\MstBrand;
use App\Models\MstStore;
use App\Models\MstCategory;
use App\Models\MstDiscMode;
use App\Models\MstSupplier;
use App\Models\MstAssetType;
use Illuminate\Http\Request;
use App\Models\MstDepartment;
use App\Models\MstSubcategory;
use App\Base\BaseCrudController;
use App\Base\Traits\FilterStore;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\MstItemRequest;
use App\Base\Operations\FetchOperation;
use App\Imports\ItemEntriesExcelImport;
use Illuminate\Support\Facades\Validator;
use App\Base\Operations\InlineCreateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MstItemCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MstItemCrudController extends BaseCrudController
{
	use FilterStore, InlineCreateOperation, FetchOperation;

    /**
     * @var Backpack User
     */
    private $user;
    private $multipleBarcode;

	/**
	 * Configure the CrudPanel object. Apply settings to all operations.
	 *
	 * @return void
	 */
	public function setup()
	{
		CRUD::setModel(\App\Models\MstItem::class);
		CRUD::setRoute(config('backpack.base.route_prefix') . '/mst-item');
		CRUD::setEntityNameStrings('', ' items');
		$this->user = backpack_user();
        // dd($this->user, backpack_user()->superOrganizationEntity);
		$this->multipleBarcode = backpack_user()->superOrganizationEntity->multiple_barcode;
	}

	public function fetchMstCategory()
    {
        $results = DB::select('select * from mst_categories where sup_org_id = ?', [$this->user->sup_org_id]);
        return $results;
    }

	public function fetchMstSubcategory()
    {
        $results = DB::select('select * from mst_subcategories where sup_org_id = ?', [$this->user->sup_org_id]);
        return $results;
        // return $this->fetch(mstSubCategory::class);
    }

	public function fetchMstUnit()
    {
        // return $this->fetch(MstUnit::class);
        $results = DB::select('select * from mst_units where sup_org_id = ?', [$this->user->sup_org_id]);
        return $results;
    }

	public function fetchMstSupplier()
    {
        // return $this->fetch(MstSupplier::class);
        $results = DB::select('select * from mst_suppliers where sup_org_id = ?', [$this->user->sup_org_id]);
        return $results;
    }

	public function fetchMstDiscMode()
    {
        return $this->fetch(MstDiscMode::class);
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
			$this->addCodeColumn(),
			$this->addSuperOrganizationColumn(),
			// [
			// 	'label'     => 'Item Price',
			// 	'type'      => 'text',
			// 	'name'      => 'item_price', // the column that contains the ID of that connected entity;
			// ],

			[
				'name' => 'name',
				'type' => 'text',
				'label' => 'Product Name',
			],
			[
				'label'     => 'Supplier',
				'type'      => 'select',
				'name'      => 'supplier_id', // the column that contains the ID of that connected entity;
				'entity'    => 'mstSupplierEntity', // the method that defines the relationship in your Model
				'attribute' => 'name_en', // foreign key attribute that is shown to user
				'model'     => MstSupplier::class,
			],
			[
				'label'     => 'Brand',
				'type'      => 'select',
				'name'      => 'brand_id', // the column that contains the ID of that connected entity;
				'entity'    => 'mstBrandEntity', // the method that defines the relationship in your Model
				'attribute' => 'name_en', // foreign key attribute that is shown to user
				'model'     => MstBrand::class,
			],
			[
				'label'     => trans('common.category_id'),
				'type'      => 'select',
				'name'      => 'category_id', // the column that contains the ID of that connected entity;
				'entity'    => 'category', // the method that defines the relationship in your Model
				'attribute' => 'name_en', // foreign key attribute that is shown to user
				'model'     => MstCategory::class,
			],
			[
				'label'     => 'Sub Category',
				'type'      => 'select',
				'name'      => 'sub_category_id', // the column that contains the ID of that connected entity;
				'entity'    => 'mstSubCategory', // the method that defines the relationship in your Model
				'attribute' => 'name_en', // foreign key attribute that is shown to user
				'model'     => MstSubcategory::class,
			],
			$this->addIsActiveColumn(),
		];
		// MstStore::find($this->user->store_id)->itemEntity);/

		$this->crud->addColumns(array_filter($columns));
		$this->filterListByUserLevel();
        $this->crud->addButtonFromView('top', 'excelImport', 'excelImport', 'end');
        $this->crud->addButtonFromModelFunction('top', 'itemsSampleExcel', 'itemsSampleExcel', 'end');
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
		$this->crud->setValidation(MstItemRequest::class);

		$fields = [
            [
				'name' => 'is_fixed_asset',
				'label' => 'Is Fixed Asset ?',
				'type' => 'toggle',
				'default' => 0,
				'inline' => true,
				'options' =>
				[
					1 => 'Yes',
					0 => 'No',
				],
				'hide_when' => [ // these fields hide (by name) when the key matches the radio value
					1 => ['subcategory_id', 'category_id'], // When is fixed asset
					0 => ['asset_type_id','department_id', 'sub_department_id']
				],
				'wrapperAttributes' => [
					'class' => 'form-group col-md-4',
					'id' => 'is_fixed_asset',
				],
				'attributes' => [
					'id' => 'is_fixed_asset',
				],
			],
			[
				'type' => 'custom_html',
				'name' => 'plain_html_2',
				'value' => '<br>',
			],
			$this->addCodeField(),
			$this->addSuperOrgField(),
            $this->addStoreField(),
			$this->addPlainHtml(),
			[    // Select2Multiple = n-n relationship (with pivot table)
				'label'     => "Stores",
				'type'      => 'select2_multiple',
				'name'      => 'mstItemStores', // the method that defines the relationship in your Model

				// optional
				'entity'    => 'mstItemStores', // the method that defines the relationship in your Model
				'model'     => MstStore::class, // foreign key model
				'attribute' => 'name_en', // foreign key attribute that is shown to user
				'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?
				'select_all' => true, // show Select All and Clear buttons?
				'wrapperAttributes' => [
					'class' => 'form-group col-md-4',
				],
				'attributes' => [
                    'id' => 'store_id',
                    'onChange' => 'INVENTORY.multipleDependentStore(this)',
                ],
				'options' => (function ($query) {
					return $query->where('sup_org_id', backpack_user()->sup_org_id)->where('parent_id', null)->get();
				}),
			],
			[
                'name' => 'store_hidden_id',
                'type' => 'hidden',
                'attributes' => [
                    'id' => 'store_hidden_id',
                ],
            ],

			$this->addPlainHtml(),

            [    // Select2Multiple = n-n relationship (with pivot table)
				'label'     => "Stores",
				'type'      => 'select2_multiple',
				'name'      => 'mstItemStores', // the method that defines the relationship in your Model
                // optional
                'entity'    => 'mstItemStores', // the method that defines the relationship in your Model
                'model'     => MstStore::class, // foreign key model
                'attribute' => 'name_en', // foreign key attribute that is shown to user
                'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?
                'select_all' => true, // show Select All and Clear buttons?
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4',
                ],
                'options' => (function ($query) {
                    return $query->where('sup_org_id', backpack_user()->sup_org_id)->get();
                }),
            ],

			[
                'label'                => "Sub Store", // Table column heading
                'type'                 => 'select2_from_ajax_multiple',
                'name'                 => 'manySubStoresEntity', // the column that contains the ID of that connected entity;
                'entity'               => 'manySubStoresEntity', // the method that defines the relationship in your Model
                'attribute'            => 'name_en', // foreign key attribute that is shown to user
                'data_source'          => url('admin/api/childstore'), // url to controller search function (with /{id} should return model)
                'include_all_form_fields' => true, //sends the other form fields along with the request so it can be filtered.
                'minimum_input_length' => 0, // minimum characters to type before querying results
                'dependencies'         => ['store_id'], // when a dependency changes, this select2 is reset to null
                // 'method'                    => 'GET', // optional - HTTP method to use for the AJAX call (GET, POST)
                "allows_multiple" => true,
                'attributes' => [
                    'id' => 'store_id',
                    'placeholder' => 'Select Store First', // placeholder for the select
                ],
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4',
                ],
            ],
			[
				'name' => 'name',
				'type' => 'text',
				'label' => 'Model Name',
				'wrapperAttributes' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'description',
				'type' => 'text',
				'label' => trans('common.description'),
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'barcode_details',
				'type' => 'text',
				'label' => trans('common.barcode_details'),
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
                'name'        => 'asset_type_id',
                'label'       => "Asset Type",
                'type'        => 'select2_from_array',
                'options'     => $this->mst_fixed_items_list(),
                'allows_null' => true,
                'wrapperAttributes' => [
					'class' => 'form-group col-md-4',
				],
			],
            [
                'name' => 'category_id',
                'type' => 'relationship',
                'label' => trans('common.category_id'),
                'entity' => 'category',
                'attribute' => 'name_en',
                'model' => MstCategory::class,
				'data_source' => backpack_url('/mst-item/fetch/mst-category'),
                'inline_create' => [
                    'entity'=>'mst-category',
                    'modal_class' => 'modal-dialog modal-xl',
                ],
                'ajax' => true,
                'minimum_input_length' => 0,
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
				'options' => (function ($query) {
					return $query->where('sup_org_id', backpack_user()->sup_org_id)->get();
				}),
            ],

			[
				'label'     => trans('common.subcategory_id'),
				'type'      => 'relationship',
				'method' => 'GET',
				'name'      => 'subcategory_id', // the column that contains the ID of that connected entity;
				'model'     => MstSubcategory::class,
				'entity'    => 'mstSubCategory', // the method that defines the relationship in your Model
				'attribute' => 'name_en', // foreign key attribute that is shown to user
				'data_source' => url("admin/api/mstsubcategory/category_id"), //api/modelsmallname/tableid from which state is taken
				'inline_create' => [
					'entity'=>'mst-subcategory',
                    'modal_class' => 'modal-dialog modal-xl',
				],
				'minimum_input_length' => 0,
				'dependencies' => ["category_id"],
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
				'attributes' => [
					'placeholder' => 'Select Category first',
				]
			],
            [    // Select2Multiple = n-n relationship (with pivot table)
				'label'     => "Department",
				'type'      => 'select2',
				'name'      => 'department_id', // the method that defines the relationship in your Model

				// optional
				'entity'    => 'parentDepartment', // the method that defines the relationship in your Model
				'model'     => MstDepartment::class, // foreign key model
				'attribute' => 'name_en', // foreign key attribute that is shown to user
				'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?
				'select_all' => true, // show Select All and Clear buttons?
				'wrapperAttributes' => [
					'class' => 'form-group col-md-4',
				],
				'options' => (function ($query) {
					return $query->whereNull('parent_id')->get();
				}),
			],
			[
				'label'     => 'Sub Department',
				'type'      => 'select2_from_ajax',
				'method' => 'GET',
				'name'      => 'sub_department_id', // the column that contains the ID of that connected entity;
				'model'     => MstDepartment::class,
				'entity'    => 'parentDepartment', // the method that defines the relationship in your Model
				'attribute' => 'name_en', // foreign key attribute that is shown to user
				'data_source' => url("admin/api/department/department_id"), //api/modelsmallname/tableid from which state is taken
				'minimum_input_length' => 0,
				'dependencies' => ["department_id"],
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
				'attributes' => [
					'placeholder' => 'Select Department first',
				]
			],
            [
				'name' => 'item_price',
				'type' => 'number',
				'label' => 'Item Price',
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'label'     => 'Brand',
				'type'      => 'select2',
				'name'      => 'brand_id', // the column that contains the ID of that connected entity;
				'entity'    => 'brand', // the method that defines the relationship in your Model
				'model'     => MstBrand::class,
				'attribute' => 'name_en', // foreign key attribute that is shown to user
				'options' => (function ($query) {
					return $query->where('sup_org_id', backpack_user()->sup_org_id)->get();
				}),
				'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
			],
			[
                'name' => 'unit_id',
                'type' => 'relationship',
                'label' => trans('common.unit_id'),
                'entity' => 'mstUnitEntity',
                'attribute' => 'name_en',
                'model' => MstUnit::class,
                'inline_create' => [
                    'entity'=>'mst-unit',
                    'modal_class' => 'modal-dialog modal-xl',
                ],
                'data_source' => backpack_url('/mst-item/fetch/mst-unit'),
                'ajax' => true,
                'minimum_input_length' => 0,
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attributes' => [
                    'id' => 'unit_id'
                ],
                'options' => (function ($query){
                    return $query->where('sup_org_id',backpack_user()->sup_org_id)->get();
                }),
            ],
            [
                'name' => 'supplier_id',
                'type' => 'relationship',
                'label' => trans('common.supplier_id'),
                'entity' => 'mstSupplierEntity',
                'attribute' => 'name_en',
                'model' => MstSupplier::class,
                'inline_create' => [
                    'entity'=>'mst-supplier',
                    'modal_class' => 'modal-dialog modal-xl',
                ],
                'data_source' => backpack_url('/mst-item/fetch/mst-supplier'),
                'ajax' => true,
                'minimum_input_length' => 0,
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'options' => (function ($query){
                    return $query->where('sup_org_id',backpack_user()->sup_org_id)->get();
                }),
            ],
			[
				'name' => 'stock_alert_minimum',
				'type' => 'number',
				'label' => trans('common.stock_alert_minimum'),
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
			],
			[
				'name' => 'tax_vat',
				'type' => 'number',
				'label' => trans('common.tax_vat'),
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
				'attributes' => [
					'id' => 'tax_vat',
					// 'onKeyup' => 'INVENTORY.fetchSalesReceipt()',
					// 'step' => 'any'
				],
			],
			[
                'name' => 'discount_mode_id',
                'type' => 'relationship',
                'label' => trans('common.discount_mode_id'),
                'entity' => 'mstDiscModeEntity',
                'attribute' => 'name_en',
                'model' => MstDiscMode::class,
                'inline_create' => [
                    'entity'=>'mst-disc-mode',
                    'modal_class' => 'modal-dialog modal-xl',
                ],
                'data_source' => backpack_url('/mst-item/fetch/mst-disc-mode'),
                'ajax' => true,
                'minimum_input_length' => 0,
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'options' => (function ($query){
                    return $query->where('sup_org_id',backpack_user()->sup_org_id)->get();
                }),
            ],

			[
				'name' => 'is_damaged',
				'label' => trans('common.is_damaged'),
				'type' => 'radio',
				'default' => 0,
				'inline' => true,
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
				'options' =>
				[
					1 => 'Yes',
					0 => 'No',
				],
			],
			[
				'name' => 'is_taxable',
				'label' => trans('common.is_taxable'),
				'type' => 'radio',
				'default' => 1,
				'inline' => true,
				'options' =>
				[
					1 => 'Yes',
					0 => 'No',
				],
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
				'attributes' => [
					'id' => 'is_taxable',
					'onChange' => 'INVENTORY.setIsTaxableField()',
				],
			],
			[
				'name' => 'is_nonclaimable',
				'label' => trans('common.is_nonclaimable'),
				'type' => 'radio',
				'default' => 0,
				'inline' => true,
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
				'options' =>
				[
					1 => 'Yes',
					0 => 'No',
				],
			],
			[
				'name' => 'is_staffdiscount',
				'label' => trans('common.is_staffdiscount'),
				'type' => 'radio',
				'default' => 0,
				'inline' => true,
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
				'options' =>
				[
					1 => 'Yes',
					0 => 'No',
				],
			],
			[
				'name' => 'is_price_editable',
				'label' => 'Is price Editable?',
				'type' => 'radio',
				'default' => 0,
				'inline' => true,
				'wrapper' => [
					'class' => 'form-group col-md-4',
				],
				'options' =>
				[
					1 => 'Yes',
					0 => 'No',
				],
			],
			$this->addIsActiveField(),
            $this->addIsSuperDataField(),
            [
				'name' => 'custom-fields',
                'type' => 'view',
                'view' => 'customAdmin/items/form'
			],
		];
		if ($this->multipleBarcode) {
			array_splice($fields, 6, 1);
		}
		$this->crud->addFields(array_filter($fields));

		/**
		 * Fields can be defined using the fluent syntax or array syntax:
		 * - CRUD::field('price')->type('number');
		 * - CRUD::addField(['name' => 'price', 'type' => 'number']));
		 */
	}

    // public function create(){
    //     $this->crud->hasAccessOrFail('create');
    //     // prepare the fields you need to show
    //     $this->data['crud'] = $this->crud;
    //     $this->data['saveAction'] = $this->crud->getSaveAction();
    //     $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.add').' '.$this->crud->entity_name;
    //     $this->data['accounting_material'] = $this->accounting_material();
    //     $this->data['amount_bill_sundry_fed'] = $this->amount_bill_sundry_fed();
    //     $this->data['bill_sundry_percentage_of'] = $this->bill_sundry_percentage_of();
    //     $this->data['bill_sundry_calculated_on'] = $this->bill_sundry_calculated_on();
    //     return view('customAdmin.items.form', $this->data);
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

	protected function setupShowOperation()
	{
		$arr = [
			$this->addCodeColumn(),
			$this->addSuperOrganizationColumn(),

			[    // Select2Multiple = n-n relationship (with pivot table)
				'label'     => "Stores",
				'type'      => 'select',
				'name'      => 'mstItemStores', // the method that defines the relationship in your Model

				// optional
				'entity'    => 'mstItemStores', // the method that defines the relationship in your Model
				'model'     => MstStore::class, // foreign key model
				'attribute' => 'name_en', // foreign key attribute that is shown to user
				'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?
				'options' => (function ($query) {
					return $query->where('sup_org_id', backpack_user()->sup_org_id)->get();
				}),
			],
			[
				'name' => 'name',
				'type' => 'text',
				'label' => trans('common.name'),
			],
			[
				'name' => 'description',
				'type' => 'text',
				'label' => trans('common.description'),
			],
			[
				'name' => 'barcode_details',
				'type' => 'text',
				'label' => trans('common.barcode_details'),
			],

			[
				'label'     => trans('common.category_id'),
				'type'      => 'select',
				'name'      => 'category_id', // the column that contains the ID of that connected entity;
				'entity'    => 'category', // the method that defines the relationship in your Model
				'attribute' => 'name_en', // foreign key attribute that is shown to user
				'model'     => MstCategory::class,
				'options' => (function ($query) {
					return $query->where('sup_org_id', backpack_user()->sup_org_id)->get();
				}),
			],
			[
				'label'     => trans('common.subcategory_id'),
				'type'      => 'select',
				'name'      => 'subcategory_id', // the column that contains the ID of that connected entity;
				'model'     => MstSubcategory::class,
				'entity'    => 'mstSubCategory', // the method that defines the relationship in your Model
				'attribute' => 'name_en', // foreign key attribute that is shown to user
			],
			[
				'name' => 'item_price',
				'type' => 'number',
				'label' => 'Item Price',
			],

			[
				'label'     => trans('common.brand_id'),
				'type'      => 'select',
				'name'      => 'brand_id', // the column that contains the ID of that connected entity;
				'entity'    => 'mstBrandEntity', // the method that defines the relationship in your Model
				'attribute' => 'name_en', // foreign key attribute that is shown to user
				'model'     => MstBrand::class,
				'options' => (function ($query) {
					return $query->where('sup_org_id', backpack_user()->sup_org_id)->get();
				}),
			],
			[
				'label'     => trans('common.unit_id'),
				'type'      => 'select',
				'name'      => 'unit_id', // the column that contains the ID of that connected entity;
				'entity'    => 'mstUnitEntity', // the method that defines the relationship in your Model
				'attribute' => 'name_en', // foreign key attribute that is shown to user
				'model'     => MstUnit::class,
				'options' => (function ($query) {
					return $query->where('sup_org_id', backpack_user()->sup_org_id)->get();
				}),
			],
			[
				'label'     => trans('common.supplier_id'),
				'type'      => 'select',
				'name'      => 'supplier_id', // the column that contains the ID of that connected entity;
				'entity'    => 'mstSupplierEntity', // the method that defines the relationship in your Model
				'attribute' => 'name_en', // foreign key attribute that is shown to user
				'model'     => MstSupplier::class,
				'options' => (function ($query) {
					return $query->where('sup_org_id', backpack_user()->sup_org_id)->get();
				}),
			],
			[
				'name' => 'stock_alert_minimum',
				'type' => 'number',
				'label' => trans('common.stock_alert_minimum'),
			],
			[
				'name' => 'tax_vat',
				'type' => 'number',
				'label' => trans('common.tax_vat'),
				'default' => 0,
				'attributes' => [
					'readonly'    => 'readonly',
				],
			],

			[
				'label'     => trans('common.discount_mode_id'),
				'type'      => 'select',
				'name'      => 'discount_mode_id', // the column that contains the ID of that connected entity;
				'entity'    => 'mstDiscModeEntity', // the method that defines the relationship in your Model
				'attribute' => 'name_en', // foreign key attribute that is shown to user
				'model'     => MstDiscMode::class,
			],
			[
				'name' => 'is_damaged',
				'label' => trans('common.is_damaged'),
				'type' => 'radio_show',
				'options' =>
				[
					1 => 'Yes',
					0 => 'No',
				],
			],
			[
				'name' => 'is_taxable',
				'label' => trans('common.is_taxable'),
				'type' => 'radio_show',
				'options' =>
				[
					1 => 'Yes',
					0 => 'No',
				],
			],

			[
				'name' => 'is_nonclaimable',
				'label' => trans('common.is_nonclaimable'),
				'type' => 'radio_show',
				'options' =>
				[
					1 => 'Yes',
					0 => 'No',
				],
			],

			[
				'name' => 'is_staffdiscount',
				'label' => trans('common.is_staffdiscount'),
				'type' => 'radio_show',
				'options' =>
				[
					1 => 'Yes',
					0 => 'No',
				],
			],
			[
				'name' => 'is_price_editable',
				'label' => 'Is price Editable?',
				'type' => 'radio_show',
				'options' =>
				[
					1 => 'Yes',
					0 => 'No',
				],
			],
			[
				'name' => 'is_active',
				'label' => 'Is Active ?',
				'type' => 'radio_show',
				'options' =>
				[
					1 => 'Yes',
					0 => 'No',
				],
			],

			$this->addIsActiveField()
		];

		$this->crud->addColumns(array_filter($arr));
	}

        /**
     * Store a newly created resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->crud->hasAccessOrFail('create');
        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();
        // dd($request->sales_account_sales);
        // dd($request->except(['_save_action', '_http_referrer', '_token']), $this->crud->getStrippedSaveRequest($request));
        // register any Model Events defined on fields
        $this->crud->registerFieldEvents();
        // insert item in the db
        $item = $this->crud->create($request->except(['_save_action', '_http_referrer', '_token']));
        $this->data['entry'] = $this->crud->entry = $item;
        if($request->code){
            $item->code = $request->code;
            $item->save();
        }
        // dd($item->sales_account_sales);
        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();
        // save the redirect choice for next time
        $this->crud->setSaveAction();
        return $this->crud->performSaveAction($item->getKey());
    }

	public function itemEntriesExcelImport(Request $request)
	{

		$total_errors = [];
		$validator = Validator::make($request->all(), [
			'itemExcelFileName' => 'required',
		]);

		try {
			$itemImport = new ItemEntriesExcelImport;
			Excel::import($itemImport, request()->file('itemExcelFileName'));

			//!! Error for name doesnot exists
			if (!empty($itemImport->name_errors)) {

				array_push($total_errors, $itemImport->name_errors);
			}

			//!! Error for items with same name that already exixts
			if (!empty($itemImport->item_errors)) {
				array_push($total_errors, $itemImport->item_errors);
			}

			if (!empty($total_errors)) {
				return view('excel-errors', compact('total_errors'));
			}

            return 1;
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
			//!! Databse validation Errors
            $database_validation_errors = $e->failures();
            return view('excel-errors', compact('database_validation_errors'));
        }
	}
}
