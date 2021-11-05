<?php

namespace App\Controllers;

include_once('app/models/blogModel.php');
include_once('core/http/Container.php');
require_once('app/validators/blogValidate.php');

use App\Models\BlogModel;
use Core\Http\BaseController;
use App\Validator\BlogValidate;
class blogController extends BaseController {
    private $blog;
    private $validate;
    public function __construct(){
        $this->blog = new BlogModel();
        $this->validate = new BlogValidate();
    }
    public function index()
    {
        $result = $this->blog->get();
        $msg = [
            'status'    => 'success',
            'msg'       => 'Get blogs',
            'data'      => $result
        ];
        return $this->status(200,$msg);
    }
    public function postAdd(){
        $req = $_POST;
        $msgs = $this->validate->add($req);
        if(count($msgs) > 0){
            $msg = [
                'status'    => 'error',
                'msg'       => 'Some fielt not fill in',
                'data'      => null
            ];
            return $this->status(422,$msg);
        } 
        $data = [
            'name'          => $req['name'],
            'title'         => $req['title'],
            'description'   => $req['description'],
            'author_id'     => 1,
            'category_id'   => $req['category_id'],
            'status'        => 0,
        ];
        if(isset($_FILE['image']) && $_FILE['image']['error'] == 0){
            $target_dir = 'public/images/';
            $target_file = $target_dir . basename($_FILE['image']['name']);
            
        }
        $result = $this->blog->create($data);
        if($result == null){
            $msg = [
                'status'    => 'error',
                'msg'       => 'Add blog error',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $msg = [
            'status'    => 'success',
            'msg'       => 'Add blog success',
            'data'      => $result
        ];
        return $this->status(200,$msg);
    }  
    public function getEdit(){
        $req = $_POST;
        $id = (int)$_REQUEST['id'];
        if($id ==0){
            echo " Vui lòng nhập id";
            return;
        }
        $resultById = $this->blog->get($id);
        if($resultById == null){
            echo " Id không tồn tại";
            return;
        }
        echo '<pre>';
        print_r($resultById);
        echo '</pre>';
        return;
    }
    public function postEdit(){
        $req = $_POST;
        $id = (int)$_REQUEST['id'];
        $msg =[];
        if($id ==0){
            echo " Vui lòng nhập id";
            return;
        }
        $resultById = $this->blog->get($id);
        if($resultById == null){
            echo " Id không tồn tại";
            return;
        }
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
        if(count($msg) > 0){
            echo "Một số trường chưa được điền đầy đủ";
            return;
        } 
        $data = [
            'name' => $req['name'],
            'title' => $req['title'],
            'description' => $req['description'],
            'author_id' => 1,
            'category_id' => $req['category_id'],
            'status' => 0,

        ];
        $result = $this->blog->update($id,$data);
        echo "Update blog success";
        return;
    }
    public function delete(){
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            echo "Vui lòng nhập Id";
            return;
        }
        $resultGetById = $this->blog->get($id);
        if($resultGetById == null){
            echo "Id khong tồn tại";
            return;
        }
        $this->blog->delete($id);
        print_r("Delete blog success");
        return ;
    }
}