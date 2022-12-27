<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesItemsDetailsRequest extends FormRequest
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
            'sales_items_id' => 'required',
            'product_id' => 'required',
            'unit_id' => 'required',
            'quantity' => 'required',
            'price' => 'required',
            'grand_total' => 'required',
            'discount_percentage' => 'min:0',
            'net_total' => 'required'
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
            'sales_items_id' => 'बिक्री वस्तु',
            'product_id' => 'उत्पादन',
            'unit_id' => 'एकाइ',
            'quantity' => 'मात्रा',
            'price' => 'मूल्य',
            'grand_total' => 'कूल जम्मा',
            'discount_percentage' => 'छुट प्रतिशत',
            'net_total' => 'कुल'
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
