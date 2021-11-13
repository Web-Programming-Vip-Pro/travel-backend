<?php

namespace App\Services;

require_once('core/http/Container.php');
require_once('app/models/blogModel.php');
require_once('app/validators/blogValidate.php');
require_once('app/middleware/middleware.php');

use App\Models\BlogModel;
use App\Validator\BlogValidate;
use App\Middleware\Middleware;
use Core\Http\BaseController;
class blogService
{
    private $blog;
    private $validate;
    private $middleware;
    private $container;
    private $user;
    public function __construct()
    {
        $this->container    = new BaseController();
        $this->validate     = new BlogValidate();
        $this->blog         = new blogModel();
        $this->middleware   = new Middleware();
        $this->user         = $this->middleware->handleAdmin();
    }
    public function list(){
        $result = $this->blog->get();
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
            'content'       => $req['content'],
            'description'   => $req['description'],
            'author_id'     => 1,
            'category_id'   => $req['category_id'],
            'status'        => 0,
        ];
        // image
        $result = $this->blog->create($data);
        if($result == false){
            $msg= 'Add blog to database fail';
            return $this->container->status(500,$msg);
        }
        $msg= 'Add blog to database success';
        return $this->container->status(200,$msg);
    }
    // function get edit  blog 
    public function getEdit($id){
        if($this->user == false){
            return $this->container->status(401,"Unauthorized");
        }
        $msgHandleId = $this->handleId($id);
        if($msgHandleId != false){
            return $this->container->status(500,$msgHandleId);
        }
        $msg = $this->blog->get($id);
        return $this->container->status(200,$msg);
    }
    // function post edit blog
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
            'content'       => $req['content'],
            'description'   => $req['description'],
            'author_id'     => 1,
            'category_id'   => $req['category_id'],
            'status'        => 0,
        ];
        $result = $this->blog->update($id,$data);
        if($result == true){
            $msg =  'Update blog success';
            return $this->container->status(200,$msg);
        }
        $msg = 'Update blog error';
        return $this->container->status(500,$msg);
    }
    // function delete blog
    public function delete ($id){
        if($this->user == false){
            return $this->container->status(401,"Unauthorized");
        }
        $msgHandleId = $this->handleId($id);
        if($msgHandleId != false){
            return $this->container->status(500,$msgHandleId);
        }
        $this->blog->delete($id);
        $msg = 'Delete blog success';
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
        $resultGetById = $this->blog->get($id);
        if($resultGetById == null){
            return  'Id not exactly';
        }
        return false;
    }
}
