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
                'status'    =>  'error',
                'msg'       => "Some fielt not fill in"
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
        if($result == null){
            $msg = [
                'status'    =>  'error',
                'msg'   =>  'Error add city'
            ];
            return $this->status(500,$msg);
        }
        $msg = [    
            'status'    => ' success',
            'msg'       => 'Add city success'
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
                'status'    =>  'error',
                'msg'       =>  'Id not existed',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $msg = [
            'status'    =>  'success',
            'msg'   =>  $resultById
        ];
        return $this->status(200,$msg);
    }
    public function postEdit(){
        $req = $_POST;
        $id = (int)$_REQUEST['id'];
        $msgs = $this->validate->edit($req);
        if($id ==0){
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
                'status'    =>  'error',
                'msg'       =>  'Id not exist',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        if (!isset($req['name'])) {
            array_push($msg, 'Vui lòng điền tên đất nước');
        }
        if(count($msg) > 0){
            echo "Một số trường chưa được điền đầy đủ";
            return;
        } 
        $data = [
            'name'          => $req['name'],
            'country_id'    => $req['country_id'],
            'description'   => $req['description'],
            'total_places'  => 0,
            'image_cover'   => 'image',
        ];  
        $result = $this->city->update($id,$data);
        $msg = [
            'status'    =>  'success',
            'msg'       =>  'update city success',
            'data'      => null
        ];
        return $this->status(500,$msg);
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
        $this->city->delete($id);
        $msg = [
            'status'    =>  'success',
            'msg'       =>  'Delete city success',
            'data'      => null
        ];
        return $this->status(200,$msg);
    }
}