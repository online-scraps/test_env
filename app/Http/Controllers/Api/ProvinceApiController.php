<?php

namespace App\Http\Controllers\Api;

use App\Models\MstProvince;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MstCountry;

class ProvinceApiController extends Controller
{
    public function index(Request $request,$value)
    {
        $search_term = $request->input('q');
        $form = collect($request->input('form'))->pluck('value', 'name');
        $page = $request->input('page');
        $options = MstProvince::query();//model ma query gareko
        // if no category has been selected, show no options
        if (! data_get($form, $value)) {//countryvanne table ma search gareko using id
            return [];
        }
        // if a category has been selected, only show articles in that category
        if (data_get($form, $value)) {
            if($form[$value] != 8){
                $country = MstCountry::find($form[$value]);
                $options = $options->where('country_id', $country->id);
            }
        }
        // if a search term has been given, filter results to match the search term
         if ($search_term) {
            $options = $options->where('name_en', 'ILIKE', "%$search_term%");//k tannalako state ho tesaile
        }
        
        // dd($options->get());

        return $options->paginate(10); 
    }
}
