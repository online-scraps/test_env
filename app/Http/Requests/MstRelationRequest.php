<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MstRelationRequest extends FormRequest
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
            'code' => 'required|unique:mst_relations,code'.$id_check,
            'name_en' => 'required|unique:mst_relations,name_en'. $sup_org_check,
            'name_lc' => 'unique:mst_relations,name_lc'. $sup_org_check,
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
            //
        ];
    }
}
