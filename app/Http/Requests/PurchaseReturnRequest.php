<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseReturnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'name' => 'required|min:5|max:255'
            'return_date' => 'required',
            'return_approved_by' => 'required',
            // 'tax_amt' => 'required',
            // 'net_amt' => 'required',
            'store_id' => 'required',
            'supplier_id' => 'required',
            'return_type' => 'required',
            'requested_store_id' => 'required',
            'return_reason_id' => 'required',
            'grn_sequences_id' => 'required',
            'status_id' => 'required',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //

            'store_id' =>' Store',
            'supplier_id' =>'Supplier',
            'status_id' =>'Status',
            'return_reason_id' => 'Return Reason',
            'grn_sequences_id' => 'Grn Sequence',
            'requested_store_id' =>'Requested Store',
            'purchase_order_type_id' =>'Purchase Order Type',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
