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
        $msg = $result;
        return $this->status(200,$msg);
    }
    public function postAdd(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $msgs = $this->validate->add($req);
        if(count($msgs) > 0){
            $msg = 'Some fielt not filled in';
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
            $msg= 'Add blog to database fail';
            return $this->status(500,$msg);
        }
        $msg= 'Add blog to database success';
        return $this->status(200,$msg);
    }  
    public function getEdit(){
        $id = (int)$_REQUEST['id'];
        if($id ==0){
            $msg = 'Id not fill in';
            return $this->status(500,$msg);
        }
        $resultById = $this->blog->get($id);
        if($resultById == false){
            $msg = 'Id not existed';
            return $this->status(500,$msg);
        }
        $msg = $resultById;
        return $this->status(200,$msg);
    }
    public function postEdit(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $id = (int)$_REQUEST['id'];
        if($id ==0){
            $msg =   'Id not fill in';
            return $this->status(500,$msg);
        }
        $resultById = $this->blog->get($id);
        if($resultById == false){
            $msg = 'Id not existed';
            return $this->status(500,$msg);
        }
        $msgs = $this->validate->edit($req);
        if(count($msgs) > 0){
            $msg =  'Some fielt not filled in';
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
            $msg =  'Update blog success';
            return $this->status(200,$msg);
        }
        $msg = 'Update blog error';
        return $this->status(500,$msg);
    }
    public function delete(){
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            $msg =   'Id not fill in';
            return $this->status(500,$msg);
        }
        $resultGetById = $this->blog->get($id);
        if($resultGetById == false){
            $msg =   'Id not exactly';
            return $this->status(500,$msg);
        }
        $this->blog->delete($id);
        $msg = 'Delete blog success';
        return $this->status(200,$msg);
    }
}