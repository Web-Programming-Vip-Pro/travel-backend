<?php
/**
 *  class validate user
 * 
 * 
*/
namespace App\Validator;

class BlogValidate{
    public function __construct(){
        return;
    }
    public function add($req){
        $msg = [];
        if (!isset($req['title'])) {
            array_push($msg, 'Please fill out title');
        }
        if (!isset($req['description'])) {
            array_push($msg, 'Please fill out description');
        }
        if (!isset($req['content'])) {
            array_push($msg, 'Please fill out content');
        }
        if (!isset($req['category_id'])) {
            array_push($msg, 'Please fill out category id');
        }
        return $msg;

    }        
    public function edit($req){
        $msg = [];
        if (!isset($req['title'])) {
            array_push($msg, 'Please fill out title');
        }
        if (!isset($req['description'])) {
            array_push($msg, 'Please fill out description');
        }
        if (!isset($req['content'])) {
            array_push($msg, 'Please fill out content');
        }
        if (!isset($req['category_id'])) {
            array_push($msg, 'Please fill out category id');
        }
        return $msg;
    }       
}
?>