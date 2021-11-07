<?php
/**
 *  class validate user
 * 
 * 
*/
namespace App\Validator;

class ReviewValidate{
    public function __construct(){
        return;
    }
    public function add($req){
        $msg = [];
        if(!$req['rate']){
            array_push($msg,'Please choise rate');
        }
        if(!$req['message'] || trim($req['message'])==''){
            array_push($msg,'Please fill out comment');
        }
        return $msg;
    }          
}
?>