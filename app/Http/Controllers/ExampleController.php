<?php

namespace App\Http\Controllers;

use App\Models\Banglore;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function saveData()
    {

        ini_set('memory_limit', '-1');

        $array = [];
             $model = Banglore::select('id','outlet_id')->where('id','<',100)->get()->toArray();
             foreach ($model as $value){
                 $singleModel = Banglore::where('id',$value['id'])->first();
                 $getpincode = $this->getAddress( (float)$singleModel->latitude,(float)$singleModel->longitude);
                 $array [] = array($getpincode['postal_code'],$getpincode['formatted_address']);
//                 ;
//                 $array [] = $getpincode['postal_code'];
//                 $singleModel->pincode = $getpincode['postal_code'];
//                 $singleModel->address = $getpincode['formatted_address'];
//                 $singleModel->save();
             }

             print_r($array);exit;

    }
    function getAddress($latitude,$longitude){
        if(!empty($latitude) && !empty($longitude)){
            //Send request and receive json data by address
            $geocodeFromLatLong = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&key=AIzaSyCxc382Y6izjaouKZD-WB5TY3YpC0rPkNs');
            $jsondata = json_decode($geocodeFromLatLong,true);
            $postalcode = $this->google_getPostalCode($jsondata);
            $address = array(
//                'country' => $this->google_getCountry($jsondata),
//                'province' => $this->google_getProvince($jsondata),
//                'city' => $this->google_getCity($jsondata),
//                'street' => $this->google_getStreet($jsondata),
                'postal_code' => $postalcode,
//                'country_code' => $this->google_getCountryCode($jsondata),
                'formatted_address' => $this->google_getAddress($jsondata),
            );
            if(!empty($address)){
                return $address;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    function google_getCity($jsondata) {
        return $this->Find_Long_Name_Given_Type("locality", $jsondata["results"][0]["address_components"]);
    }
    function google_getStreet($jsondata) {
        return $this->Find_Long_Name_Given_Type("street_number", $jsondata["results"][0]["address_components"]) . ' ' . $this->Find_Long_Name_Given_Type("route", $jsondata["results"][0]["address_components"]);
    }
    function google_getPostalCode($jsondata) {
        return $this->Find_Long_Name_Given_Type("postal_code", $jsondata["results"][0]["address_components"]);
    }

    function google_getCountry($jsondata) {
        return $this->Find_Long_Name_Given_Type("country", $jsondata["results"][0]["address_components"]);
    }
    function google_getProvince($jsondata) {
        return $this->Find_Long_Name_Given_Type("administrative_area_level_1", $jsondata["results"][0]["address_components"], true);
    }
    function Find_Long_Name_Given_Type($type, $array, $short_name = false) {
        if(!empty($array)){
            foreach( $array as $value) {
                if (in_array($type, $value["types"])) {
                    if ($short_name)
                        return $value["short_name"];
                    return $value["long_name"];
                }
            }
        }
    }

    function google_getCountryCode($jsondata) {
        return $this->Find_Long_Name_Given_Type("country", $jsondata["results"][0]["address_components"], true);
    }
    function google_getAddress($jsondata) {
        return $jsondata["results"][0]["formatted_address"];
    }
    //
}
