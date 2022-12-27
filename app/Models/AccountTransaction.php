<?php

namespace App\Models;

use App\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountTransaction extends BaseModel
{
    use HasFactory;

    protected $table = 'account_transactions';
    protected $guarded = ['id'];
}
