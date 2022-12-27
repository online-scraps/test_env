<?php
namespace  App\Base\Traits;

use App\Models\MstStore;
use App\Models\AppSetting;
use App\Models\SeriesNumber;
use App\Models\ContraVoucher;
use App\Models\VoucherDetail;
use App\Models\AccountSetting;
use App\Models\JournalVoucher;
use App\Models\PaymentVoucher;
use App\Models\ReceiptVoucher;
use App\Models\ChartsOfAccount;
use App\Models\SupOrganization;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\Facades\Alert;
use App\Models\VoucherGroupSetting;
use Illuminate\Support\Facades\Storage;

trait VoucherCommon{
    public function voucherCreate($terminal_id, $voucher_type){
        // if($voucher_type == 2){
        //     $this->data['ledgers'] = $this->getLedgerData();
        // }else{
        //     if(backpack_user()->isStoreUser()){
        //         $this->data['ledgers'] = ChartsOfAccount::where('is_ledger',true)->whereStoreId(backpack_user()->store_id)->get();
        //     }else if(backpack_user()->isOrganizationUser()){
        //         $this->data['ledgers'] = ChartsOfAccount::where('is_ledger',true)->where('sup_org_id',backpack_user()->sup_org_id)->get();
        //     }else{
        //         $this->data['ledgers'] = ChartsOfAccount::where('is_ledger',true)->get();
        //     }
        // }

        $this->data['ledgers']['allowed'] = $this->getLedgerAutocomplete($voucher_type)['allowed'];
        $this->data['ledgers']['except'] = $this->getLedgerAutocomplete($voucher_type)['except'];
        $this->data['ledgers']['dr_cr'] = $this->getLedgerAutocomplete($voucher_type)['dr_cr'];

        $this->data['app_setting'] = AppSetting::where('sup_org_id', backpack_user()->sup_org_id)->first();

        $account_setting = AccountSetting::where('sup_org_id', backpack_user()->sup_org_id)->first();
        $this->data['account_setting'] = $account_setting;

        if($account_setting){
            $account_setting->maintain_image_note ?
            $this->data['maintain_image_note'] = DB::table('maintain_image_note_configure')->where('account_setting_id', $account_setting->id)->first() :
            $this->data['maintain_image_note'] = null;
        }

        $this->data['stores'] = MstStore::all();
        $this->data['organizations'] = SupOrganization::all();
        $this->data['series_numbers'] = SeriesNumber::whereSupOrgId(backpack_user()->sup_org_id)->whereTerminalId($terminal_id)->get();
        $this->data['today_date'] = convert_bs_from_ad();
    }

