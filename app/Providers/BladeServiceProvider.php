<?php
namespace App\Providers;

use App\User;
use Carbon\Carbon;
use \App\Models\AppClient;
use App\Helpers\DateHelper;
use App\Base\Helpers\NepaliNumber;
use App\Base\Helpers\NumberToWords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use \ImLiam\BladeHelper\Facades\BladeHelper;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {


        BladeHelper::directive('englishNumberToWords', function($value){
            $value = isset($value) ? $value : 0;
            // return $value;
              return \App\Utils\NumberToWords::ConvertToEnglishWord($value);
         
        });
      
    }
}