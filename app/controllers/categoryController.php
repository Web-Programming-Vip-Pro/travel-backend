<?php

namespace App\Controllers;

include_once('app/models/categoryModel.php');
include_once('core/http/Container.php');
require_once('app/validators/cateValidate.php');
use App\Models\CategoryModel;
use Core\Http\BaseController;
use App\Validator\CateValidate;
class categoryController extends BaseController {
    private $category;
    private $validate;
    public function __construct(){
        $this->category = new CategoryModel();
        $this->validate = new CateValidate();
    }
    public function index()
    {
        $result = $this->category->get();
        $msg = [
            'status' =>'success',
            'msg'    => 'Get list places',
            'data'   =>  $result
        ];
        return $this->status(200,$msg);
    }
    public function postAdd(){
        $req = $_POST;
        $msgs = $this->validate->add($req);
        if(count($msgs) > 0){
            $msg = [
                'status'    => 'error',
                'msg'       => 'Some fielt not filled in',
                'data'      => $msgs
             ];
             return $this->status(422,$msg);
        } 
        $data = [
            'title'         => $req['title'],
            'description'   => $req['description']
        ];
        $result = $this->category->create($data);
        if($result == false){
            $msg=[
                'status'    =>  'error',
                'msg'       =>  'Add cate to database fail',
                'data'      =>  null
            ];
            return $this->status(500,$msg);
        }
        $msg=[
            'status'    =>'Created',
            'msg'       =>'Add cate to database success',
            'data'      => null
        ];
        return $this->status(200,$msg);
    }  
    public function getEdit(){
        $req = $_POST;
        $id = (int)$_REQUEST['id'];
        if($id ==0){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'Id not fill in',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $resultById = $this->category->get($id);
        if($resultById == false){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'Id not existed',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $msg = [
            'status'    =>  'success',
            'msg'       =>  'Get cate with id = '.$id,
            'data'      => $resultById
        ];
        return $this->status(200,$msg);
    }
    public function postEdit(){
        $req = $_POST;
        $id = (int)$_REQUEST['id'];
        if($id ==0){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'Id not fill in',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $resultById = $this->category->get($id);
        if($resultById == false){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'Id not existed',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $msgs = $this->validate->edit($req); 
        if(count($msgs) > 0){
            $msg = [
                'status'    => 'error',
                'msg'       => 'Some fielt not filled in',
                'data'      => $msgs
             ];
             return $this->status(422,$msg);
        } 
        $data = [
            'title'         => $req['title'],
            'description'   => $req['description']
        ];    
        $result = $this->category->update($id,$data);
        if($result == true){
            $msg = [
                'status'    => 'success', 
                'msg'       => 'Update cate success',
                'data'      => null
            ];
            return $this->status(200,$msg);
        }
        $msg = [
            'status'    => 'error', 
            'msg'       => 'Update cate error',
            'data'      => null
        ];
        return $this->status(500,$msg);
    }
    public function delete(){
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            $data=[
                'status'    => 'error',
                'msg'       =>  'Id not filled in',
                'data'      => null, 
            ];
            return $this->status(500,$data);
        }
        $resultGetById = $this->category->get($id);
        if($resultGetById == null){
            $msg = [
                'status'    => 'error',
                'msg'       =>  'Id not exactly',
                'data'      => null, 
            ];
            return $this->status(500,$msg);
        }
        $this->category->delete($id);
        $msg = [
            'status'    => 'success',
            'msg'       =>  'Delete user success',
            'data'      => null, 
        ];
        return $this->status(200,$msg);
    }
}
