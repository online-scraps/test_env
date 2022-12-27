<?php

namespace App\Http\Controllers\Admin;

use App\Models\SessionLog;
use App\Base\BaseCrudController;
use App\Http\Requests\SessionLogRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SessionLogCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SessionLogCrudController extends BaseCrudController
{

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        $this->crud->setModel(\App\Models\SessionLog::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/session-log');
        $this->crud->setEntityNameStrings('session log', 'session logs');
        $this->crud->addButtonFromModelFunction('line', 'activityLog', 'activityLog', 'beginning');
        $this->crud->denyAccess(['create', 'update', 'delete', 'show']);
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
            [
                'name' => 'row_number',
                'type' => 'row_number',
                'label' => trans('S.N.'),
                'orderable' => true,

            ],
            [
                'label' => trans('User Name'),
                'type' => 'text',
                'name' => 'username', // the db column for the foreign key

            ],
            [
                'label' => trans('User Email'),
                'name' => 'user_email', // the db column for the foreign key

            ],
            [
                'label' => trans('Login Date'),
                'type' => 'text',
                'name' => 'login_date', // the db column for the foreign key

            ],
            [
                'label' => trans('Login time'),
                'type' => 'text',
                'name' => 'login_time', // the db column for the foreign key

            ],
            [
                'label' => trans('Currently <br> logged In?'),
                'type' => 'check',
                'name' => 'is_currently_logged_in',
            ],
            [
                'label' => trans('Logout time'),
                'type' => 'text',
                'name' => 'logout_time', // the db column for the foreign key

            ],
            [
                'label' => trans('IP'),
                'type' => 'text',
                'name' => 'user_ip', // the db column for the foreign key

            ],
            [
                'label' => trans('Device'),
                'type' => 'text',
                'name' => 'device', // tdesktophe db column for the foreign key

            ],
            [
                'label' => trans('Platform'),
                'type' => 'text',
                'name' => 'platform', // tdesktophe db column for the foreign key

            ],
            [
                'label' => trans('Browser'),
                'type' => 'text',
                'name' => 'browser', // tdesktophe db column for the foreign key
            ],
        ];
        $this->crud->addColumns(array_filter($cols));
    }
}
