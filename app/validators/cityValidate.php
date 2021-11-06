<?php
/**
 *  class validate user
 * 
 * 
*/
namespace App\Validator;

class CityValidate{
    public function __construct(){
        return;
    }
    public function add($req){
        $msg = [];
        if (!$req['name'] || trim($req['name']) == '') {
            array_push($msg, 'Please fill out name');
        }
        if (!$req['country_id'] || trim($req['country_id']) == '') {
            array_push($msg, 'Please fill out name');
        }
        if (!$req['description'] || trim($req['description']) == '') {
            array_push($msg, 'Please fill out name');
        }
        return $msg;

    }        
    public function edit($req){
        $msg = [];
        if (!isset($req['name']) || trim($req['name']) == '') {
            array_push($msg, 'Please fill out name');
        }
        if (!isset($req['country_id']) || trim($req['country_id']) == '' ) {
            array_push($msg, 'Please fill out country_id');
        }
        if (!isset($req['description']) || trim($req['description']) == '') {
            array_push($msg, 'Please fill out description');
        }
        return $msg;
    }       
}
?>