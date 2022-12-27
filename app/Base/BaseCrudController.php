<?php

namespace App\Base;

use App\Models\MstItem;
use App\Models\MstStore;
use App\Models\MstCountry;
use App\Models\MstDistrict;
use App\Models\MstProvince;
use App\Models\GeneralLedger;
use App\Models\MstFiscalYear;
use App\Base\Traits\ParentData;
use App\Models\SupOrganization;
use App\Base\Traits\FilterStore;
use App\Models\StockItemDetails;
use App\Base\Traits\BarCodeSession;
use App\Base\Traits\CheckPermission;
use App\Base\Traits\MasterArrayData;
use App\Base\Traits\UserLevelFilter;
use App\Base\Operations\ListOperation;
use App\Base\Operations\ShowOperation;
use App\Base\Traits\ActivityLogTraits;
use App\Base\Operations\FetchOperation;
use App\Base\Operations\CreateOperation;
use App\Base\Operations\DeleteOperation;
use App\Base\Operations\UpdateOperation;
use Backpack\CRUD\app\Http\Controllers\CrudController;

class BaseCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;
    use ParentData;
    use CheckPermission;
    use FilterStore;
    use UserLevelFilter;
    use BarCodeSession;

    //For purchase/sales type master data
    use MasterArrayData;

    use ActivityLogTraits;


    protected $activity = ['index','create','edit','update','store','show','destroy'];

    public function __construct()
    {


        if ($this->crud) {
            $this->enableDialog(false);
            return;
        }

        $this->middleware(function ($request, $next) {
            $this->crud = app()->make('crud');
            // ensure crud has the latest request
            $this->crud->setRequest($request);
            $this->request = $request;
            $this->setupDefaults();
            $this->setup();
            $this->setLogs();
            $this->isAllowed(['show' => 'list']);
            $this->setupConfigurationForCurrentOperation();
            return $next($request);
        });
    }

    //Items list for stock entry
    public function getItemList($conditions = [])
    {


        $filtered_items=[];
        // dd(backpack_user()->user_level);
        switch (backpack_user()->user_level) {
            case config('users.user_level.organization_user'):
            //    return  $items=MstItem::where('is_active', 'true')->where('sup_org_id', backpack_user()->sup_org_id)->get(['id','code','name'])->unique('id');
            return  $items=MstItem::where(['is_active'=> 'true', 'sup_org_id' => backpack_user()->sup_org_id, 'is_fixed_asset' => 'false'])
                ->get(['id','code','name'])->unique('id');

                foreach($items as $item){
                    $filtered_items=[
                        'id'=>$item->id,
                        'code'=>$item->code,
                        'name'=>$item->name
                        ];
                }
            return $filtered_items;
            case config('users.user_level.store_user'):
                 $items= MstStore::find(backpack_user()->store_id)->where('sup_org_id', backpack_user()->sup_org_id)->select('id')->with(['itemEntity'=>function($q){
                    $q->select('mst_items.id','mst_items.code','mst_items.name')->where( 'mst_items.is_fixed_asset','false');
                }])->where('is_active', 'true')->get()->unique('mst_items.id');

                foreach($items as $key=>$item){
                    $test=$items[$key]->itemEntity;
                    foreach($test as $t){
                        array_push($filtered_items,[
                            'id'=>$t->id,
                            'code'=>$t->code,
                            'name'=>$t->name
                            ]);
                    }

                }
                return $filtered_items;
            case config('users.user_level.store_admin'):
                 $items= MstStore::find(backpack_user()->store_id)->where('sup_org_id', backpack_user()->sup_org_id)->select('id')->with(['itemEntity'=>function($q){
                    $q->select('mst_items.id','mst_items.code','mst_items.name')->where( 'mst_items.is_fixed_asset','false');
                }])->where('is_active', 'true')->get()->unique('mst_items.id');

                foreach($items as $key=>$item){
                    $test=$items[$key]->itemEntity;
                    foreach($test as $t){
                        array_push($filtered_items,[
                            'id'=>$t->id,
                            'code'=>$t->code,
                            'name'=>$t->name
                            ]);
                    }
                }
                return $filtered_items;
            default:
                $items= MstItem::where(['is_active' => 'true', 'is_fixed_asset' => 'false'])->get();
                foreach($items as $item){

                    array_push($filtered_items, [
                        'id' => $item->id,
                        'code' => $item->code,
                        'name' => $item->name,
                        'qty' => $item->itemQtyDetail->item_qty ?? 0
                    ]);
                }

                return $filtered_items;
        }
    }

    //Added is fixed asset clause false for items (Items for stock entry)
    public function getFixedAssetItemList($conditions = [])
    {
        $filtered_items=[];
        // dd(backpack_user()->user_level);
        switch (backpack_user()->user_level) {
            case config('users.user_level.organization_user'):
                return  $items=MstItem::where(['is_active'=> 'true', 'sup_org_id' => backpack_user()->sup_org_id, 'is_fixed_asset' => 'true'])
                ->get(['id','code','name'])->unique('id');

                foreach($items as $item){
                    $filtered_items=[
                        'id'=>$item->id,
                        'code'=>$item->code,
                        'name'=>$item->name
                        ];
                }
            return $filtered_items;
            case config('users.user_level.store_user'):
                    $items= MstStore::find(backpack_user()->store_id)->where('sup_org_id', backpack_user()->sup_org_id)->select('id')->with(['itemEntity'=>function($q){
                    $q->select('mst_items.id','mst_items.code','mst_items.name')->where( 'mst_items.is_fixed_asset','true');
                }])->where('is_active', 'true')->get()->unique('mst_items.id');

                foreach($items as $key=>$item){
                    $test=$items[$key]->itemEntity;
                    foreach($test as $t){
                        array_push($filtered_items,[
                            'id'=>$t->id,
                            'code'=>$t->code,
                            'name'=>$t->name
                            ]);
                    }

                }
                return $filtered_items;
            case config('users.user_level.store_admin'):
                    $items= MstStore::find(backpack_user()->store_id)->where('sup_org_id', backpack_user()->sup_org_id)->select('id')->with(['itemEntity'=>function($q){
                    $q->select('mst_items.id','mst_items.code','mst_items.name')->where( 'mst_items.is_fixed_asset','true');
                }])->where('is_active', 'true')->get()->unique('mst_items.id');

                foreach($items as $key=>$item){
                    $test=$items[$key]->itemEntity;
                    foreach($test as $t){
                        array_push($filtered_items,[
                            'id'=>$t->id,
                            'code'=>$t->code,
                            'name'=>$t->name
                            ]);
                    }
                }
                return $filtered_items;
            default:
                $items= MstItem::where(['is_active' => 'true', 'is_fixed_asset' =>'true'])->get();
                foreach($items as $item){

                    array_push($filtered_items, [
                        'id' => $item->id,
                        'code' => $item->code,
                        'name' => $item->name,
                        'qty' => $item->itemQtyDetail->item_qty ?? 0
                    ]);
                }

                return $filtered_items;
        }
    }

    //Items only in stock entry
    public function getStockEntryItemsList($conditions = [])
    {
        $filtered_items=[];
        switch (backpack_user()->user_level) {
            case config('users.user_level.organization_user'):
                $items = StockItemDetails::where(['is_active'=> 'true', 'sup_org_id' => backpack_user()->sup_org_id])->distinct()->pluck('item_id');
                foreach($items as $indItem){
                    $item = MstItem::find($indItem);
                    array_push($filtered_items,[
                        'id'=>$item->id,
                        'code'=>$item->code,
                        'name'=>$item->name
                    ]);
                }
            return $filtered_items;
            case config('users.user_level.store_user'):
                    $items= MstStore::find(backpack_user()->store_id)->where('sup_org_id', backpack_user()->sup_org_id)->select('id')->with(['itemEntity'=>function($q){
                    $q->select('mst_items.id','mst_items.code','mst_items.name')->where( 'mst_items.is_fixed_asset','true');
                }])->where('is_active', 'true')->get()->unique('mst_items.id');

                foreach($items as $key=>$item){
                    $test=$items[$key]->itemEntity;
                    foreach($test as $t){
                        array_push($filtered_items,[
                            'id'=>$t->id,
                            'code'=>$t->code,
                            'name'=>$t->name
                            ]);
                    }
                }
                return $filtered_items;
            case config('users.user_level.store_admin'):
                    $items= MstStore::find(backpack_user()->store_id)->where('sup_org_id', backpack_user()->sup_org_id)->select('id')->with(['itemEntity'=>function($q){
                    $q->select('mst_items.id','mst_items.code','mst_items.name')->where( 'mst_items.is_fixed_asset','true');
                }])->where('is_active', 'true')->get()->unique('mst_items.id');

                foreach($items as $key=>$item){
                    $test=$items[$key]->itemEntity;
                    foreach($test as $t){
                        array_push($filtered_items,[
                            'id'=>$t->id,
                            'code'=>$t->code,
                            'name'=>$t->name
                            ]);
                    }

                }
                // dd($filtered_items);
                return $filtered_items;
            default:
                // $items= MstItem::where(['is_active' => 'true', 'is_fixed_asset' =>'true'])->get();
                $items = StockItemDetails::where(['is_active'=> 'true', 'sup_org_id' => backpack_user()->sup_org_id])->pluck('id');
                foreach($items as $item){

                    array_push($filtered_items, [
                        'id' => $item->id,
                        'code' => $item->code,
                        'name' => $item->name,
                        'qty' => $item->itemQtyDetail->item_qty ?? 0
                    ]);
                }

                return $filtered_items;
        }
    }

    public function getLedgerList($conditions = [])
    {
        $filtered_items=[];
        switch (backpack_user()->user_level) {
            case config('users.user_level.organization_user'):
            //    return  $items=GeneralLedger::where('is_active', 'true')->where('sup_org_id', backpack_user()->sup_org_id)->get(['id','code','name']);
               return  $items=GeneralLedger::where('is_active', 'true')->get(['id','gl_code','name']);

                foreach($items as $item){
                    $filtered_items=[
                        'id'=>$item->id,
                        'code'=>$item->gl_code,
                        'name'=>$item->name
                        ];
                }
            return $filtered_items;
            case config('users.user_level.store_user'):
                 $items= MstStore::find(backpack_user()->store_id)->where('sup_org_id', backpack_user()->sup_org_id)->select('id')->with(['itemEntity'=>function($q){
                    $q->select('general_ledgers.id','general_ledgers.gl_code','general_ledgers.name');
                }])->where('is_active', 'true')->get();

                foreach($items as $key=>$item){
                    $test=$items[$key]->itemEntity;
                    foreach($test as $t){
                        array_push($filtered_items,[
                            'id'=>$t->id,
                            'code'=>$t->gl_code,
                            'name'=>$t->name
                            ]);
                    }

                }
                return $filtered_items;
            default:
                $items= GeneralLedger::where('is_active','true')->get();
                foreach($items as $item){
                    // dd($item->itemQtyDetail->item_qty??0);

                   array_push($filtered_items,[
                    'id'=>$item->id,
                    'code'=>$item->gl_code,
                    'name'=>$item->name,
                    // 'qty'=>$item->itemQtyDetail->item_qty??0
                ]);
                }
                // dd($filtered_items);
                return $filtered_items;
        }
    }

    //common fields
    protected function addSuperOrgField()
    {
        // dd(backpack_user()->isSystemUser());
        if (backpack_user()->isSystemUser()) {
            return [  // Select
                'label'     => trans('common.sup_org_id'),
                'type' => 'select2',
                'name' => 'sup_org_id',
                'method' => 'GET',
                'entity' => 'superOrganizationEntity',
                'attribute' => 'name_en',
                'options'   => (function ($query) {
                    return (new SupOrganization())->getFieldComboOptions($query);
                }),
                'model' => SupOrganization::class,
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attributes' => [
                    'required' => 'required'
                ],
            ];
        } else {
            return [
                'name' => 'sup_org_id',
                'type' => 'hidden',
                'value' => backpack_user()->sup_org_id,
            ];
        }
    }
    protected function addStoreField()
    {
        // dd(backpack_user()->isSystemUser());
        if (backpack_user()->isSystemUser()) {
            return [  // Select
                'label'     => trans('common.store_id'),
                'type' => 'select2',
                'name' => 'store_id',
                'method' => 'GET',
                'entity' => 'mstStoreEntity',
                'attribute' => 'name_en',
                'options'   => (function ($query) {
                    return (new MstStore())->getFieldComboOptions($query);
                }),
                'model' => MstStore::class,
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attributes' => [
                    'required' => 'required'
                ],
            ];
        } else {
            return [
                'name' => 'store_id',
                'type' => 'hidden',
                'value' => backpack_user()->store_id,
            ];
        }
    }


    protected function addCodeField()
    {
        return [
            'name' => 'code',
            'label' => trans('common.code'),
            'type' => 'text',
            'wrapper' => [
                'class' => 'form-group col-md-4',
            ],
        ];
    }

    protected function addReadOnlyCodeField()
    {
        return [
            'name' => 'code',
            'label' => trans('common.code'),
            'type' => 'text',
            'wrapper' => [
                'class' => 'form-group col-md-4',
            ],
            'attributes' => [
                'id' => 'code',
                'readonly' => true,
            ],
        ];
    }

    protected function addPlainHtml()
    {
        return   [
            'type' => 'custom_html',
            'name' => 'plain_html_1',
            'value' => '<br>',
        ];
    }

    protected function addNameEnField()
    {
        return [
            'name' => 'name_en',
            'label' => trans('common.name_en'),
            'type' => 'text',
            'attributes' => [
                'id' => 'name-en',
                'required' => 'required',
                'max-lenght' => 200,
            ],
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
        ];
    }

    protected function addNameLcField()
    {
        return [
            'name' => 'name_lc',
            'label' => trans('common.name_lc'),
            'type' => 'text',
            'attributes' => [
                'id' => 'name-lc',
                // 'required' => 'required',
                'max-lenght' => 200,
            ],
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
        ];
    }

    protected function addFiscalYearField()
    {
        return[
            'name' => 'fiscal_year_id',
            'type' => 'select2',
            'entity'=>'fiscalyearEntity',
            'attribute' => 'code',
            'model'=>MstFiscalYear::class,
            'label' => trans('common.fiscal_year'),
            'wrapper' => [
                'class' => 'form-group col-md-4',
            ],
            'attributes'=>[
                'required' => 'Required',
            ],
        ];
    }

    protected function addCountryField()
    {
        return [
            'name' => 'country_id',
            'type' => 'select2',
            'entity' => 'countryEntity',
            'attribute' => 'name_en',
            'model' => MstCountry::class,
            'label' => trans('common.country'),
            'options'   => (function ($query) {
                return (new MstCountry())->getFieldComboOptions($query);
            }),
            'wrapper' => [
                'class' => 'form-group col-md-4',
            ],
            'attributes' => [
                'required' => 'required',
            ],
        ];
    }
    protected function addProvinceField()
    {
        return [
            'name' => 'province_id',
            'type' => 'select2',
            'entity' => 'provinceEntity',
            'attribute' => 'name_en',
            'model' => MstProvince::class,
            'label' => trans('common.fed_province'),
            'options'   => (function ($query) {
                return (new MstProvince())->getFieldComboOptions($query);
            }),
            'wrapper' => [
                'class' => 'form-group col-md-4',
            ],
            'attributes' => [
                'required' => 'required',
            ],
        ];
    }

    protected function addDistrictField()
    {
        return  [
            'name' => 'district_id',
            'type' => 'select2',
            'entity' => 'districtEntity',
            'attribute' => 'name_en',
            'model' => MstDistrict::class,
            'label' => trans('common.fed_district'),
            'options'   => (function ($query) {
                return (new MstDistrict())->getFieldComboOptions($query);
            }),
            'wrapper' => [
                'class' => 'form-group col-md-4',
            ],
        ];
    }

    // protected function addLocalLevelField()
    // {
    //     return [
    //         'name' => 'local_level_id',
    //         'type' => 'select2',
    //         'entity'=>'localLevelEntity',
    //         'attribute' => 'name_lc',
    //         'model'=>MstFedLocalLevel::class,
    //         'label' => trans('common.fed_local_level'),
    //         'options'   => (function ($query) {
    //             return (new MstFedLocalLevel())->getFieldComboOptions($query);
    //                 }),
    //         'wrapper' => [
    //             'class' => 'form-group col-md-4',
    //         ],
    //     ];
    // }

    protected function addDateBsField()
    {
        return  [
            'name' => 'date_bs',
            'type' => 'nepali_date',
            'label' => trans('common.date_bs'),
            'attributes' => [
                'id' => 'date_bs',
                'relatedId' => 'date_ad',
                'maxlength' => '10',
            ],
            'wrapper' => [
                'class' => 'form-group col-md-4',
            ],
        ];
    }
    protected function addDateAdField()
    {
        return [
            'name' => 'date_ad',
            'type' => 'date',
            'label' => trans('common.date_ad'),
            'attributes' => [
                'id' => 'date_ad',
            ],
            'wrapper' => [
                'class' => 'form-group col-md-4',
            ],
        ];
    }

    protected function addRemarksField()
    {
        return [
            'name' => 'remarks',
            'label' => trans('common.remarks'),
            'type' => 'textarea',
            'wrapper' => [
                'class' => 'form-group col-md-12',
            ],
        ];
    }
    protected function addDescriptionField()
    {
        return [
            'name' => 'description',
            'label' => trans('common.description'),
            'type' => 'textarea',
            'wrapper' => [
                'class' => 'form-group col-md-12',
            ],
        ];
    }

    protected function addDescriptionColumn()
    {
        return [
            'name' => 'description',
            'label' => trans('common.description'),
            'type' => 'textarea',
        ];
    }



    public function addIsActiveField()
    {
        return [
            'name' => 'is_active',
            'label' => trans('common.is_active'),
            'type' => 'radio',
            'default' => 1,
            'inline' => true,
            'wrapper' => [
                'class' => 'form-group col-md-4',
            ],
            'options' =>
            [
                1 => 'Yes',
                0 => 'No',
            ],
        ];
    }

    public function addDisplayOrderField()
    {
        return [
            'name' => 'display_order',
            'type' => 'number',
            'label' => trans('common.display_order'),
            'default' => 0,
            'wrapper' => [
                'class' => 'form-group col-md-4',
            ],
        ];
    }
    // public function addSuperOrganizationField(){
    //     return [
    //         'label'     => trans('common.sup_org_id'),
    //         'type'      => 'select2',
    //         'name'      => 'sup_org_id', // the column that contains the ID of that connected entity;
    //         'entity'    => 'superOrganizationEntity', // the method that defines the relationship in your Model
    //         'attribute' => 'name_en', // foreign key attribute that is shown to user
    //         'model'     => SupOrganization::class,
    //         'wrapper' => [
    //             'class' => 'form-group col-md-4',
    //         ],
    //     ];
    // }

    //Purchase/Sales type Master Common Fields

    protected function addTaxTypeField(){
        return [   // radio
            'name'        => 'taxation_type', // the name of the db column
            'label'       => 'Taxation Type', // the input label
            'type'        => 'radio',
            'options'   => $this->tax_type(), //  you can use this to filter the results show in the select
            'inline' => true,
            'wrapper'   => [
                'class'      => 'form-group col-md-12'
            ],
        ];
    }

    protected function addTaxInvoiceField(){
        return [   // select_from_array
            'name'        => 'tax_invoice',
            'label'       => "Tax Invoice",
            'type'        => 'select2_from_array',
            'options'     => [true => 'Yes', false => 'No'],
            'allows_null' => false,
            'default'     => 'one',
            'wrapperAttributes'   => [
                'class'      => 'form-group col-md-4'
            ],
        ];
    }

    protected function addCapitalPurchaseField(){
        return [   // select_from_array
            'name'        => 'capital_purchase',
            'label'       => "Capital Purchase",
            'type'        => 'select2_from_array',
            'options'     => [true => 'Yes', false => 'No'],
            'allows_null' => false,
            'default'     => 'one',
            'wrapperAttributes'   => [
                'class'      => 'form-group col-md-4'
            ],
        ];
    }

    protected function addSkipVatField(){
        return [   // select_from_array
            'name'        => 'skip_vat',
            'label'       => "Skip in VAT Reports",
            'type'        => 'select2_from_array',
            'options'     => [true => 'Yes', false => 'No'],
            'allows_null' => false,
            'default'     => 'one',
            'wrapperAttributes'   => [
                'class'      => 'form-group col-md-4'
            ],
        ];
    }

    protected function addIssueStFormField(){
        return [   // select_from_array
            'name'        => 'issue_st_form',
            'label'       => "Issue ST Form",
            'type'        => 'select2_from_array',
            'options'     => [true => 'Yes', false => 'No'],
            'allows_null' => false,
            'default'     => 'one',
            'wrapperAttributes'   => [
                'class'      => 'form-group col-md-3'
            ],
        ];
    }

    protected function addFormIssuableField(){
        return [   // select_from_array
            'name'        => 'form_issubale',
            'label'       => "Form Issuable",
            'type'        => 'select2_from_array',
            'options'     => [true => 'Yes', false => 'No'],
            'allows_null' => false,
            'default'     => 'one',
            'wrapperAttributes'   => [
                'class'      => 'form-group col-md-3'
            ],
        ];
    }

    protected function addReceiveStFormField(){
        return [   // select_from_array
            'name'        => 'receive_st_form',
            'label'       => "Receive ST Form",
            'type'        => 'select2_from_array',
            'options'     => [true => 'Yes', false => 'No'],
            'allows_null' => false,
            'default'     => 'one',
            'wrapperAttributes'   => [
                'class'      => 'form-group col-md-3'
            ],
        ];
    }

    protected function addFormReceivableField(){
        return [   // select_from_array
            'name'        => 'form_receivable',
            'label'       => "Form Receivable",
            'type'        => 'select2_from_array',
            'options'     => [true => 'Yes', false => 'No'],
            'allows_null' => false,
            'default'     => 'one',
            'wrapperAttributes'   => [
                'class'      => 'form-group col-md-3'
            ],
        ];
    }

    protected function addTaxCalculationField(){
        return [   // Tax Calculation
            'name'        => 'tax_calculation', // the name of the db column
            'label'       => 'Tax Calculation', // the input label
            'type'        => 'radio',
            // optional - force the related options to be a custom query, instead of all();
            'options'   => $this->tax_calc(), //  you can use this to filter the results show in the select
            'wrapper'   => [
                'class'      => 'form-group col-md-12'
            ],
            'inline'      => true, // show the radios all on the same line?
        ];
    }
    protected function addTaxPercentageField(){
        return [   // Tax Calculation
            'name'        => 'tax_percent', // the name of the db column
            'label'       => 'Tax (In %)', // the input label
            'type'        => 'text',
            'wrapper'   => [
                'class'      => 'form-group col-md-4'
            ],
        ];
    }

    protected function addSurChargeField(){
        return [   // Tax Calculation
            'name'        => 'surcharge_percent', // the name of the db column
            'label'       => 'Surcharge (In %)', // the input label
            'type'        => 'text',
            'wrapper'   => [
                'class'      => 'form-group col-md-4'
            ],
        ];
    }

    protected function addCessChargeField(){
        return [   // Tax Calculation
            'name'        => 'cess_percent', // the name of the db column
            'label'       => 'Add. (Cess In %)', // the input label
            'type'        => 'text',
            'wrapper'   => [
                'class'      => 'form-group col-md-4'
            ],
        ];
    }

    protected function addRegionField(){
        return [   // radio
            'name'        => 'region', // the name of the db column
            'label'       => 'Region', // the input label
            'type'        => 'radio',
            'options'   => $this->region(), //  you can use this to filter the results show in the select
            'inline' => true,
            'attributes' => [
                'id' => 'region'
            ],
            'wrapper'   => [
                'class'      => 'form-group col-md-6'
            ],
        ];
    }

    //Is super data field according to users
    protected function addIsSuperDataField()
    {
        if (backpack_user()->isSystemUser()) {
            return [
                'type'      => 'hidden',
                'name'      => 'is_super_data',
                'value'     => 1
            ];
        } else {
            return [
                'type'      => 'hidden',
                'name'      => 'is_super_data',
                'value'     => 0
            ];
        }
    }


    // common columns

    protected function addRowNumberColumn()
    {
        return [
            'name' => 'row_number',
            'type' => 'row_number',
            'label' => trans('common.row_number'),
        ];
    }

    protected function addCodeColumn()
    {
        return [
            'name' => 'code',
            'label' => trans('common.code'),
            'type' => 'text',
        ];
    }


    protected function addNameEnColumn()
    {
        return [
            'name' => 'name_en',
            'label' => trans('common.name_en'),
            'type' => 'text',
        ];
    }

    protected function addNameLcColumn()
    {
        return [
            'name' => 'name_lc',
            'label' => trans('common.name_lc'),
            'type' => 'text',
        ];
    }

    protected function addFiscalYearColumn()
    {
        return[
            'name' => 'fiscal_year_id',
            'type' => 'select',
            'entity'=>'fiscalyearEntity',
            'attribute' => 'code',
            'model'=>MstFiscalYear::class,
            'label' => trans('common.fiscal_year'),
        ];
    }

    protected function addProvinceColumn()
    {
        return [
            'name' => 'province_id',
            'type' => 'select',
            'entity' => 'provinceEntity',
            'attribute' => 'name_en',
            'model' => MstProvince::class,
            'label' => trans('common.fed_province'),
        ];
    }


    protected function addSuperOrganizationColumn()
    {
        if (backpack_user()->isSystemUser()) {
            return [
                'label'     => trans('common.sup_org_id'),
                'type'      => 'select',
                'name'      => 'sup_org_id', // the column that contains the ID of that connected entity;
                'entity'    => 'superOrganizationEntity', // the method that defines the relationship in your Model
                'attribute' => 'name_en', // foreign key attribute that is shown to user
                'model'     => SupOrganization::class
            ];
        } else {
            return null;
        }
    }

    protected function addStoreColumn(){
        if(backpack_user()->isSystemUser()){
            return [
                'label' => trans('common.store_id'),
                'type' => 'select',
                'name' => 'store_id',
                'entity' => 'mstStoreEntity',
                'attribute' => 'name_en',
                'model' => MstStore::class,
            ];
        }else{
            return null;
        }
    }
    // protected function addDistrictColumn()
    // {
    //     return  [
    //         'name' => 'district_id',
    //         'type' => 'select',
    //         'entity'=>'districtEntity',
    //         'attribute' => 'name_lc',
    //         'model'=>MstFedDistrict::class,
    //         'label' => trans('common.fed_district'),
    //     ];
    // }

    // protected function addLocalLevelColumn()
    // {
    //     return [
    //         'name' => 'local_level_id',
    //         'type' => 'select',
    //         'entity'=>'localLevelEntity',
    //         'attribute' => 'name_lc',
    //         'model'=>MstFedLocalLevel::class,
    //         'label' => trans('common.fed_local_level'),
    //     ];
    // }

    protected function addDateBsColumn()
    {
        return  [
            'name' => 'date_bs',
            'type' => 'nepali_date',
            'label' => trans('common.date_bs'),
        ];
    }
    protected function addDateAdColumn()
    {
        return [
            'name' => 'date_ad',
            'type' => 'date',
            'label' => trans('common.date_ad'),
        ];
    }



    public function addIsActiveColumn()
    {
        return [
            'name' => 'is_active',
            'label' => trans('common.is_active'),
            'type' => 'radio',
            'options' =>
            [
                1 => 'Yes',
                0 => 'No',
            ],
        ];
    }

    public function addDisplayOrderColumn()
    {
        return [
            'name' => 'display_order',
            'type' => 'number',
            'label' => trans('common.display_order'),
        ];
    }

        //Sales/Purchase Type Master Columns

    protected function addTaxTypeColumn(){
        return [// select_from_array
            'name'    => 'taxation_type',
            'label'   => 'Taxation Type',
            'type'    => 'select_from_array',
            'options' => $this->tax_type(),
        ];
    }

    protected function addRegionColumn(){
        return [// select_from_array
            'name'    => 'region',
            'label'   => 'Region',
            'type'    => 'select_from_array',
            'options' => $this->region(),
        ];
    }

    protected function addTaxCalculationColumn(){
        return [// select_from_array
            'name'    => 'tax_calculation',
            'label'   => 'Tax Calculation',
            'type'    => 'select_from_array',
            'options' => $this->tax_calc(),
        ];
    }

    // public function addClientColumn(){
    //     if(backpack_user()->isClientUser())
    //     {
    //         return null;
    //     }
    //     else{
    //        return [  // Select
    //             'label' =>trans('common.client_id'),
    //             'type' => 'select',
    //             'name' => 'client_id',
    //             'entity' => 'clientEntity',
    //             'attribute' => 'name_lc',
    //             'model' => AppClient::class,
    //         ];
    //     }
    // }



    //common filters
    public function addNameFilter(){
        return $this->crud->addFilter(
            [
                'label' => trans('common.name_en'),
                'type' => 'text',
                'name' => 'name',
            ],
            false,
            function($value){
                $this->crud->addClause('where', 'name', 'iLIKE', '%' . $value . '%');
            }
        );
    }

    public function addNameEnFilter()
    {
        return $this->crud->addFilter(

            [
                'label' => trans('common.name_en'),
                'type' => 'text',
                'name' => 'name_en',
            ],
            false,
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'name_en', 'iLIKE', '%' . $value . '%');
            }
        );
    }

    public function addNameLcFilter()
    {
        return $this->crud->addFilter(

            [
                'label' => trans('common.name_lc'),
                'type' => 'text',
                'name' => 'name_lc',
            ],
            false,
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'name_lc', 'iLIKE', '%' . $value . '%');
            }
        );
    }

    public function addOrganizationFilter(){
        if(backpack_user()->isSystemUser()){
            return $this->crud->addFilter(
                [
                    'name' => 'sup_org_id',
                    'type' => 'select2',
                    'label' => trans('common.org_id'),
                    'placeholder' => 'Select Organization',
                ],
                function () {
                    return SupOrganization::pluck('name_en','id')->toArray();
                },
                function($value){
                    $this->crud->addClause('where', 'sup_org_id', $value);
                }
            );
        }
    }

    // public function addClientIdFilter()
    // {
    //     if(backpack_user()->isClientUser() === false)
    //     {
    //         return  $this->crud->addFilter(
    //             [
    //                 'name'        => 'client',
    //                 'type'        => 'select2',
    //                 'label'       => trans('common.client_id'),
    //                 'placeholder' => 'पहिला जिल्ला छान्नुहोस्',
    //             ],
    //             function () {
    //                 return (new AppClient())->getClientFilterComboOptions();
    //             },
    //             function ($value) { // if the filter is active
    //                 $this->crud->addClause('where', 'client_id', $value);
    //             }
    //         );
    //     }
    // }

    // public function addProvinceIdFilter()
    // {
    //     if(backpack_user()->isClientUser() === false)
    //     {
    //     return $this->crud->addFilter(
    //         [
    //             'label' => 'प्रदेश',
    //             'type' => 'select2',
    //             'name' => 'province_id', // the db column for the foreign key
    //             'placeholder' => 'प्रदेश छान्नुहोस्',
    //             'attributes' => [
    //                 'onChange'=>'TMPP.getDistrict(this)',
    //             ]
    //         ],
    //         function () {
    //             return (new MstFedProvince())->getProvinceFilterComboOptions();
    //         },
    //         function ($value) { // if the filter is active
    //             $data = $this->customFilterQuery();
    //             $datas = collect(DB::select($data));
    //             $client_ids = $datas->pluck('client_id')->toArray();
    //             $this->crud->query->whereIn('client_id', $client_ids);
    //         }
    //     );
    //     }
    // }

    // public function addDistrictIdFilter()
    // {
    //     if(backpack_user()->isClientUser() === false)
    //     {
    //     return $this->crud->addFilter(
    //         [
    //             'name'        => 'district_id',
    //             'type'        => 'select2',
    //             'label'       => 'जिल्ला',
    //             'placeholder' => 'पहिला प्रदेश छान्नुहोस्',
    //             'attributes' => [
    //                 'onChange'=>'TMPP.getFedLocalLevel(this)',
    //             ]
    //         ],
    //         function () {
    //         },
    //         function ($value) { // if the filter is active
    //             $data = $this->customFilterQuery();
    //             $datas = collect(DB::select($data));
    //             $client_ids = $datas->pluck('client_id')->toArray();
    //             $this->crud->query->whereIn('client_id', $client_ids);
    //         }
    //     );
    //     }
    // }

    // public function customFilterQuery()
    // {
    //     $province_id = request()->province_id;
    //     $district_id = request()->district_id;
    //     $table_name = $this->crud->model->getTable();
    //     $sql = "SELECT * from $table_name t
    //             inner join app_client ap on ap.id = t.client_id
    //             inner join mst_fed_local_level mfll on mfll.id = ap.fed_local_level_id
    //             inner join mst_fed_district mfd on mfd.id = mfll.district_id
    //             inner join mst_fed_province mp on mp.id = mfd.province_id
    //             where 1 = 1";

    //     $whereas = [];
    //     if($province_id)
    //     {
    //         $whereas[] = ' and mp.id =' . $province_id;
    //     }
    //     if($district_id){
    //         $whereas[] = 'and mfd.id =' . $district_id;
    //     }
    //     $where_clause = implode(" " ,$whereas);
    //     $sql .= $where_clause;
    //     return $sql;
    // }


}
