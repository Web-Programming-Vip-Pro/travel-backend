<?php
/**
 *  class validate user
 * 
 * 
*/
namespace App\Validator;

class PlaceValidate{
    public function __construct(){
        return;
    }
    public function add($req){
        $msg = [];
        if(!$req['title'] || trim($req['title']) == "" ){
            array_push($msg,'Please fill out title');
        }
        if(!$req['price'] || trim($req['price']) == "" ){
            array_push($msg,'Please fill out price');
        }
        if(!$req['location'] || trim($req['location']) == "" ){
            array_push($msg,'Please fill out location');
        }
        if(!$req['city_id'] || trim($req['city_id']) == "" ){
            array_push($msg,'Please fill out city');
        }
        return $msg;

    }        
    public function edit($req){
        $msg = [];
        if(!$req['title'] || trim($req['title']) == "" ){
            array_push($msg,'Please fill out title');
        }
        if(!$req['price'] || trim($req['price']) == "" ){
            array_push($msg,'Please fill out price');
        }
        if(!$req['location'] || trim($req['location']) == "" ){
            array_push($msg,'Please fill out location');
        }
        if(!$req['city_id'] || trim($req['city_id']) == "" ){
            array_push($msg,'Please fill out city');
        }
        return $msg;
    }       
}
?>