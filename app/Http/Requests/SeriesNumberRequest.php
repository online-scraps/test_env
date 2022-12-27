<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SeriesNumberRequest extends FormRequest
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
        $id_check = $this->request->get('id') ? ",".$this->request->get('id') : ",NULL";
        $id_check = $id_check.",id";
        return [
            'terminal_id' => 'integer',
            // 'description' => 'required|unique:series_number,description'.$id_check,
            'description' => 'required',
            // 'starting_word' => 'required',
            'starting_no' => 'required|integer',
            // 'ending_word' => 'required',
            // 'padding_length' => 'required|integer',
            // 'padding_no' => 'required|integer',
            'fiscal_year_id' => 'required|integer',
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
            'terminal_id' => 'Terminal',
            'description' => 'Description',
            'starting_word' => 'Starting Word',
            'starting_no' => 'Starting Number',
            'ending_word' => 'Ending Word',
            'padding_length' => 'Padding Length',
            'padding_no' => 'Padding Char/No.',
            'fiscal_year_id' => 'Fiscal Year',
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
            'integer' => 'The :attribute must be number',
            'terminal_id.integer' => 'Invalid Terminal. Select Again.',
            'fiscal_year_id.integer' => 'Invalid Fiscal Year. Select Again.',
        ];
    }
}
