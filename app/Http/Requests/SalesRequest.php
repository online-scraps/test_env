<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesRequest extends FormRequest
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
            'full_name' => 'required|min:5|max:255',
            'gender_id' => 'required',
            'age' => 'required|min:0',
            'address' => 'required',
            'date_ad' => 'required|date',
            'date_bs' => 'required',
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
            'full_name' => 'पुरा नाम',
            'gender_id' => 'लिङ्ग',
            'age' => 'उमेर',
            'address' => 'ठेगाना',
            'date_ad' => 'अंग्रेजी मिति',
            'date_bs' => 'नेपाली मिति',
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
            'required' => 'कृपया :attribute भर्नुहोस !!!',
            'max' => ':attribute :max अंक बढि नभर्नुहोस !!!',
            'min' => ':attribute :min अंक बढि नभर्नुहोस !!!',
        ];
    }
}
