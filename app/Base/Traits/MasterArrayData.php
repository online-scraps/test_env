<?php
namespace  App\Base\Traits;

use Exception;
use App\Base\BaseModel;
use App\Models\SupStatus;
// use App\Models\StockEntries;
use App\Models\MstSequence;
use App\Models\ChartsOfAccount;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Support\Facades\Auth;

/**
 *  CheckPermission
 */
trait MasterArrayData{



    // Mst Fixed Asset Types
    public static function mst_fixed_items_list()
    {
        return [
            1 => 'PPE (Property, Plant, and Equipment)',
            2 => 'Land',
            3 => 'Buildings',
            4 => 'Vehicles',
            5 => 'Furniture',
            6 => 'Machinery',
        ];
    }

    //Series Number for Account
    public static function terminal_list(){
        return [
            1 => 'Opening Balance Voucher',
            2 => 'Journal Voucher',
            3 => 'Receipt Voucher',
            4 => 'Payment Voucher',
            5 => 'Contra Voucher',
            6 => 'Purchase Voucher',
            7 => 'Sales Voucher',
            8 => 'Sales Quotation Voucher',
            9 => 'Purchase Quotation Voucher',
            10 => 'Sales Order',
            11 => 'Purchase Order',
            12 => 'Sales Return (Cr. Note) Voucher',
            13 => 'Purchase Return (Dr. Note) Voucher',
            14 => 'Payment Voucher',
            15 => 'Receipt Voucher',
            16 => 'Journal Voucher',
            18 => 'Dr. Note (W/O) Items',
            19 => 'Cr. Note (W/O) Items',
            20 => 'Production Voucher',
            21 => 'Unassemble Voucher',
            22 => 'Stock Journal Voucher',
            23 => 'Mat. Issued to Party Voucher',
            24 => 'Mat. Received from Party Voucher',
            25 => 'Physical Stock Voucher',
            26 => 'Stock Transfer Voucher',
        ];
    }

    // Meta Sequence Code for Inventory
    public static function sequence_type(){
        return [
            1 => 'Batch No',
            2 => 'Goods Received Note Sequence',
            3 => 'Invoice Sequence',
            4 => 'Purchase order Sequence',
            5 => 'Purchase Return Sequence',
            6 => 'Stock Adjustment Sequence',
            7 => 'Sales Return Sequence',
            8 => 'Chalan Entry Sequence',
            9 => 'Stock Transfer Sequence',
            10 => 'Fixed Asset Stock Sequence',
        ];
    }

    //  BEGIN:: Type MASTER
    public static function tax_type(){
        return [
            1 => 'Taxable (Voucher-wise)',
            2 => 'Taxable (Item-wise)',
            3 => 'Against ST Form',
            4 => 'Tax Paid',
            5 => 'Exempt',
            6 => 'Tax Free',
            7 => 'Lump Sum Dealer',
            8 => 'Nil Rated',
        ];
    }

    public static function account_info(){
        return [
            1 => 'Specify here(Single A/c)',
            2 => 'Specify here(Seperate Accounts for different Tax-Rates)',
            3 => 'Specify in voucher',
        ];
    }

    public static function region(){
        return [
            1 => 'Local',
            2 => 'Import',
        ];
    }

    public static function tax_calc(){
        return [
            1 => 'Single Tax Rate',
            2 => 'Multi Tax Rate',
        ];
    }
    //  END::Type MASTER

    public static function customerType()
    {
        return [
            1 => 'Business / Coorporate Customer',
            2 => 'Individual',
        ];
    }

    //Bill Sundry
    public static function accounting_material()
    {
        return [
            1 => 'Material Issue',
            2 => 'Material Receipt',
            3 => 'Stock Transfer',
        ];
    }

    public static function amount_bill_sundry_fed()
    {
        return [
            1 => 'Absolute Amount',
            2 => 'Per Main Qty',
            3 => 'Per Alt Qty',
            4 => 'Per Packaging Qty',
            5 => 'Percentage',
        ];
    }

    public static function bill_sundry_percentage_of()
    {
        return [
            1 => 'Net Bill Amount',
            2 => 'Items Basic Amount',
            3 => 'Selective Calculation',
            4 => 'Total MRP of Items',
            5 => 'Taxable Amount',
            6 => 'Previous Bill Sundry(s) Amount',
            7 => 'Other Bill Sundry',
            8 => 'Item Description',
        ];
    }

