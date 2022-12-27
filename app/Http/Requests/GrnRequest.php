<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GrnRequest extends FormRequest
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
            'po_date' => 'required',
            'approved_by' => 'required',
            // 'tax_amt' => 'required',
            // 'net_amt' => 'required',
            'store_id' => 'required',
            'supplier_id' => 'required',
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
            'po_date' =>'Purchase Order Date',
            'store_id' =>' Store',
            'supplier_id' =>'Supplier',
            'status_id' =>'Status',
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
            'required' => 'The :attribute field is required.',

        ];
    }
}
