<?php

namespace App\Services;

require_once('core/http/Container.php');
require_once('app/models/categoryModel.php');
require_once('app/validators/cateValidate.php');
require_once('app/middleware/middleware.php');

use App\Models\CategoryModel;
use App\Validator\CateValidate;
use Core\Http\BaseController;
use App\Middleware\Middleware;
class CategoryService
{
    private $category;
    private $validate;
    private $container;
    private $middleware;
    private $user;
    public function __construct()
    {
        $this->container    = new BaseController();
        $this->validate     = new CateValidate();
        $this->category     = new CategoryModel();
        $this->middleware   = new Middleware();
        $this->user = $this->middleware->handleAdmin();
    }
    public function list($req){
        $page = isset($req['page']) ? (int)($req['page']) : 0;
        $limit = isset($req['limit']) ? (int)($req['limit']) : 20;
        $result = $this->category->get(-1,$page,$limit);
        return $this->container->status(200,$result);
    }
    public function add($req)
    {
        if($this->user == false){
            return $this->container->status(401,"Unauthorized");
        }
        $msgs = $this->handleValidator($req,'add');
        if($msgs != false){
            return $this->container->status(422,$msgs);
        }
        $data = [
            'title'         => $req['title'],
            'description'   => $req['description']
        ];
        $result = $this->category->create($data);
        if($result == false){
            $msg= 'Add cate to database fail';
            return $this->container->status(500,$msg);
        }
        $msg= 'Add cate to database success';
        return $this->container->status(200,$msg);
    }
    // function get edit  category 
    public function getEdit($id){
        if($this->user == false){
            return $this->container->status(401,"Unauthorized");
        }
        $msgHandleId = $this->handleId($id);
        if($msgHandleId != false){
            return $this->container->status(500,$msgHandleId);
        }
        $msg = $this->category->get($id);
        return $this->container->status(200,$msg);
    }
    // function post edit category
    public function postEdit($id,$req)
    {
        if($this->user == false){
            return $this->container->status(401,"Unauthorized");
        }
        $msgHandleId = $this->handleId($id);
        if($msgHandleId != false){
            return $this->container->status(500,$msgHandleId);
        }
        $msgs = $this->handleValidator($req,'edit');
        if($msgs != false){
            return $this->container->status(422,$msgs);
        }
        $data = [
            'title'         => $req['title'],
            'description'   => $req['description']
        ];
        $result = $this->category->update($id,$data);
        if($result == true){
            $msg =  'Update cate success';
            return $this->container->status(200,$msg);
        }
        $msg = 'Update cate error';
        return $this->container->status(500,$msg);
    }
    // function delete category
    public function delete ($id){
        if($this->user == false){
            return $this->container->status(401,"Unauthorized");
        }
        $msgHandleId = $this->handleId($id);
        if($msgHandleId != false){
            return $this->container->status(500,$msgHandleId);
        }
        $this->category->delete($id);
        $msg = 'Delete cate success';
        return $this->container->status(200,$msg);
    }
    // fucntion handle validate 
    public function handleValidator($req,$action){
        $msgs = null;
        if($action == 'add'){
            $msgs = $this->validate->add($req); 
        }else{
            $msgs = $this->validate->edit($req); 
        }
        if(count($msgs) > 0){
            return $msgs;
        } 
        return false;
    }
    // fucntion handle id if not fill or not exact
    public function handleId($id){
        if($id == 0){
            return 'Id not fill in';
        }
        $resultGetById = $this->category->get($id);
        if($resultGetById == null){
            return  'Id not exactly';
        }
        return false;
    }
}
