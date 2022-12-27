<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PoSequenceRequest extends FormRequest
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
            'name_en' => 'required|max:100|unique:po_sequences,name_en'.$id_check,
            'name_lc' => 'max:100|unique:po_sequences,name_lc'.$id_check,
            'sequence_code' => 'required|max:100',

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
            'sequence_code' => trans('common.sequence_code'),
           
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
