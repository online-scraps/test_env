<?php

namespace App\Http\Controllers\Admin;

use App\Models\MenuItem;
use App\Base\BaseCrudController;
use App\Models\RoleHasMenuAccess;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;
use App\Http\Requests\MenuItemRequest;
use App\Base\Operations\ReorderOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class MenuItemCrudController extends BaseCrudController
{
    use ReorderOperation;

    public function setup()
    {
        CRUD::setModel(MenuItem::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/menu-item');
        CRUD::setEntityNameStrings('', trans('menuitem.title_text'));
        $this->crud->enableReorder('name_lc', 2);
        $this->isAllowed();
        $this->data['script_js'] = $this->getScripts();

    }

    public function getScripts()
    {
        return "
            let mode = '{$this->crud->getActionMethod()}';
            if(mode == 'create'){
                $('#name-lc').prop('readonly', false);
                $('#name-en').prop('readonly', false);
            }else{
                $('#name-lc').prop('readonly', true);
                $('#name-en').prop('readonly', true);
            }
        ";
    }

    public function setupListOperation()
    {
        $col = [
            $this->addRowNumberColumn(),
            $this->addNameEnColumn(),
            [
                'name' => 'model_name',
                'label' => trans('menuitem.model_name'),
            ],
            [
                'name' => 'display_name',
                'label' => trans('menuitem.display_name'),
            ],
            [
                'label' => trans('menuitem.parent'),
                'type' => 'select',
                'name' => 'parent_id',
                'entity' => 'parent',
                'attribute' => 'name_lc',
                'model' => MenuItem::class,
            ],
            // [
            //     'label' => trans('menuitem.parent'),
            //     'type' => 'select',
            //     'name' => 'parent_id',
            //     'entity' => 'parent',
            //     'attribute' => 'name_lc',
            //     'model' => MenuItem::class,
            //     'options'   => (function ($query) {
            //         return $query->where('depth','<=' ,'3')->orWhere('depth','=',NULL)->get();
            //     }),
            // ],
            // [
            //     'name' => 'link',
            //     'label' => trans('menuitem.link'),
            // ],
            // $this->addSuperOrganizationColumn(),

        ];
        $this->crud->addColumns(array_filter($col));
        $this->crud->removeButtons(['create', 'delete']);
        // $this->crud->addClause('where');
    }

    public function setupCreateOperation()
    {
        CRUD::setValidation(MenuItemRequest::class);

        $arr = [
            $this->addNameLcField(),
            $this->addNameEnField(),
            [
                'label' => trans('menuitem.parent'),
                'type' => 'select',
                'name' => 'parent_id',
                'entity' => 'parent',
                'attribute' => 'name_lc',
                'model' => MenuItem::class,
                'options'   => (function ($query) {
                    return $query->where('depth', '<=', '3')->orWhere('depth', '=', NULL)->get();
                }),
            ],
            [   // icon_picker
                'label'   => "Icon",
                'name'    => 'icon_picker',
                'type'    => 'icon_picker',
                'iconset' => 'fontawesome', // options: fontawesome, lineawesome, glyphicon, ionicon, weathericon, mapicon, octicon, typicon, elusiveicon, materialdesign

            ],
            [
                'name' => 'display_name',
                'type' => 'text',
                'label' => 'Display Name',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4'
                ]
            ],
            // [
            //     'name' => ['type', 'link'],
            //     'label' => trans('menuitem.type'),
            //     'type' => 'page_or_link',
            // ],
            // $this->addSuperOrgField(),

        ];
        $this->crud->addFields(array_filter($arr));
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function reorder()
    {
        $this->crud->hasAccessOrFail('reorder');

        if (!$this->crud->isReorderEnabled()) {
            abort(403, 'Reorder is disabled.');
        }

        $menus = $this->getMenuItems();

        // get all results for that entity
        if (backpack_user()->hasRole('superadmin')) {
            $this->data['entries'] = $this->crud->getEntries();
        } else {
            $this->data['entries'] = $menus;
        }

        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.reorder') . ' ' . $this->crud->entity_name;

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getReorderView(), $this->data);
    }

    public function saveReorder()
    {
        $this->crud->hasAccessOrFail('reorder');

        $all_entries = json_decode(\Request::input('tree'), true);
        $user_id = backpack_user()->id;
        $role_id = backpack_user()->roles->pluck('id');

        if ($user_id == '1') {
            if (count($all_entries)) {
                $count = $this->crud->updateTreeOrder($all_entries);
            } else {
                return false;
            }
        } else {
            DB::beginTransaction();
            try {
                foreach ($all_entries as $entry) {
                    if ($entry['item_id'] != null) {
                        RoleHasMenuAccess::updateOrCreate(
                            ['user_id' => $user_id, 'menu_item_id' => $entry['item_id']],
                            ['role_id' => $role_id[0], 'display_order' => $entry['left'], 'parent_id' => $entry['parent_id'], 'depth' => $entry['depth']],
                        );
                    }
                }
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                dd($th);
            }
        }

        return 'success';
    }

    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();
        $model_name = \strtolower(preg_replace('/\s+/', '', $request->name_en));

        // register any Model Events defined on fields
        $this->crud->registerFieldEvents();

        // create the row in the db
        DB::beginTransaction();
        try {
            $item = MenuItem::create(
                [
                    'name_en' => $request->name_en,
                    'name_lc' => $request->name_lc,
                    'model_name' => $model_name,
                    'display_name' => $request->display_name,
                    'icon_picker' => $request->icon_picker,
                    'parent_id' => $request->parent_id,
                    // 'link' => $request->link,
                ],
            );

            \Alert::success(trans('backpack::crud.insert_success'))->flash();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            dd($th);
        }

        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    public function update()
    {
        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();
        $model_name = \strtolower(preg_replace('/\s+/', '', $request->name_en));

        // register any Model Events defined on fields
        $this->crud->registerFieldEvents();

        // update the row in the db
        DB::beginTransaction();
        try {
            $item = MenuItem::whereId($request->id)->update(
                [
                    'name_en' => $request->name_en,
                    'name_lc' => $request->name_lc,
                    'model_name' => $model_name,
                    'display_name' => $request->display_name,
                    'icon_picker' => $request->icon_picker,
                    'parent_id' => $request->parent_id,
                ],
            );

            \Alert::success(trans('backpack::crud.insert_success'))->flash();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            dd($th);
        }

        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction();
    }

    private function getMenuItems()
    {
        $model_names = backpack_user()->getAllPermissions()->map(function ($permission) {
            $item = explode(' ', $permission->name);
            return end($item);
        });

        $model_names = array_values(array_unique($model_names->toArray()));
        $menus = MenuItem::menulist($model_names);

        $parent_menus = MenuItem::where('created_by', backpack_user()->sup_org_id)->get();

        // dd($menus,$parent_menus);

        $new_menus = $menus->merge($parent_menus);
        return $new_menus;
    }
}
