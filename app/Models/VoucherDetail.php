<?php

namespace App\Models;

use App\Base\BaseModel;
use App\Models\GeneralLedger;
use App\Models\ChartsOfAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoucherDetail extends BaseModel
{
    use HasFactory;

    protected $table = 'voucher_details';
    protected $guarded = ['id'];

    public function generalLedgerEntity(){
        return $this->belongsTo(ChartsOfAccount::class, 'general_ledger_id', 'id');
    }
}
