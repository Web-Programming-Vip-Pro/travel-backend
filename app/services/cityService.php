<?php

namespace App\Services;

require_once('core/http/Container.php');
require_once('app/models/cityModel.php');
require_once('app/validators/cityValidate.php');
require_once('app/middleware/middleware.php');

use App\Models\CityModel;
use App\Validator\CityValidate;
use App\Middleware\Middleware;
use Core\Http\BaseController;
class CityService
{
    private $city;
    private $validate;
    private $middleware;
    private $container;
    private $user;
    public function __construct()
    {
        $this->container    = new BaseController();
        $this->validate     = new CityValidate();
        $this->city         = new CityModel();
        $this->middleware   = new Middleware();
        $this->user         = $this->middleware->handleAdmin();
    }
    public function list(){
        $result = $this->city->get();
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
            'name'          => $req['name'],
            'country_id'    => $req['country_id'],
            'description'   => $req['description'],
            'total_places'  => 0,
            'image_cover'   => 'image',
        ];
        // image
        $result = $this->city->create($data);
        if($result == false){
            $msg= 'Add city to database fail';
            return $this->container->status(500,$msg);
        }
        $msg= 'Add city to database success';
        return $this->container->status(200,$msg);
    }
    // function get edit  city 
    public function getEdit($id){
        if($this->user == false){
            return $this->container->status(401,"Unauthorized");
        }
        $msgHandleId = $this->handleId($id);
        if($msgHandleId != false){
            return $this->container->status(500,$msgHandleId);
        }
        $msg = $this->city->get($id);
        return $this->container->status(200,$msg);
    }
    // function post edit city
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
            'name'          => $req['name'],
            'country_id'    => $req['country_id'],
            'description'   => $req['description'],
            'total_places'  => 0,
            'image_cover'   => 'image',
        ];
        $result = $this->city->update($id,$data);
        if($result == true){
            $msg =  'Update city success';
            return $this->container->status(200,$msg);
        }
        $msg = 'Update city error';
        return $this->container->status(500,$msg);
    }
    // function delete city
    public function delete ($id){
        if($this->user == false){
            return $this->container->status(401,"Unauthorized");
        }
        $msgHandleId = $this->handleId($id);
        if($msgHandleId != false){
            return $this->container->status(500,$msgHandleId);
        }
        $this->city->delete($id);
        $msg = 'Delete city success';
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
        $resultGetById = $this->city->get($id);
        if($resultGetById == null){
            return  'Id not exactly';
        }
        return false;
    }
}
