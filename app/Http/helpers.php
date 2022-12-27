<?php
use App\Utils\DateHelper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Convert BS date to AD
 *
 * @param string|null $date_ad
 * @return string
 */
function convert_bs_from_ad(string $date_ad = null) {
    if(empty($date_ad)) {
        $date_ad = Carbon::now()->todateString();
    }

    $dateHelper = new DateHelper();
    return $dateHelper->convertBsFromAd($date_ad);
}

function get_current_fiscal_year(){
    $date_ad = Carbon::now()->todateString();
    $dateHelper = new DateHelper();
    $date_bs =  $dateHelper->convertBsFromAd($date_ad);
    return $dateHelper->fiscalYear($date_bs);
}

/**
 * Convert date from AD to BS
 *
 * @param string|null $date_bs
 * @return string
 */
function convert_ad_from_bs(string $date_bs = null) {
    if(empty($date_bs)) {
        $date_bs = Carbon::now()->todateString();
        return $date_bs;
    }

    $dateHelper = new DateHelper();
    return $dateHelper->convertAdFromBs($date_bs);
}


//get all models
function modelCollection()
{
    $output = [];
    $base_path = \base_path().'/app/Models';

    //scan all the models/entities in particular module
    $entities = \scandir($base_path);
    foreach($entities as $entity){
        if(\str_starts_with($entity,'.')) continue;  //ignore '.' and '..' files
        $output[\strtolower(\substr($entity,0,-4))] =\substr($entity,0,-4);
    }

    return $output;
}

function dateToString($date)
{
    return Carbon::parse($date)->toDateString();
}

function generate_date_with_extra_days(string $date = null,$days) {
    if(empty($date)) {
        $date = Carbon::now();
        $date->subDays($days);
        return $date;
    }
    $date=Carbon::parse($date);
    $date->subDays($days);

    return Carbon::parse($date)->toDateString();

}

function dateToday(){
    return Carbon::now()->toDateString();
}

/**
 * @param $supOrgId
 * @return false|string
 * @throws \Psr\Container\ContainerExceptionInterface
 * @throws \Psr\Container\NotFoundExceptionInterface
 */
function getBarcodeJson($supOrgId){
    // dd('ok');
    if(!file_exists(storage_path("barcodes/barcode-$supOrgId.json"))){
        $data =  json_encode([]);
    }else{
        $data = (File::get(storage_path("barcodes/barcode-$supOrgId.json")));
    }

    // DD($data, !session()->has('barcode'));

    if(!session()->has('barcode')){
        return $data;
    }

    $sessionCodes = call_user_func_array('array_merge',array_values(session()->get('barcode')));
    $newData = array_merge(json_decode($data,true),$sessionCodes);

    // dd($sessionCodes,array_values(session()->get('barcode')));

    // dd(array_values(session()->get('barcode')), $sessionCodes, json_decode($data,true),$newData);

    return json_encode($newData);
}

function get_fiscal_year_from_date($date_bs)
{
    $dateHelper = new DateHelper();
    if(strlen($date_bs) < 10){
        $date_split = explode('-',$date_bs);
        $year_bs = $date_split[0];
        $month_bs = $date_split[1];
        $new_day_bs = $date_split[2];
        // For month condition
        if(strlen($month_bs) === 1)
        {
            $month_bs = str_pad($month_bs,2,0,STR_PAD_LEFT);
        }else{
            $month_bs = $month_bs;
        }
        // For day condition
        if(strlen($new_day_bs) === 1){
            $new_day_bs = str_pad($new_day_bs,2,0,STR_PAD_LEFT);
        }else{
            $new_day_bs = $new_day_bs;
        }

        $date_bs = $year_bs .'-'. $month_bs .'-'. $new_day_bs;
        }

    return $dateHelper->fiscalYear($date_bs);
}

