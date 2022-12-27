<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesTypeMasterRequest extends FormRequest
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
            'sales_type' => 'required|min:1|max:10'
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
            'sales_type' => 'Sales Type',
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
            'required' => 'The :attribute is required',
            'sales_type.min' => 'The :attribute must be of minimum :min characters long',
            'sales_type.max' => 'The :attribute must be of maximum :max characters long',
        ];
    }
}
