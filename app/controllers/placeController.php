<?php

namespace App\Controllers;

include_once('app/models/placeModel.php');
include_once('app/models/cityModel.php');
include_once('core/http/Container.php');
require_once('app/validators/placeValidate.php');

use App\Models\PlaceModel;
use App\Models\CityModel;
use Core\Http\BaseController;
use App\Validator\PlaceValidate;
class placeController extends BaseController{
    private $place;
    private $validate;
    private $city;
    public function __construct(){
        $this->place = new PlaceModel();
        $this->validate = new PlaceValidate();
        $this->city = new CityModel();
    }
    public function index()
    {
       $result = $this->place->get();
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
        if(count($msgs) >0){
            $msg = [
                'status'    => 'error',
                'msg'       => 'Some fielt not filled in',
                'data'      => $msgs
             ];
             return $this->status(422,$msg);
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
            'author_id'     => 1,
        ];
        $result = $this->place->create($data);
        if($result == false){
            $msg=[
                'status'    =>  'error',
                'msg'       =>  'Add place to database fail',
                'data'      =>  null
            ];
            return $this->status(500,$msg);
        }
        $msg=[
            'status'    =>'Created',
            'msg'       =>'Add place to database success',
            'data'      => null
        ];
        return $this->status(200,$msg);
    }  
    public function getEdit(){
        $id = (int)$_REQUEST['id'];
        if($id == 0 ){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'Id not fill in',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $result = $this->place->get($id);
        if($result == false){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'Id not existed',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $msg = [
            'status'    =>  'success',
            'msg'       =>  'Get user by id',
            'data'      => $result
        ];
        return $this->status(200,$msg);
        
    }
    public function postEdit(){
        $req = $_POST;
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            $msg =[
                'status'    => 'error',
                'msg'       =>  'Id not filled in',
                'data'      => null, 
            ];
            return $this->status(500,$msg);
        }
        $resultById = $this->place->get($id);
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
            'city_id'       => $req['city_id'],
            'type'          => $req['type'],
            'price'         => $req['price'],
            'images'         => 'image',
            'location'      => $req['location'],
            'stars'         => 0.0,
            'reviews'       => 0,
            'status'        => 0,
            'author_id'     => 1,
        ];
        $result = $this->place->update($id,$data);
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
                'data'      => null, 
            ];
            return $this->status(500,$data);
        }
        $resultGetById = $this->place->get($id);
        if($resultGetById == false){
            $msg = [
                'status'    => 'error',
                'msg'       =>  'Id not exactly',
                'data'      => null, 
            ];
            return $this->status(500,$msg);
        }
        $this->place->delete($id);
        $msg = [
            'status'    => 'success',
            'msg'       =>  'Delete user success',
            'data'      => null, 
        ];
        return $this->status(200,$msg);
    }
}
