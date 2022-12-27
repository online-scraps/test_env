<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MstStoreRequest extends FormRequest
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
        // $id_check = $this->request->get('id') ? "," . $this->request->get('id') : "";
        $id_check = $this->request->get('id') ? ",".$this->request->get('id') : ",NULL";
        $sup_org_id = $this->request->get('sup_org_id') ? ",".$this->request->get('sup_org_id') : ",NULL";
        $sup_org_check = $id_check.",id,sup_org_id".$sup_org_id.",deleted_uq_code,1";
        return [
            'name_en' => 'required|max:100|:mst_stores,name_en' .  $sup_org_check,
            'name_lc' => 'max:100|:mst_stores,name_lc' .  $sup_org_check,
            'address' => 'required|max:100',
            'sup_org_id' => 'required|max:100',
            'email' => 'required|email|max:100|unique:mst_stores,email' .  $sup_org_check,
            'phone_no' => 'required|numeric|digits:10',

            // 'description' => 'max:1000',
            // 'store_user_id' => 'required|max:100',
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
            'name_en' => trans('common.name_en'),
            'name_lc' => trans('common.name_lc'),
            'sup_org_id'=>trans('common.sup_org_id'),
            'address' => trans('common.address'),
            'email' => trans('common.email'),
            'phone_no' => trans('common.phone_no'),
            'description' => trans('common.description'),
            'store_user_id' => trans('common.store_user_id'),
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
            'min' => 'The :attribute length must be greater than :min.',
            'max' => 'The :attribute length must not be greater than :max.',
        ];
    }
}
