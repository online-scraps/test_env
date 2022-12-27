<?php

namespace App\Models;

use App\Base\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SessionLog extends Model
{
    use HasFactory, CrudTrait;

    protected $connection = 'pgsql2';
    protected $table = 'session_logs';
    protected $primaryKey = 'id';
    protected $fillable =['user_id','username','session_history_id','session_name','user_ip','device','platform','browser','mac_address','user_email',
                        'login_date','login_time','is_currently_logged_in','logout_time','created_by'];

    public function activityLog(){
        return '<a class="btn btn-success" style="font-size:12px; border-radius:10px; color:#red;" href="/admin/activity-log/?session_id='.urlencode($this->id).'" data-toggle="tooltip" title="View Activity"></i>Activity</a>';
    }
}
