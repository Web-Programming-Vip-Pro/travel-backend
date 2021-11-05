<?php

namespace App\Controllers;

include_once('app/models/categoryModel.php');
include_once('core/http/Container.php');

use App\Models\CategoryModel;
use Core\Http\BaseController;
class categoryController extends BaseController {
    private $category;
    public function __construct(){
        $this->category = new CategoryModel();
    }
    public function index()
    {
        $result = $this->category->get();
        $result = json_encode($result);
        echo '<pre>';
        print_r($result);
        echo '</pre>';
        return;
    }
    public function postAdd(){
        $req = $_POST;
        $msg = [];
        if (!isset($req['title'])) {
            array_push($msg, 'Vui lòng điền tên category');
        }
        if (!isset($req['description'])) {
            array_push($msg, 'Vui lòng điền miêu tả');
        }
        if(count($msg) > 0){
            echo "Một số trường chưa được điền đầy đủ";
            return;
        } 
        $data = [
            'title'         => $req['title'],
            'description'   => $req['description']
        ];
        $result = $this->category->create($data);
        if($result == null){
            echo "Error add category";
            return;
        }
        echo "Add category success";
        return;
    }  
    public function getEdit(){
        $req = $_POST;
        $id = (int)$_REQUEST['id'];
        if($id ==0){
            echo " Vui lòng nhập id";
            return;
        }
        $resultById = $this->category->get($id);
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
        $resultById = $this->category->get($id);
        if($resultById == null){
            echo " Id không tồn tại";
            return;
        }
        if (!isset($req['title'])) {
            array_push($msg, 'Vui lòng điền tên category');
        }
        if (!isset($req['description'])) {
            array_push($msg, 'Vui lòng điền miêu tả');
        }
        if(count($msg) > 0){
            echo "Một số trường chưa được điền đầy đủ";
            return;
        } 
        $data = [
            'title'         => $req['title'],
            'description'   => $req['description']
        ];    
        $result = $this->category->update($id,$data);
        echo "Update category success";
        return;
    }
    public function delete(){
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            echo "Vui lòng nhập Id";
            return;
        }
        $resultGetById = $this->category->get($id);
        if($resultGetById == null){
            echo "Id không tồn tại";
            return;
        }
        $this->category->delete($id);
        print_r("Delete category success");
        return ;
    }
}