    public function voucherStore($request, $station, $voucher_type, $class){
            $request->request->set('station', $station);

            $voucherDetails = $request->only([
                'auto_no',
                'series_no_id',
                'voucher_no',
                'voucher_date_bs',
                'voucher_date_ad',
                'cheque_no',
                'pay_to',
                'narration',
                'total_dr_amount',
                'total_cr_amount',
                'sup_org_id',
                'store_id',
                'station',
                'note_with_account_master',
                'note_with_account_voucher',
            ]);

        DB::beginTransaction();
        $voucher_id = $class::create($voucherDetails);

        foreach($request->general_ledger_id as $key => $val){
            $sno = VoucherDetail::where('deleted_uq_code','=',1)->max('sno');
            if($sno == null)
                $sno = 1;
            else
                $sno = $sno + 1;

            $voucherDetails = [
                'sno' => $sno,
                'voucher_id' => $voucher_id->id,
                'sup_org_id' => $request->sup_org_id,
                'store_id' => $request->store_id,
                'dr_cr' => $request->dr_cr[$key],
                'general_ledger_id' => $request->general_ledger_hidden[$key],
                'dr_amount' => $request->dr_amount[$key],
                'cr_amount' => $request->cr_amount[$key],
                'remarks' => $request->remarks[$key],
            ];
            VoucherDetail::create($voucherDetails);

            $AccountTransaction = [
                'refrence_id' => $voucher_id->id,
                'series_no_id' => $request->series_no_id,
                'voucher_no' => $request->voucher_no,
                'voucher_date_ad' => $request->voucher_date_ad,
                'voucher_date_bs' => $request->voucher_date_bs,
                'sup_org_id' => $request->sup_org_id,
                'store_id' => $request->store_id,
                'general_ledger_id' => $request->general_ledger_hidden[$key],
                'cheque_no' => $request->cheque_no,
                'dr_amount' => $request->dr_amount[$key],
                'cr_amount' => $request->cr_amount[$key],
                'narration' => $request->narration,
                'station' => $request->station,
            ];
            AccountTransaction::create($AccountTransaction);
        }

        $accountMasterFileName = null;
        if($request->hasFile('image_with_account_master')){
            $file = $request->file('image_with_account_master');
            $file_name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $path = '/Voucher/'.$voucher_type.'/AccountMaster/'. backpack_user()->id . '/' . $voucher_id->id ;
            $filename_with_extension = $file_name.'_'.time().'.'.$extension;
            $accountMasterFileName = $path.'/'.$filename_with_extension;

            $mpath = $request->file('image_with_account_master')->storeAs($path, $filename_with_extension,'uploads');
        }

        $accountVoucherFileName = null;
        if($request->hasFile('image_with_account_voucher')){
            $file = $request->file('image_with_account_voucher');
            $file_name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $path = '/Voucher/'.$voucher_type.'/AccountVoucher/'. backpack_user()->id . '/' .$voucher_id->id ;
            $filename_with_extension = $file_name.'_'.time().'.'.$extension;
            $accountVoucherFileName = $path.'/'.$filename_with_extension;

            $mpath = $request->file('image_with_account_voucher')->storeAs($path, $filename_with_extension,'uploads');
        }

        DB::table('mst_vouchers')->where('id',$voucher_id->id)->update([
            'image_with_account_master' => $accountMasterFileName,
            'image_with_account_voucher' => $accountVoucherFileName,
        ]);

        DB::commit();
    }

    public function voucherEdit($id, $terminal_id, $voucher_type)
    {
        if($voucher_type == 2){
            $this->data['ledgers'] = $this->getLedgerData();
        }else{
            if(backpack_user()->isStoreUser()){
                $this->data['ledgers'] = ChartsOfAccount::where('is_ledger',true)->whereStoreId(backpack_user()->store_id)->get();
            }else if(backpack_user()->isOrganizationUser()){
                $this->data['ledgers'] = ChartsOfAccount::where('is_ledger',true)->where('sup_org_id',backpack_user()->sup_org_id)->get();
            }else{
                $this->data['ledgers'] = ChartsOfAccount::where('is_ledger',true)->get();
            }
        }

        $this->data['app_setting'] = AppSetting::where('sup_org_id', backpack_user()->sup_org_id)->first();

        $account_setting = AccountSetting::where('sup_org_id', backpack_user()->sup_org_id)->first();
        $this->data['account_setting'] = $account_setting;

        if($account_setting){
            $account_setting->maintain_image_note ?
            $this->data['maintain_image_note'] = DB::table('maintain_image_note_configure')->where('account_setting_id', $account_setting->id)->first() :
            $this->data['maintain_image_note'] = null;
        }

        $this->data['voucher_details'] = VoucherDetail::whereVoucherId($id)->get();
        $this->data['stores'] = MstStore::all();
        $this->data['organizations'] = SupOrganization::all();
        $this->data['series_numbers'] = SeriesNumber::whereSupOrgId(backpack_user()->sup_org_id)->whereTerminalId($terminal_id)->get();
    }

