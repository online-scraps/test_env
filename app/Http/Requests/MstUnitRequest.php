<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MstUnitRequest extends FormRequest
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
        // $id_check = $this->request->get('id') ? ",".$this->request->get('id') : "";
        $id_check = $this->request->get('id') ? ",".$this->request->get('id') : ",NULL";
        $sup_org_id = $this->request->get('sup_org_id') ? ",".$this->request->get('sup_org_id') : ",NULL";
        $sup_org_check = $id_check.",id,sup_org_id".$sup_org_id.",deleted_uq_code,1";
        return [
            'name_en' => 'required|max:100|unique:mst_units,name_en'. $sup_org_check,
            'name_lc' => 'max:100|unique:mst_units,name_lc'. $sup_org_check,
            'description' => 'max:1000',
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
            'code' => 'Code',
            'name_en' => 'Name',
            'name_lc' => 'नाम',
            'description' => 'Description',
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
        ];
    }
}
