<?php

namespace App\Services;

require_once('core/http/Container.php');
require_once('app/models/placeModel.php');
require_once('app/validators/placeValidate.php');
require_once('app/middleware/middleware.php');

use App\Models\PlaceModel;
use App\Middleware\Middleware;
use App\Validator\PlaceValidate;
use Core\Http\BaseController;
class PlaceService
{
    private $place;
    private $middleware;
    private $validate;
    private $container;
    private $user;
    public function __construct()
    {
        $this->container    = new BaseController();
        $this->validate     = new PlaceValidate();
        $this->place        = new PlaceModel();
        $this->middleware   = new Middleware();
        $this->user         = $this->middleware->handleAgency();
    }
    public function list(){
        $result = $this->place->get();
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
            'city_id'       => $req['city_id'],
            'type'          => $req['type'],
            'price'         => $req['price'],
            'images'         => 'image',
            'location'      => $req['location'],
            'stars'         => 0.0,
            'reviews'       => 0,
            'status'        => 0,
            'author_id'     => $this->user->id,
        ];
        // image
        $result = $this->place->create($data);
        if($result == false){
            $msg= 'Add place to database fail';
            return $this->container->status(500,$msg);
        }
        $msg= 'Add place to database success';
        return $this->container->status(200,$msg);
    }
    // function get edit  place 
    public function getEdit($id){
        if($this->user == false){
            return $this->container->status(401,"Unauthorized");
        }
        $msgHandleId = $this->handleId($id);
        if($msgHandleId != false){
            return $this->container->status(500,$msgHandleId);
        }
        $msg = $this->place->get($id);
        return $this->container->status(200,$msg);
    }
    // function post edit place
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
            'city_id'       => $req['city_id'],
            'type'          => $req['type'],
            'price'         => $req['price'],
            'images'         => 'image',
            'location'      => $req['location'],
            'stars'         => 0.0,
            'reviews'       => 0,
            'status'        => 0,
            'author_id'     => $this->user->id,
        ];
        $result = $this->place->update($id,$data);
        if($result == true){
            $msg =  'Update place success';
            return $this->container->status(200,$msg);
        }
        $msg = 'Update place error';
        return $this->container->status(500,$msg);
    }
    // function delete place
    public function delete ($id){
        if($this->user == false){
            return $this->container->status(401,"Unauthorized");
        }
        $msgHandleId = $this->handleId($id);
        if($msgHandleId != false){
            return $this->container->status(500,$msgHandleId);
        }
        $this->place->delete($id);
        $msg = 'Delete place success';
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
        $resultGetById = $this->place->get($id);
        if($resultGetById == null){
            return  'Id not exactly';
        }
        return false;
    }
}
