<?php

namespace App\Models;

use App\Base\BaseModel;
use App\Models\MstFiscalYear;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class SystemConfiguration extends BaseModel
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'system_configurations';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'entry_controls_options' => 'array',
        'entry_controls_mandatory' => 'array',
    ];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */


    public function fiscalYearEntity()
    {
        return $this->belongsTo(MstFiscalYear::class, 'fiscal_year_id', 'id');
    }

    public function checkedByEntity()
    {
        return $this->belongsTo(User::class, 'checked_by', 'id');
    }


    public function preparedByEntity()
    {
        return $this->belongsTo(User::class, 'prepared_by', 'id');
    }

    public function approvedByEntity()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
