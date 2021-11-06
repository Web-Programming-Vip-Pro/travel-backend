<?php

namespace App\Controllers;

include_once('app/models/cityModel.php');
include_once('core/http/Container.php');
require_once('app/validators/cityValidate.php');

use App\Models\CityModel;
use Core\Http\BaseController;
use App\Validator\CityValidate;
class cityController extends BaseController{
    private $city;
    private $validate;
    public function __construct(){
        $this->city = new CityModel();
        $this->validate = new CityValidate();
    }
    public function index()
    {
        $result = $this->city->get();
        $msgs = [
            'status'    =>  'success',
            'msg'       =>  'Get city',
            'data'      =>  $result
        ];
        return $this->status(200,$msgs);
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
            'name'          => $req['name'],
            'country_id'    => $req['country_id'],
            'description'   => $req['description'],
            'total_places'  => 0,
            'image_cover'   => 'image',
        ];
        $result = $this->city->create($data);
        if($result == false){
            $msg = [
                'status'    => 'error',
                'msg'       => 'Error add city',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $msg = [    
            'status'    => ' success',
            'msg'       => 'Add city success',
            'data'      => null
        ]; 
        return $this->status(200,$msg);
    }  
    public function getEdit(){
        $req = $_POST;
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'Id not fill in',
                'data'      =>  null
            ];
            return $this->status(500,$msg);
        }
        $resultById = $this->city->get($id);
        if($resultById == null){
            $msg = [
                'status'    => 'error',
                'msg'       => 'Id not existed',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $msg = [
            'status'    =>  'success',
            'msg'       =>  'Get city with id = '.$id,
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
                'data'      =>  null
            ];
            return $this->status(500,$msg);
        }
        $resultById = $this->city->get($id);
        if($resultById == false){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'City not exist',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $msgs = $this->validate->edit($req);
        if(count($msgs) > 0){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'Some fielt not pill in',
                'data'      => $msgs
            ];
            return $this->status(422,$msg);
        } 
        $data = [
            'name'          => $req['name'],
            'country_id'    => $req['country_id'],
            'description'   => $req['description'],
            'total_places'  => 0,
            'image_cover'   => 'image',
        ];
        $result = $this->city->update($id,$data);
        if($result == false){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'update city fail',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $msg = [
            'status'    =>  'success',
            'msg'       =>  'update city success',
            'data'      => null
        ];
        return $this->status(200,$msg);
    }
    public function delete(){
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'Id not fill in',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $resultGetById = $this->city->get($id);
        if($resultGetById == null){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'Id not existed',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $result = $this->city->delete($id);
        if($result == false){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'delete city fail',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $msg = [
            'status'    =>  'success',
            'msg'       =>  'Delete city success',
            'data'      => null
        ];
        return $this->status(200,$msg);
    }
}