    public static function bill_sundry_calculated_on()
    {
        return [
            1 => 'Bill Sundry Amount',
            2 => 'Bill Sundry Applied On',
        ];
    }

    // META SEQUENCE
    public function setMetaSequesnce($model_to_save, $sequence_type_id, $sequence_type_name)
    {
            $this->user = backpack_user();
            $sequence = MstSequence::where(['sup_org_id' => $this->user->sup_org_id, 'sequence_type' => $sequence_type_id])->orderBy('created_at', 'DESC')->first();
            $seqName = $this->sequence_type()[$sequence_type_id];
            if(!$sequence){
                return [
                    'status' => 'error',
                    'result' => 'Create new sequence for '. $seqName
                ];
            }

            $sequence_code = $sequence->sequence_code;
            $starting_no = $sequence->starting_no;
            $adjustment_no = $starting_no;

            $code_word = $sequence_code.'-'.$adjustment_no;

            $existing_sequence_codes = $model_to_save::where(['sup_org_id'=> $this->user->sup_org_id])
                    ->where($sequence_type_name, '!=', null)->orderBy('created_at', 'DESC')->pluck($sequence_type_name)->toArray();

            if($sequence->is_consumed == true){
                $latest_code = $model_to_save::where(['sup_org_id'=> $this->user->sup_org_id])
                                ->where($sequence_type_name, '!=', null)->orderBy('created_at', 'DESC')->first();
                if($latest_code){
                    $latest_adjustment = $latest_code->$sequence_type_name;
                    $split = (explode('-',$latest_adjustment));
                    $adjustment_no = $split[1]+1;
                }
                $code_word = $sequence_code.'-'.$adjustment_no;
                if(in_array($code_word, $existing_sequence_codes)){
                    $code_word = $sequence_code.'-'.($adjustment_no + 1);
                }
            }else{
                if(in_array($code_word, $existing_sequence_codes)){
                    return [
                        'status' => 'error',
                        'result' => 'Sequence '.$code_word.' already exists'
                    ];
                }else{
                    $sequence->is_consumed = true;
                    $sequence->save();
                }
            }

            return [
                    'status' => 'success',
                    'result' =>$code_word
                ];


    }

    // vouchers
    public static function voucher_id(){
        return [
            0 => [
                'name' => 'Contra Voucher',
                'dr_cr' => 2,
                'voucher' => BaseModel::CONTRAVOUCHER,
            ],
            1 => [
                'name' => 'Payment Voucher',
                'dr_cr' => 1,
                'voucher' => BaseModel::PAYMENTVOUCHER,
            ],
            2 => [
                'name' => 'Receipt Voucher',
                'dr_cr' => 0,
                'voucher' => BaseModel::RECEIPTVOUCHER,
            ],
        ];
    }

    public static function dr_cr(){
        return [
            0 => [
                'name' => 'Dr.',
            ],
            1 => [
                'name' => 'Cr.',
            ],
            2 => [
                'name' => 'Both',
            ],
        ];
    }

    public static function group_id(){
        $groups = ChartsOfAccount::select('id','name')->get()->toArray();
        return $groups;
    }

    public static function numberSystem(){
        return[
            1 => 'Nepali',
            2 => 'English',
        ];
    }

    public function getsequenceCode($sequenceId)
    {
        $this->user = backpack_user();
        $data = MstSequence::where(['sup_org_id' => $this->user->sup_org_id, 'store_id' => $this->user->store_id, 'sequence_type' => $sequenceId])->pluck('id', 'sequence_code');
        return $data;
    }

    public function filterQueryByUser($query)
    {
        $this->user = backpack_user();

        if ($this->user->isSystemUser()) {
            $query = $query;
        }
        elseif($this->user->isOrgUser()) {
            $query = $query->Where('sup_org_id', $this->user->sup_org_id);
        }
        elseif($this->user->isStoreUser()) {
            $query = $query->Where(['sup_org_id' => $this->user->sup_org_id, 'store_id' => $this->user->store_id]);
        }
        return $query;
    }
}
