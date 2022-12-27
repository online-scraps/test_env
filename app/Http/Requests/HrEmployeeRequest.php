<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HrEmployeeRequest extends FormRequest
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
            'full_name' => 'required|max:255',
            'date_ad' =>'required',
            'date_bs' =>'required',
            'email' =>'email|unique:hr_employees,email'. $id_check,
            'address' =>'required',
            'photo' =>'required',
            'gender_id' =>'required',
            'position_id' =>'required',
            // 'department_id' =>'required',
            'province_id' =>'required',
            'country_id' =>'required',
            'district_id' =>'required',
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
