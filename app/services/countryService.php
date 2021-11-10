<?php

namespace App\Services;

require_once('core/http/Container.php');
require_once('app/models/countryModel.php');
require_once('app/validators/countryValidate.php');
require_once('app/middleware/middleware.php');

use App\Models\CountryModel;
use App\Validator\CountryValidate;
use App\Middleware\Middleware;
use Core\Http\BaseController;
class CountryService
{
    private $country;
    private $validate;
    private $middleware;
    private $container;
    private $user;
    public function __construct()
    {
        $this->container    = new BaseController();
        $this->validate     = new countryValidate();
        $this->country      = new countryModel();
        $this->middleware   = new Middleware();
        $this->user         = $this->middleware->handleAdmin();
    }
    public function list(){
        if($this->user == false){
            return $this->container->status(401,"Unauthorized");
        }
        $result = $this->country->get();
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
            'name' => $req['name'],
        ];
        // image
        $result = $this->country->create($data);
        if($result == false){
            $msg= 'Add country to database fail';
            return $this->container->status(500,$msg);
        }
        $msg= 'Add country to database success';
        return $this->container->status(200,$msg);
    }
    // function get edit  country 
    public function getEdit($id){
        if($this->user == false){
            return $this->container->status(401,"Unauthorized");
        }
        $msgHandleId = $this->handleId($id);
        if($msgHandleId != false){
            return $this->container->status(500,$msgHandleId);
        }
        $msg = $this->country->get($id);
        return $this->container->status(200,$msg);
    }
    // function post edit country
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
            'name' => $req['name'],
        ]; 
        $result = $this->country->update($id,$data);
        if($result == true){
            $msg =  'Update country success';
            return $this->container->status(200,$msg);
        }
        $msg = 'Update country error';
        return $this->container->status(500,$msg);
    }
    // function delete country
    public function delete ($id){
        if($this->user == false){
            return $this->container->status(401,"Unauthorized");
        }
        $msgHandleId = $this->handleId($id);
        if($msgHandleId != false){
            return $this->container->status(500,$msgHandleId);
        }
        $this->country->delete($id);
        $msg = 'Delete country success';
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
        $resultGetById = $this->country->get($id);
        if($resultGetById == null){
            return  'Id not exactly';
        }
        return false;
    }
}
