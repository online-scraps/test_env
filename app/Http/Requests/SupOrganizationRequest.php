<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupOrganizationRequest extends FormRequest
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
        $id_check = $this->request->get('id') ? "," . $this->request->get('id') : "";
        return [
            'name_en' => 'required|max:100|unique:sup_organizations,name_en' . $id_check,
            'name_lc' => 'max:100|unique:sup_organizations,name_lc' . $id_check,
            'address' => 'required|max:100',
            'email' => 'required|email|max:100|unique:sup_organizations,email' . $id_check,
            'phone_no' => 'required|numeric|digits:10',
            'description' => 'max:1000',
            'country_id'=>'required'
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
            'address' => trans('common.address'),
            'email' => trans('common.email'),
            'phone_no' => trans('common.phone_no'),
            'description' => trans('common.description'),
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
            'max' => 'The :attribute length must not be greater than :max.',
            'min' => 'The :attribute length must be greater than :min.',
        ];
    }
}
