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
            array_push($msg, 'Vui lòng điền tiêu đề');
        }
        if (!isset($req['description'])) {
            array_push($msg, 'Vui lòng điền miêu tả');
        }
        if (!isset($req['content'])) {
            array_push($msg, 'Vui lòng điền nội dung');
        }
        if (!isset($req['category_id'])) {
            array_push($msg, 'Vui lòng thêm category');
        }
        return $msg;

    }        
    public function edit($req){
        $msg = [];
        if (!isset($req['title'])) {
            array_push($msg, 'Vui lòng điền tiêu đề');
        }
        if (!isset($req['description'])) {
            array_push($msg, 'Vui lòng điền miêu tả');
        }
        if (!isset($req['content'])) {
            array_push($msg, 'Vui lòng điền nội dung');
        }
        if (!isset($req['category_id'])) {
            array_push($msg, 'Vui lòng thêm category');
        }
        return $msg;
    }       
}
?>