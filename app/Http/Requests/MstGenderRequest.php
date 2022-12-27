<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MstGenderRequest extends FormRequest
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
        $id_check = $this->request->get('id') ? ",".$this->request->get('id') : "";

        return [
            'code' => 'required|unique:mst_genders,code'.$id_check,
            'name_en' => 'required|unique:mst_genders,name_en'.$id_check,
            'name_lc' => 'required|unique:mst_genders,name_lc'.$id_check,
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
