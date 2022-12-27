<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\MstStore;
use App\Models\HrEmployee;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\SupOrganization;
use App\Base\BaseCrudController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;

class UserCrudController extends BaseCrudController
{
    protected $user;


    public function setup()
    {
        $this->user = backpack_user();
        $this->crud->setModel(User::class);
        $this->crud->setEntityNameStrings(trans('backpack::permissionmanager.user'), trans('backpack::permissionmanager.users'));
        $this->crud->setRoute('admin/user');
        $this->isAllowed(['getStoreListAPI'=>'list']);
    }

    public function setupListOperation()
    {
        $cols = [
            $this->addRowNumberColumn(),
            [
                'name'  => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            [ // n-n relationship (with pivot table)
                'label'     => trans('backpack::permissionmanager.roles'), // Table column heading
                'type'      => 'select_multiple',
                'name'      => 'roles', // the method that defines the relationship in your Model
                'entity'    => 'roles', // the method that defines the relationship in your Model
                'attribute' => 'field_name', // foreign key attribute that is shown to user
                'model'     => config('permission.models.role'), // foreign key model
            ],

            $this->addSuperOrganizationColumn(),
            [
                'name' => 'is_admin',
                'label' => "Is Admin",
                'type' => 'radio',
                'options' =>
                [
                    1 => 'Yes',
                    0 => 'No',
                ],
            ],
        ];

        $cols = array_filter($cols);

        $this->crud->addColumns($cols);
        // if (!$this->user->hasRole('superadmin')) {
        //     $this->crud->query->where('sup_org_id', $this->user->sup_org_id);
        // }
    }

    public function addFields()
    {
        $is_admin = [
            'name' => 'is_admin',
            'label' => "Is Admin",
            'type' => 'radio',
            'default' => 0,
            'inline' => true,
            'wrapper' => [
                'class' => 'form-group col-md-4',
            ],
            'options' =>
            [
                true => 'Yes',
                false => 'No',
            ],
        ];
        if(!$this->user->isSystemUser()){
            $already_exist_admin_user = DB::table('users')->where([['sup_org_id', $this->user->sup_org_id],['is_admin', true]])->first();
            if($already_exist_admin_user){
                $is_admin = null;
            }
        }
        
        $arr = [
            $this->addSuperOrgField(),

            [
                'label' => trans('Select Employee'),
                'type' => 'select2',
                'name' => 'employee_id',
                'entity' => 'employeeEntity',
                'attribute' => 'full_name',
                'model' => HrEmployee::class,
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6',
                ],
                'options'   => (function ($query) {
                    return (new HrEmployee())->getFieldActiveComboOptions($query);
                }),
                'attributes' => [
                    'id' => 'employee-id',
                    'onChange' => 'INVENTORY.fetchEmployeeById(this)'
                ],
            ],

            [
                'label' => 'Select Store',
                'type' => 'select2_from_ajax',
                'name' => 'store_id',
                'method' => 'GET',
                'entity' => 'storeEntity',
                'attribute' => 'name_en',
                'model' => MstStore::class,
                'data_source' => url("admin/api/mststore/sup_org_id"), //api/modelsmallname/tableid from which state is taken
                'minimum_input_length' => 0,
                'dependencies' => ["sup_org_id"],
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6',
                ],
                'attributes' => [
                    'placeholder' => 'Select Organization first',
                ]

            ],
            [
                'type' => 'custom_html',
                'name' => 'custom_html_1',
                'value' => '<br/>',
            ],
            [
                'name'  => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type'  => 'text',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6',
                ],
                'attributes' => [
                    'id' => 'full_name',
                ],
            ],

            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6',
                ],
                'attributes' => [
                    'id' => 'email',
                ],
            ],

            [
                'name'  => 'password',
                'label' => trans('backpack::permissionmanager.password'),
                'type'  => 'password',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6',
                ],
            ],
            [
                'name'  => 'password_confirmation',
                'label' => trans('backpack::permissionmanager.password_confirmation'),
                'type'  => 'password',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-6',
                ],

            ],
            // [   // radio
            //     'name'        => 'user_level', // the name of the db column
            //     'label'       => 'User Level', // the input label
            //     'type'        => 'radio',
            //     'default'      => config('users.user_level.store_user'),
            //     'options'     => [
            //         // the key will be stored in the db, the value will be shown as label; 
            //         config('users.user_level.organization_user') =>"Organization User",
            //         config('users.user_level.store_user') =>"Store User",

            //     ],
            //     // optional
            //     //'inline'      => false, // show the radios all on the same line?
            // ],
            [
                'type' => 'custom_html',
                'name' => 'custom_html_2',
                'value' => '<br/>',
            ],
            [
                'label' => 'Is Discount approver',
                'type' => 'checkbox',
                'name' => 'is_discount_approver',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-2',
                ],
            ],
            [
                'label' => 'Is Due approver',
                'type' => 'checkbox',
                'name' => 'is_due_approver',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-2',
                ],
            ],

            [
                'label' => 'Is Stock approver',
                'type' => 'checkbox',
                'name' => 'is_stock_approver',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-2',
                ],
            ],

            [
                'label' => 'Is Po approver',
                'type' => 'checkbox',
                'name' => 'is_po_approver',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-2',
                ],
            ],

            $is_admin,

            [
                // two interconnected entities
                'label'             => trans('backpack::permissionmanager.user_role_permission'),
                'field_unique_name' => 'user_role_permission',
                'type'              => 'checklist_dependency_custom',
                'name'              => ['roles', 'permissions'],
                'subfields'         => [
                    'primary' => [
                        'label'            => trans('backpack::permissionmanager.roles'),
                        'name'             => 'roles', // the method that defines the relationship in your Model
                        'entity'           => 'roles', // the method that defines the relationship in your Model
                        'entity_secondary' => 'permissions', // the method that defines the relationship in your Model
                        'attribute'        => 'field_name', // foreign key attribute that is shown to user
                        'model'            => Role::class, // foreign key model
                        'pivot'            => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns'   => 4, //can be 1,2,3,4,6
                        'option' => $this->getPrivateRoles(),
                    ],
                    'secondary' => [
                        'label'          => ucfirst(trans('backpack::permissionmanager.permission_singular')),
                        'name'           => 'permissions', // the method that defines the relationship in your Model
                        'entity'         => 'permissions', // the method that defines the relationship in your Model
                        'entity_primary' => 'roles', // the method that defines the relationship in your Model
                        'attribute'      => 'name', // foreign key attribute that is shown to user
                        'model'          => Permission::class, // foreign key model
                        'pivot'          => true, // on create&update, do you need to add/delete pivot table entries?]
                        'option' => $this->getPermissions(),
                        'number_columns' => 4, //can be 1,2,3,4,6
                    ],
                ],

            ],

        ];
        $arr = array_filter($arr);
        $this->crud->addFields($arr);
    }

    public function setupCreateOperation()
    {
        $this->crud->setValidation(UserCreateRequest::class);
        $this->addFields();
    }

    public function setupUpdateOperation()
    {
        $this->crud->setValidation(UserUpdateRequest::class);
        $this->addFields();
    }
    // public function getRoles(){
    //     $user = backpack_user();
    //     $role  = DB::select('SELECT r.id from roles r where r.id in (SELECT mr.role_id from model_has_roles as mr
    //                         LEFT JOIN users as u on u.id = mr.model_id
    //                         WHERE u.id =:id)',['id'=>$user->id]);

    //     return $role;
    // }

    public function getPrivateRoles()
    {
        if (backpack_user()->isSystemUser()) {
            return Role::all();
        } else {
            $role = DB::table('roles')->where('user_id', backpack_user()->id)->get();
            return $role;
        }
    }

    public function getPermissions(){
        $user = User::find(backpack_user()->id);

        if ($this->user->isSystemUser()) {
            return Permission::all();
        } else {
            if($user->is_admin){
                $permissions = $user->getAllPermissions();
            }else{
                $permissions = $user->getPermissionsViaRoles();
            }
        }
        return $permissions;
    }


    public function store()
    {
        $this->crud->hasAccessOrFail('create');
        $user = backpack_user();

        $request = $this->crud->validateRequest();
        $request->request->set('created_by', $user->id);
        $request->request->set('updated_by', $user->id);

        if(!backpack_user()->isSystemUser()){
            $request->request->set('sup_org_id', $user->sup_org_id);
        }

        //save full_name, email and password for sending email
        $email_details = [
            'full_name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ];


        //encrypt password
        $request = $this->handlePasswordInput($request);

        if(isset($request->store_id)){
            if($request->request->get('is_admin')){
                $request['user_level']=config('users.user_level.store_admin');
            }else{
                $request['user_level']=config('users.user_level.store_user');
            }
        }else{
            // dd($request->user);
            $request['user_level']=config('users.user_level.organization_user');

        }
        DB::beginTransaction();
        try {
            $item = $this->crud->create($request->except(['save_action', '_token', '_method', 'http_referrer']));

            if ($item && env('SEND_MAIL_NOTIFICATION') == TRUE) {
                $this->send_mail($email_details);
            }

            $user = User::find($item->id);
            $permissions = $request->permissions;
            if($permissions){
                foreach($permissions as $perm){
                    $user->givePermissionTo($perm);
                }
            }

            // $this->client_user->notify(new TicketCreatedNotification($item));

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


    public function update()
    {
        $this->crud->hasAccessOrFail('update');
        // $user = backpack_user();

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        //save full_name, email and password for sending email
        $email_details = [
            'full_name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ];
        //encrypt password
        $request = $this->handlePasswordInput($request);
        // dd($request->all());

        DB::beginTransaction();
        try {
            
            $item = $this->crud->update(
                $request->get($this->crud->model->getKeyName()),
                $request->except(['save_action', '_token', '_method', 'http_referrer'])
            );

            
            $user = User::find($item->id);
            $permissions = $request->permissions;
            if($permissions){
                foreach($permissions as $perm){
                    $user->givePermissionTo($perm);
                }
            }



            // if($item && env('SEND_MAIL_NOTIFICATION') == TRUE){
            //     $this->send_mail($email_details);
            // }
            \Alert::success(trans('backpack::crud.update_success'))->flash();

            DB::commit();
        } catch (\Throwable $th) {

            DB::rollback();
        }
        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }

    /**
     * Handle password input fields.
     */
    protected function handlePasswordInput($request)
    {
        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', Hash::make($request->input('password')));
        } else {
            $request->request->remove('password');
        }

        return $request;
    }


    public function fetchEmployeeDetails(Request $request)
    {
        $employeeId = $request->employeeId;
        $employee = HrEmployee::findOrFail($employeeId);
        return response()->json([
            'message' => 'success',
            'user' => $employee,
        ]);
    }


    public function getStoreListAPI(Request $request, $value)
    {

        

        $search_term = $request->input('q');
        $form = collect($request->input('form'))->pluck('value', 'name');
        $page = $request->input('page');
        $options = MstStore::query();//model ma query gareko
        // if no category has been selected, show no options
        if (! data_get($form, $value)) {//countryvanne table ma search gareko using id
            return [];
        }
        // if a category has been selected, only show articles in that category
        if (data_get($form, $value)) {
            if($form[$value] != 8){
                $org = SupOrganization::find($form[$value]);
                $options = $options->where('sup_org_id', $org->id);
            }
        }
        // if a search term has been given, filter results to match the search term
         if ($search_term) {
            //  dd($search_term);
            $options = $options->where('name_en', 'ILIKE', "%$search_term%");//k tannalako state ho tesaile
        }
        
        return $options->paginate(10);     
    }
    public function getchildStoreListAPI(Request $request)
    {

        $search_term = $request->input('q');

        $form = collect($request->input('form'))->pluck('value', 'name');

        $store_id = $form['store_hidden_id'];

        if (isset($store_id)) {
            $store_id = explode(',', $store_id);

        $options = MstStore::query();
            $options = $options->whereIn('parent_id', $store_id);
        } else {
            return [];
        }

        // if a search term has been given, filter results to match the search term
        if ($search_term) {
            $options = $options->where('name_lc', 'ILIKE', "%$search_term%"); //k tannalako state ho tesaile
        }

        return $options->paginate(10);     
    }
}