function ConvertToEnglishWords(float $number){
    if( $number == 0 || $number < 0)
        return '-';
    $no = floor($number);
    $rs = $no;
    $point = intval(round(($number - $no) * 100, 0)) ;
    $paisa = $point;
    $hundred = null;
    $digits_1 = strlen($no);

    $i = 0;
    $str = array();

    $words = array(
        0 =>" ",
        1 => "ONE",
        2 => "TWO",
        3 => "THREE",
        4 => "FOUR",
        5 => "FIVE",
        6 => "SIX",
        7 => "SEVEN",
        8 => "EIGHT",
        9 => "NINE",
        10 => "TEN",
        11 => "ELEVEN",
        12 => "TWELVE",
        13 => "THIRTEEN",
        14 => "FOURTEEN",
        15 => "FIFTEEN",
        16 => "SIXTEEN",
        17 => "SEVENTEEN",
        18 => "EIGHTEEN",
        19 => "NINETEEN",
        20 => "TWENTY",
        21 => "TWENTY ONE",
        22 => "TWENTY TWO",
        23 => "TWENTY THREE",
        24 => "TWENTY FOUR",
        25 => "TWENTY FIVE ",
        26 => "TWENTY SIX",
        27 => "TWENTY SEVEN",
        28 => "TWENTY EIGHT",
        29 => "TWENTY NINE",
        30 => "THIRTY",
        31 => "THIRTY ONE",
        32 => "THIRTY TWO",
        33 => "THIRTY THREE",
        34 => "THIRTY FOUR",
        35 => "THIRTY FIVE",
        36 => "THIRTY SIX",
        37 => "THIRTY SEVEN",
        38 => "THIRTY EIGHT",
        39 => "THIRTY NINE",
        40 => "FORTY", 
        41 => "FORTY ONE",
        42 => "FORTY TWO",
        43 => "FORTY THREE",
        44 => "FORTY FOUR",
        45 => "FORTY FIVE",
        46 => "FORTY SIX",
        47 => "FORTY SEVEN",
        48 => "FORTY EIGHT",
        49 => "FORTY NINE",
        50 => "FIFTY", 
        51 => "FIFTY ONE",
        52 => "FIFTY TWO",
        53 => "FIFTY THREE",
        54 => "FIFTY FOUR",
        55 => "FIFTY FIVE",
        56 => "FIFTY SIX",
        57 => "FIFTY SEVEN",
        58 => "FIFTY EIHT",
        59 => "FIFTY NINE",
        60 => "SIXTY", 
        61 => "SIXTY ONE", 
        62 => "SIXTY TWO", 
        63 => "SIXTY THREE", 
        64 => "SIXTY FOUR",
        65 => "SIXTY FIVE",
        66 => "SIXTY SIX",
        67 => "SIXTY SEVEN",
        68 => "SIXTY EIGHT",
        69 => "SIXTY NINE",
        70 => "SEVENTY", 
        71 => "SEVENTY ONE",
        72 => "SEVENTY TWO",
        73 => "SEVENTY THREE",
        74 => "SEVENTY FOUR",
        75 => "SEVENTY FIVE",
        76 => "SEVENTY SIX",
        77 => "SEVENTY SEVEN",
        78 => "SEVENTY EIGHT",
        79 => "SEVENTY NINE",
        80 => "EIGHTY", 
        81 => "EIGHTY ONE",
        82 => "EIGHTY TWO",
        83 => "EIGHTY THREE",
        84 => "EIGHTY FOUR",
        85 => "EIGHTY FIVE",
        86 => "EIGHTY SIX",
        87 => "EIGHTY SEVEN",
        88 => "EIGHTY EIGHT",
        89 => "EIGHTY NINE",
        90 => "NINETY",
        91 => "NINETY ONE",
        92 => "NINETY TWO",
        93 => "NINETY THREE",
        94 => "NINETY FOUR",
        95 => "NINETY FIVE",
        96 => "NINETY SIX",
        97 => "NINETY SEVEN",
        98 => "NINETY EIGHT",
        99 => "NINETY NINE",
        100 => "HUNDRED"
    );

    $digits = array('', 'HUNDRED','THOUSAND','LAKH', 'CRORE');
    
    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? '' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' ' : null;
            $str [] = ($number < 100) ? $words[$number] . " " . $digits[$counter] . $plural . " " . $hundred : $words[floor($number / 10) * 10] . " " . $words[$number % 10] . " " . $digits[$counter] . $plural . " " . $hundred;
        } else $str[] = null;
    }


        if($rs > 0 && $paisa >= 1){
            $str = array_reverse($str);
            $result = implode('', $str);
            $points = ($point) ? " " . $words[$point] . " "  : '';
            return $result . "RUPEES AND" . $points . " PAISA ONLY.";
        }elseif( $rs > 0 && $paisa < 1 ){
            $str = array_reverse($str);
            $result = implode('', $str);
            return $result . "RUPEES ONLY.";
        }elseif( $rs == 0 && $paisa == 0 ){
            return  "ZERO RUPEES AND ZERO PAISA ONLY.";
        }else{
            $str = array_reverse($str);
            $points = ($point) ? " " . $words[$point] . " "  : '';
            return  $points . " PAISA ONLY.";
        }
}

