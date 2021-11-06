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
                'data'      => $msgs
            ];
            return $this->status(422,$msg);
        } 
        $data = [
            'title'         => $req['title'],
            'content'       => $req['content'],
            'description'   => $req['description'],
            'author_id'     => 1,
            'category_id'   => $req['category_id'],
            'status'        => 0,
        ];
        /* if(isset($_FILE['image']) && $_FILE['image']['error'] == 0){
            $target_dir = 'public/images/';
            $target_file = $target_dir . basename($_FILE['image']['name']);
            
        } */
        $result = $this->blog->create($data);
        if($result == false){
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
        $id = (int)$_REQUEST['id'];
        if($id ==0){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'Id not fill in',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $resultById = $this->blog->get($id);
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
            'msg'       =>  'Get blog by id',
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
        $resultById = $this->blog->get($id);
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
            $msg=[
                'status'    => 'error',
                'msg'       => 'Some field not fill in',
                'data'      => $msgs
            ];
            return $this->status(422,$msg);
        } 
        $data = [
            'title'         => $req['title'],
            'content'       => $req['content'],
            'description'   => $req['description'],
            'author_id'     => 1,
            'category_id'   => $req['category_id'],
            'status'        => 0,
        ];
        $result = $this->blog->update($id,$data);
        if($result == true){
            $msg = [
                'status'    => 'success', 
                'msg'       => 'Update user success',
                'data'      => null
            ];
            return $this->status(200,$msg);
        }
        $msg = [
            'status'    => 'error', 
            'msg'       => 'Update user error',
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
                'data'      => null
            ];
            return $this->status(500,$data);
        }
        $resultGetById = $this->blog->get($id);
        if($resultGetById == false){
            $msg = [
                'status'    => 'error',
                'msg'       =>  'Id not exactly',
                'data'      => null, 
            ];
            return $this->status(500,$msg);
        }
        $this->blog->delete($id);
        $msg = [
            'status'    => 'success',
            'msg'       =>  'Delete blog success',
            'data'      => null, 
        ];
        return $this->status(200,$msg);
    }
}