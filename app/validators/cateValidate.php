<?php
/**
 *  class validate user
 * 
 * 
*/
namespace App\Validator;

class CateValidate{
    public function __construct(){
        return;
    }
    public function add($req){
        $msg = [];
        if (!isset($req['title'])) {
            array_push($msg, 'Vui lòng điền tên category');
        }
        if (!isset($req['description'])) {
            array_push($msg, 'Vui lòng điền miêu tả');
        }
        return $msg;

    }        
    public function edit($req){
        $msg = [];
        if (!isset($req['title'])) {
            array_push($msg, 'Vui lòng điền tên category');
        }
        if (!isset($req['description'])) {
            array_push($msg, 'Vui lòng điền miêu tả');
        }
        return $msg;
    }       
}
?>