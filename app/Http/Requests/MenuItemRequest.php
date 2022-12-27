<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuItemRequest extends FormRequest
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
            'name_lc' => 'required|max:100|unique:menu_items,name_lc'.$id_check,
            'name_en' => 'required|max:100|unique:menu_items,name_en'.$id_check,
            'model_name' => 'unique:menu_items,model_name'.$id_check,
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
            'name_lc' => trans('menuitem.name_lc'),
            'name_en' => trans('menuitem.name_en'),
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
            'max' => 'The :attribute must not be greater than :max.',
        ];
    }
}