    public function voucherUpdate($request, $voucher_type, $id, $class){
        $voucherDetails = $request->only([
            'auto_no',
            'series_no_id',
            'voucher_no',
            'voucher_date_bs',
            'voucher_date_ad',
            'cheque_no',
            'pay_to',
            'narration',
            'total_dr_amount',
            'total_cr_amount',
            'sup_org_id',
            'store_id',
            'note_with_account_master',
            'note_with_account_voucher',
        ]);

        DB::beginTransaction();

        $journal_voucher = $class::whereId($id)->first();
        $class::whereId($id)->update($voucherDetails);

        VoucherDetail::whereVoucherId($id)->delete();
        AccountTransaction::whereRefrenceId($id)->delete();

        foreach($request->general_ledger_id as $key => $val){
            $voucherDetails = [
                'voucher_id' => $id,
                'sup_org_id' => $request->sup_org_id,
                'store_id' => $request->store_id,
                'dr_cr' => $request->dr_cr[$key],
                'general_ledger_id' => $request->general_ledger_hidden[$key],
                'dr_amount' => $request->dr_amount[$key],
                'cr_amount' => $request->cr_amount[$key],
                'remarks' => $request->remarks[$key],
            ];
            VoucherDetail::create($voucherDetails);

            $AccountTransaction = [
                'refrence_id' => $id,
                'series_no_id' => $request->series_no_id,
                'voucher_no' => $request->voucher_no,
                'voucher_date_ad' => $request->voucher_date_ad,
                'voucher_date_bs' => $request->voucher_date_bs,
                'sup_org_id' => $request->sup_org_id,
                'store_id' => $request->store_id,
                'general_ledger_id' => $request->general_ledger_hidden[$key],
                'cheque_no' => $request->cheque_no,
                'dr_amount' => $request->dr_amount[$key],
                'cr_amount' => $request->cr_amount[$key],
                'narration' => $request->narration,
            ];
            AccountTransaction::create($AccountTransaction);
        }

        $accountMasterFileName = null;
        if($request->hasFile('image_with_account_master')){
            if(Storage::disk('uploads')->exists($journal_voucher->image_with_account_master)){
                Storage::disk('uploads')->delete($journal_voucher->image_with_account_master);
            }

            $file = $request->file('image_with_account_master');
            $file_name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $path = '/Voucher/'.$voucher_type.'/AccountMaster/'. backpack_user()->id . '/' . $id ;
            $filename_with_extension = $file_name.'_'.time().'.'.$extension;
            $accountMasterFileName = $path.'/'.$filename_with_extension;

            $mpath = $request->file('image_with_account_master')->storeAs($path, $filename_with_extension,'uploads');
        }else if($request->image_with_account_master_current == $journal_voucher->image_with_account_master){
            $accountMasterFileName = $request->image_with_account_master_current;
        }else{
            $accountMasterFileName = null;
        }


        $accountVoucherFileName = null;
        if($request->hasFile('image_with_account_voucher')){
            if(Storage::disk('uploads')->exists($journal_voucher->image_with_account_voucher)){
                Storage::disk('uploads')->delete($journal_voucher->image_with_account_voucher);
            }

            $file = $request->file('image_with_account_voucher');
            $file_name = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $path = '/Voucher/'.$voucher_type.'/AccountVoucher/'. backpack_user()->id . '/' .$id ;
            $filename_with_extension = $file_name.'_'.time().'.'.$extension;
            $accountVoucherFileName = $path.'/'.$filename_with_extension;

            $mpath = $request->file('image_with_account_voucher')->storeAs($path, $filename_with_extension,'uploads');
        }else if($request->image_with_account_voucher_current == $journal_voucher->image_with_account_voucher){
            $accountVoucherFileName = $request->image_with_account_voucher_current;
        }else{
            $accountVoucherFileName = null;
        }

        DB::table('mst_vouchers')->where('id', $id)->update([
            'image_with_account_master' => $accountMasterFileName,
            'image_with_account_voucher' => $accountVoucherFileName,
        ]);

        DB::commit();
    }

    public function getLedgerData(){
        $sup_org_id = backpack_user()->sup_org_id;
        $store_id = backpack_user()->store_id;
        $groups = null;

        $group_setting_id = VoucherGroupSetting::whereSupOrgId($sup_org_id)->first();
        if($group_setting_id){
            $group_id = json_decode($group_setting_id->group_id);

            foreach($this->voucher_id() as $key => $voucher){
                if($voucher['voucher'] == ContraVoucher::CONTRAVOUCHER){
                    $groups = $group_id[$key];
                }
            }
        }

        if($groups){
            if(backpack_user()->isStoreUser()){
                $data = $this->getLedgers($sup_org_id,$store_id,$groups);
            }else if(backpack_user()->isOrganizationUser() && backpack_user()->store_id == null){
                $data = $this->getLedgers($sup_org_id,$store_id,$groups);
            }else{
                $data = ChartsOfAccount::where('is_ledger',true)->get();
            }
        }else{
            if(backpack_user()->isStoreUser()){
                $data = ChartsOfAccount::where('is_ledger',true)->whereStoreId(backpack_user()->store_id)->get();
            }else if(backpack_user()->isOrganizationUser()){
                $data = ChartsOfAccount::where('is_ledger',true)->where('sup_org_id',backpack_user()->sup_org_id)->get();
            }else{
                $data = ChartsOfAccount::where('is_ledger',true)->get();
            }
        }

        return $data;
    }

