<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MstItemRequest extends FormRequest
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
        $id_check = $this->request->get('id') ? ",".$this->request->get('id') : ",NULL";
        $sup_org_id = $this->request->get('sup_org_id') ? ",".$this->request->get('sup_org_id') : ",NULL";
        $sup_org_check = $id_check.",id,sup_org_id".$sup_org_id.",deleted_uq_code,1";

        return [
            'barcode_details'=>'max:100|nullable|unique:mst_items,barcode_details'.$id_check,
            'sup_org_id' => 'required',
            // 'code' => 'unique:mst_items,code'.$id_check,
            // 'item_price' => 'required|max:100',
            // 'store_id' => 'required',
            'name' => 'required|alpha_dash||max:100|unique:mst_items,name'.$sup_org_check,
            'description' => 'max:1000',
            // 'category_id' => 'required',
            // 'subcategory_id' => 'required',
            'supplier_id' => 'required',
            // 'brand_id' => 'required',
            'unit_id' => 'required',
            'stock_alert_minimum' => 'required|max:100',
            // 'discount_mode_id' => 'required',
            'tax_vat' => 'required_if:is_taxable,==,1',
            'sales_acount_ledger_id' => 'required_if:sales_account_sales,==,1',
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
            'barcode_details' => trans('common.barcode_details'),
            'sup_org_id' => trans('common.sup_org_id'),
            'batch_no' => trans('common.batch_no'),
            'store_id' => trans('common.store_id'),
            'name' => trans('common.name'),
            'description' => trans('common.description'),
            'category_id' => trans('common.category_id'),
            'subcategory_id' => trans('common.subcategory_id'),
            'supplier_id' => trans('common.supplier_id'),
            'brand_id' => trans('common.brand_id'),
            'unit_id' => trans('common.unit_id'),
            'stock_alert_minimum' => trans('common.stock_alert_minimum'),
            'tax_vat' => trans('common.tax_vat'),
            'discount_mode_id' => trans('common.discount_mode_id'),
            'is_taxable' => 'taxable field',
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
            'required' => 'The :attribute field is required.',
            'unique' => 'The :attribute has already been taken.',
            'max' => 'The :attribute must not be greater than :max.',
            'tax_vat.required_if' => 'Tax Vat field is required when Taxable Field is selected Yes',
            'sales_acount_ledger_id.required_if' => 'Sales account ledger is required if specified here',
        ];
    }
}
