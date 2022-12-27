<?php

namespace App\Http\Controllers\Admin;

use App\Models\Activity;
use App\Models\ActivityLog;
use App\Base\BaseCrudController;
use App\Http\Requests\ActivityLogRequest;

/**
 * Class ActivityLogCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ActivityLogCrudController extends BaseCrudController
{
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        $this->crud->setModel(ActivityLog::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/activity-log');
        $this->crud->setEntityNameStrings('Activity Log', 'Activity Log');

        $this->enableDialog(true);
        $this->crud->denySave = true;
        
        $this->crud->denyAccess(['create', 'delete', 'show']);
        if (request()->has('session_id')) {
            $this->crud->addClause('where', 'session_id', request()->session_id);
        }
        $this->crud->back_url = 'session-log';
        $this->user = backpack_user();
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->removeButton('update');

        $this->crud->setValidation(ActivityLogRequest::class);

        $cols = [
            [
                'name' => 'row_number',
                'type' => 'row_number',
                'label' => trans('S.N.'),
                'orderable' => true,

            ],
            [
                'name' => 'session',
                'type' => 'session_activity_dialog',
                'label' => 'Session ID'
            ],
            [
                'name' => 'activity_name',
                'label' => 'Controller Name',
                'function_name' => 'controller_name',
                'type' => 'model_function',
            ],
            [
                'name' => 'activity_type',
                'label' => 'Activity Type',
            ],
            [
                'name' => 'activity_time',
                'label' => 'Activity Time',
            ],
            [
                'name' => 'activity_date_bs',
                'label' => 'Activity Date B.S.',
            ],
            [
                'name' => 'description',
                'label' => 'Description',
            ],
            [
                'name' => 'url',
                'label' => 'URL',
            ],
            [
                'name' => 'request_method',
                'label' => 'Request Method',
            ],
            [
                'name' => 'url_query_string',
                'label' => 'URL Query String',
            ],
            [
                'name' => 'url_response',
                'label' => 'URL Response',
            ],
            [
                'name' => 'status',
                'label' => 'Status',
            ],
        ];
        $this->crud->addColumns(array_filter($cols));
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        $arr = [
            [
                'name' => 'session',
                'type' => 'text',
                'label' => 'Session ID',
                'wrapperAttributes' => [
                    "class" => 'form-group col-md-4',
                ],
                'attributes' => [
                    'readonly' => true,
                ]
            ],
            [
                'name' => 'activity_name',
                'label' => 'Controller Name',
                'type' => 'text',
                'wrapperAttributes' => [
                    "class" => 'form-group col-md-8',
                ],
                'attributes' => [
                    'readonly' => true,
                ]
            ],
            [
                'name' => 'activity_type',
                'label' => 'Activity Type',
                'type' => 'text',
                'wrapperAttributes' => [
                    "class" => 'form-group col-md-4',
                ],
                'attributes' => [
                    'readonly' => true,
                ]
            ],
            [
                'name' => 'activity_date_bs',
                'label' => 'Activity Date B.S.',
                'type' => 'text',
                'wrapperAttributes' => [
                    "class" => 'form-group col-md-4',
                ],
                'attributes' => [
                    'readonly' => true,
                ]
            ],
            [
                'name' => 'activity_time',
                'label' => 'Activity Time',
                'type' => 'text',
                'wrapperAttributes' => [
                    "class" => 'form-group col-md-4',
                ],
                'attributes' => [
                    'readonly' => true,
                ]
            ],

            [
                'name' => 'description',
                'label' => 'Description',
                'type' => 'description_area',
                'attributes' => [
                    'readonly' => true,
                ]
            ],
            [
                'name' => 'url',
                'label' => 'URL',
                'type' => 'text',
                'wrapperAttributes' => [
                    "class" => 'form-group col-md-8',
                ],
                'attributes' => [
                    'readonly' => true,
                ]
            ],
            [
                'name' => 'request_method',
                'label' => 'Request Method',
                'type' => 'text',
                'wrapperAttributes' => [
                    "class" => 'form-group col-md-4',
                ],
                'attributes' => [
                    'readonly' => true,
                ]
            ],
            [
                'name' => 'url_query_string',
                'label' => 'URL Query String',
                'type' => 'text',
                'attributes' => [
                    'readonly' => true,
                ]
            ],
            [
                'name' => 'url_response',
                'label' => 'URL Response',
                'type' => 'text',
                'wrapperAttributes' => [
                    "class" => 'form-group col-md-8',
                ],
                'attributes' => [
                    'readonly' => true,
                ]
            ],
            [
                'name' => 'status',
                'label' => 'Status',
                'type' => 'text',
                'wrapperAttributes' => [
                    "class" => 'form-group col-md-4',
                ],
                'attributes' => [
                    'readonly' => true,
                ]
            ],
        ];

        $this->crud->addFields(array_filter($arr));
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