    public function getLedgers($sup_org_id, $store_id,$groups){
        $group_where = '';
            foreach($groups as $key => $group){
                $key == 0 ? $group_where .= ' group_id = '. $group : $group_where .= ' OR group_id = ' . $group;
        }

        $query = "WITH RECURSIVE ledgers AS (
            SELECT
                *
            FROM
                charts_of_accounts
            WHERE" . $group_where ." UNION SELECT
                charts_of_accounts.*
            FROM
                charts_of_accounts
                JOIN ledgers ON charts_of_accounts.group_id = ledgers.id
            ) SELECT
            id,name
        FROM
            ledgers
            where is_ledger = true
        ";

        if(isset($store_id)){
            $query = $query . 'OR store_id = ' . $store_id . ' OR sup_org_id = ' . $sup_org_id;
        }
        if(isset($sup_org_id) && $store_id == null){
            $query = $query . ' AND sup_org_id = ' . $sup_org_id . ' AND store_id IS NULL';
        }

        $ledgers = DB::select(DB::RAW($query));

        return collect($ledgers);
    }

    public function getLedgerAutocomplete($voucherTypeId)
    {
        $sup_org_id = backpack_user()->sup_org_id;
        $store_id = backpack_user()->store_id;
        $groups = null;
        $debit_credits = null;
        $dr_cr = null;

        $group_setting_id = VoucherGroupSetting::whereSupOrgId($sup_org_id)->first();

        if($group_setting_id){
            $group_id = json_decode($group_setting_id->group_id);
            $dr_cr = json_decode($group_setting_id->dr_cr);
            foreach($this->voucher_id() as $key => $voucher){
                if($voucher['voucher'] == $voucherTypeId){
                    $groups = $group_id[$key];
                    $debit_credits = $dr_cr[$key];
                }
            }
        }

        $groupsExceptIds = ChartsOfAccount::where([['group_id', '!=' , null]])->distinct()->pluck('group_id')->toArray(); //

        // dd($groups, $groupsExceptIds);

        if($groups){
            if(backpack_user()->isStoreUser()){
                $ledgerData = $this->getLedgers($sup_org_id,$store_id,$groups);
                $exceptData = $this->getLedgers($sup_org_id,$store_id,$groupsExceptIds);
            }else if(backpack_user()->isOrganizationUser() && backpack_user()->store_id == null){
                $ledgerData = $this->getLedgers($sup_org_id,$store_id,$groups);
                $exceptData = $this->getLedgers($sup_org_id,$store_id,$groupsExceptIds);
            }else{
                $ledgerData = ChartsOfAccount::where('is_ledger',true)->get();
            }
        }

        // dd($debit_credits, $ledgerData, $groups);

        return [
            'allowed' => $ledgerData,
            'except' => $exceptData,
            'dr_cr' => $debit_credits
        ];
    }

    public function getLedgersExcepTGroupSetting($sup_org_id, $store_id, $groups){
        $group_where = '';
            foreach($groups as $key => $group){
                $key == 0 ? $group_where .= ' group_id = '. $group : $group_where .= ' OR group_id = ' . $group;
        }

        $query = "WITH RECURSIVE ledgers AS (
            SELECT
                *
            FROM
                charts_of_accounts
            WHERE" . $group_where ." UNION SELECT
                charts_of_accounts.*
            FROM
                charts_of_accounts
                JOIN ledgers ON charts_of_accounts.group_id = ledgers.id
            ) SELECT
            id,name
        FROM
            ledgers
            where is_ledger = true
        ";

        if(isset($store_id)){
            $query = $query . 'OR store_id = ' . $store_id . ' OR sup_org_id = ' . $sup_org_id;
        }
        if(isset($sup_org_id) && $store_id == null){
            $query = $query . ' AND sup_org_id = ' . $sup_org_id . ' AND store_id IS NULL';
        }

        $ledgers = DB::select(DB::RAW($query));

        return collect($ledgers);
    }
}
