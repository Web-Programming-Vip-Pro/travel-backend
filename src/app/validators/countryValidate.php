<?php
/**
 *  class validate user
 * 
 * 
*/
namespace App\Validator;

class CountryValidate{
    public function __construct(){
        return;
    }
    public function add($req){
        $msg = [];
        if (!isset($req['name'])) {
            array_push($msg, 'Vui lòng điền tên đất nước');
        }
        return $msg;

    }        
    public function edit($req){
        $msg = [];
        if (!isset($req['name'])) {
            array_push($msg, 'Vui lòng điền tên đất nước');
        }
        return $msg;
    }        
}
?>