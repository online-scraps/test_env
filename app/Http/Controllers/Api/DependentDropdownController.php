<?php

namespace App\Http\Controllers\Api;

use App\Models\MstStore;
use App\Models\MstDistrict;
use App\Models\MstProvince;
use Illuminate\Http\Request;
use App\Models\MstDepartment;
use App\Http\Controllers\Controller;

class DependentDropdownController extends Controller
{
    public function getProvince($id){
        $provinces = MstProvince::where('country_id',$id)->get();
        return response()->json($provinces);
    }

    public function getDistrict($id){
        $districts = MstDistrict::where('province_id',$id)->get();
        return response()->json($districts);
    }

    public function getSubDepartment(Request $request, $value)
    {
        $search_term = $request->input('q');
        $form = collect($request->input('form'))->pluck('value', 'name');
        $page = $request->input('page');
        $options = MstDepartment::query(); //model ma query gareko

        if (!data_get($form, $value)) {
            return [];
        }

        if (data_get($form, $value)) {
            $depart = MstDepartment::find($form[$value]);
            $options = $options->where('parent_id', $depart->id);
        }

        // if a search term has been given, filter results to match the search term
        if ($search_term) {
            $options = $options->where('name_en', 'ILIKE', "%$search_term%");
        }

        return $options->paginate(10);
    }

    public function getStore($id){
        $stores = MstStore::where('sup_org_id', $id)->get();
        return response()->json($stores);
    }
}
