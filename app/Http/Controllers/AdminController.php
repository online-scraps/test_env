<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\MstItem;
use App\Models\MstStore;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SupOrganization;
use App\Models\StockItemDetails;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Base\Helpers\GetNepaliServerDate;
use Prologue\Alerts\Facades\Alert;

class AdminController extends Controller
{
    protected $data = []; // the information we send to the view
    protected $user;
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(backpack_middleware());
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $this->data['title'] = trans('backpack::base.dashboard'); // set the page title
        $this->data['breadcrumbs'] = [
            trans('backpack::crud.admin')     => backpack_url('dashboard'),
            trans('backpack::base.dashboard') => false,
        ];



        // $this->data['organizations'] = SupOrganization::where('is_active', 1)->get();
        // dd($this->data['organizations']);
        // return view('customAdmin.dashboard.dashboard', $this->data);

        return view(backpack_view('dashboard'), $this->data);
    }

    /**
     * Redirect to the dashboard.
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function redirect()
    {
        // The '/admin' route is not to be used as a page, because it breaks the menu's active state.
        return redirect(backpack_url('dashboard'));
    }

    public function changeTabStatus($key)
    {

        $request = request();
        $session = $request->getSession();
        $request->session()->forget('current_tab');
        $request->session()->put('current_tab', $key);
        $current_tab = $request->session()->get('current_tab');
        $redirect_url = 'admin/dashboard';

        switch ($key) {
            case 'inventory_btn':
                $redirect_url = '';
                break;
            case 'master_btn':
                $redirect_url = '';
                break;
            case 'hr_management_btn':
                $redirect_url = '';
                break;
            case 'system_btn':
                $redirect_url = '';
                break;
        }
        return response()->json(['current_tab' => $current_tab, 'href' => $redirect_url]);
    }

    public function action(Request $request)
    {
        $icon_list_parent = [
            'Organization' => 'fas fa-building', 'Super Master' => 'fas fa-user-cog', 'Primary Master' => 'fas fa-users-cog',
            'Meta' => 'fas fa-file-invoice', 'Sales' => 'fas fa-file-invoice-dollar', 'Inventory Management' => 'fas fa-truck-moving',
            'Stock Management' => 'fas fa-boxes', 'HR' => 'fas fa-user-tie', 'Menu Management' => 'fas fa-bars',
            'Users Management' => 'fas fa-users', 'Settings' => 'fas fa-cog','Reports'=>'fas fa-book',
            'Accounts Master'=>'fas fa-file-invoice', 'Vouchers' => 'fas fa-file-invoice', 'Accounts Settings' => 'fas fa-cogs'
        ];
         

        $menu_mappings = [
            // inventory
            "Organization" => ['sup-organization'],
            "Super Master" =>  ['mst-country', 'mst-province', 'mst-district', 'mst-gender', 'sup-status'],
            "Primary Master" =>  [
                'mst-item', 'mst-store', 'mst-unit', 'mst-disc-mode', 'mst-category', 'mst-relation',
                'mst-subcategory', 'mst-supplier', 'mst-brand', 'mst-position', 'mst-department',
                'mst-fiscal-year', 'mst-customer'
            ],
            "Meta" => [
                'mst-sequence'
            ],
            "Sales" => ['sales', 'sales-items-details'],
            "Inventory Management" => ['purchase-order-type', 'purchase-order-detail', 'grn', 'grn-type', 'purchase-return'],
            "Stock Management" => ['stock-entries', 'stock-status', 'stock-transfer'],
            "HR" => ['hr-employee'],
            "Menu Management" => ['menu-item'],
            "Settings" => ['app-setting'],
            "Users Management" => ['user', 'role', 'permission'],
            "Reports" => ['barcode-report','session-log'],

            // accounts
            "Accounts Settings" => [
                'account-setting', 'voucher-group-setting'
            ],
            "Accounts Master" => [
                'charts-of-account','series-number', 'system-configuration','sales-type-master', 'purchase-type-master', 'bill-sundry',
            ],
            "Vouchers" => [
                'journal-voucher', 'contra-voucher', 'payment-voucher', 'receipt-voucher',
            ],
        ];


        if ($request->ajax()) {
            $query = $request->get('query');
            $query = str_replace(" ", "_", $query);
            // $query = str_replace(" ", "_", $query);
            $tags = DB::table('menu_items')->select('model_name')->get();
            // $permission = User::with('permissions')->get();
            // dd($get_user_permissions->all());
            // $roles = Role::where('roles.id', $role_id)
            //     ->leftjoin('role_has_permissions', 'role_has_permissions.role_id', '=', 'roles.id')
            //     ->leftjoin('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            //     ->select('permissions.name')
            //     ->get();

            // $permissions = [];
            //get all permission of current user
            $permissions = backpack_user()->getAllPermissions()->mapWithKeys( function ($permission, $key) {
                return [$key => $permission->name];
            })->toArray();

            foreach ($permissions as $key => $item) {
                $remove_key = ['list', 'create', 'update', 'delete'];
                $perm = str_replace($remove_key, '', $item);
                $perm = str_replace(" ", "", $perm);
                $permissions[$key] = $perm;
            }
            // $permissions = array_unique($permissions);
            // dd($permissions);

            $data = DB::table('menu_items')
                ->select('name_en as name', 'model_name', 'link', 'icon_picker as icon', 'display_name')
                ->whereIn('model_name', $permissions)
                ->get();

            //group menu items
            $menu_data = [];
            foreach ($menu_mappings as $key => $item) {
                foreach ($data as $d) {
                    if (in_array($d->link, $menu_mappings[$key])) {
                        if ($d->display_name != null) {
                            $d->name = $d->display_name;
                        } else {
                            $d->name = str_replace('','Mst',$d->name);
                        }
                        $menu_data[$key][] = $d;
                    }
                }
            }
            $total_row = $data->count();
            $data = array(
                'menu_data'  => $menu_data,
                'icon_list' => $icon_list_parent,
                'total_rows' => $total_row,
                'tags' => $tags
            );

            // dd($data);

            echo json_encode($data);
        }
    }

    public function dashboardHome()
    {

        $this->data['title'] = trans('backpack::base.dashboard'); // set the page title
        $this->data['breadcrumbs'] = [
            trans('backpack::crud.admin')     => backpack_url('dashboard'),
            trans('backpack::base.dashboard') => false,
        ];
        // dd(backpack_user()->isSystemUser());

        if (!backpack_user()->isSystemUser()) {

            $this->data['stores'] =  MstStore::where('is_active', 1)
                ->where('sup_org_id', backpack_user()->sup_org_id)->count() ?? 0;

            $this->data['items'] = MstItem::where('is_active', 1)
                ->where('sup_org_id', backpack_user()->sup_org_id)->count() ?? 0;

            $this->data['users'] = User::where('is_active', 1)
                ->where('sup_org_id', backpack_user()->sup_org_id)->count() ?? 0;

            $this->data['total_barcodes'] = StockItemDetails::where('sup_org_id', backpack_user()->sup_org_id)->count() ?? 0;

            $this->data['active_barcodes'] = StockItemDetails::where('sup_org_id', backpack_user()->sup_org_id)
                ->where('is_active', 1)->count() ?? 0;

            $this->data['inactive_barcodes'] = StockItemDetails::where('sup_org_id', backpack_user()->sup_org_id)
                ->where('is_active', 0)->count() ?? 0;
        } else {

            $this->data['organizations'] = SupOrganization::where('is_active', 1)->count() ?? 0;

            $this->data['stores'] =  MstStore::where('is_active', 1)->count() ?? 0;

            $this->data['items'] = MstItem::where('is_active', 1)->count() ?? 0;

            $this->data['users'] = User::where('is_active', 1)->count() ?? 0;

            $this->data['total_barcodes'] = StockItemDetails::count() ?? 0;

            $this->data['active_barcodes'] = StockItemDetails::where('is_active', 1)->count() ?? 0;

            $this->data['inactive_barcodes'] = StockItemDetails::where('is_active', 0)->count() ?? 0;
        }
        return view('customAdmin.dashboard.dashboard', $this->data);
    }


    // Fetching data from super user through modal
    // Begin Code
    public function fetchMasterData(Request $request, $modelPath)
    {
        $this->user = backpack_user();
        $modelPath = Str::replace('_', '\\', $modelPath);
        $string = "body_checkbox-";
        $arrayEntryId = [];
        $errorArr = [];
        foreach ($request->all() as $key => $req) {
            $contains = Str::contains($key, $string);
            if ($contains) {
                $entryId = Str::remove($string, $key);
                array_push($arrayEntryId, $entryId);
            }
        }
        $originalData = $modelPath::findMany($arrayEntryId);
        foreach ($originalData as $indData) {
            $existing = $modelPath::where(
                [
                    'name_en' => $indData->name_en,
                    'sup_org_id' => $this->user->sup_org_id,
                    'store_id' => $this->user->store_id,
                    'is_super_data' => false,
                ]
            )->first();

            // dd($existing->id, $indData);

            $oldDataToUpdate = $modelPath::whereNotNull('sup_data_id')->pluck('id', 'sup_data_id')->toArray();

            if ($existing) {
                $error = $indData->name_en . ' already exists';
                array_push($errorArr, $error);
            }
            // elseif(!empty($oldDataToUpdate)){

            //     // dd($oldDataToUpdate);
            //     foreach ($oldDataToUpdate as $key => $value) {
            //         $originalIndData = $modelPath::find($key);
            //         $cloneIndData = $modelPath::find($value);
            //         $arrOriginalIndData = $originalIndData->toArray();
            //         $arrOriginalIndData['sup_org_id'] = $this->user->sup_org_id;
            //         $arrOriginalIndData['store_id'] = $this->user->store_id;
            //         $arrOriginalIndData['is_super_data'] = false;
            //         $arrOriginalIndData['sup_data_id'] = $key;
            //         $cloneIndData->update($arrOriginalIndData);
            //     }
            // }
            else {
                $cloneData = $indData->replicate();
                $cloneData->sup_org_id = $this->user->sup_org_id;
                $cloneData->store_id = $this->user->store_id;
                $cloneData->is_super_data = false;
                $cloneData->sup_data_id = $indData->id;
                $cloneData->save();
            }
        }
        if(!count($errorArr)){
            Alert::success('Data Fetched Successfully.')->flash();
        }else{
            foreach ($errorArr as $err) {
                Alert::error($err)->flash();
            }
        }
        return redirect()->back();
    }
    // END Code
}
