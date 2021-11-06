<?php

namespace App\Controllers;

include_once('app/models/countryModel.php');
include_once('core/http/Container.php');
require_once('app/validators/countryValidate.php');

use App\Models\CountryModel;
use Core\Http\BaseController;
use App\Validator\CountryValidate;
class countryController extends BaseController{
    private $country;
    private $validate;
    public function __construct(){
        $this->country = new CountryModel();
        $this->validate = new CountryValidate();
    }
    public function index()
    {
        $result = $this->country->get();
        $msg = [
            'status' =>'success',
            'msg'    => 'Get list country',
            'data'   =>  $result
        ];
        return $this->status(200,$msg);
    }
    public function postAdd(){
        $req = $_POST;
        $msgs = $this->validate->add($req);
        if(count($msgs) > 0){
            $msg = [
                'status'    => 'error',
                'msg'       => 'Some fielt not filled in',
                'data'      => $msgs
             ];
             return $this->status(422,$msg);
        } 
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
            'name' => $req['name'],
        ];
        $result = $this->country->create($data);
        if($result == false){
            $msg=[
                'status'    =>  'error',
                'msg'       =>  'Add country to database fail',
                'data'      =>  null
            ];
            return $this->status(500,$msg);
        }
        $msg=[
            'status'    =>'Created',
            'msg'       =>'Add country to database success',
            'data'      => null
        ];
        return $this->status(200,$msg);
    }  
    public function getEdit(){
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
        $resultById = $this->country->get($id);
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
            'msg'       =>  'Get country with id = '.$id,
            'data'      => $resultById
        ];
        return $this->status(200,$msg);
    }
    public function postEdit(){
        $req = $_POST;
        $id = (int)$_REQUEST['id'];
        if($id ==0){
            $msg =[
                'status'    => 'error',
                'msg'       =>  'Id not filled in',
                'data'      => null, 
            ];
            return $this->status(500,$msg);
        }
        $resultById = $this->country->get($id);
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
            $msg = [
                'status'    => 'error',
                'msg'       => 'Some fielt not filled in',
                'data'      => $msgs
             ];
             return $this->status(422,$msg);
        } 
        $data = [
            'name' => $req['name'],
        ];    
        $result = $this->country->update($id,$data);
        if($result == true){
            $msg = [
                'status'    => 'success', 
                'msg'       => 'Update country success',
                'data'      => null
            ];
            return $this->status(200,$msg);
        }
        $msg = [
            'status'    => 'error', 
            'msg'       => 'Update country error',
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
        $resultGetById = $this->country->get($id);
        if($resultGetById == null){
            $msg = [
                'status'    => 'error',
                'msg'       =>  'Id not exactly',
                'data'      => null, 
            ];
            return $this->status(500,$msg);
        }
        $this->country->delete($id);
        $msg = [
            'status'    => 'success',
            'msg'       =>  'Delete country success',
            'data'      => null, 
        ];
        return $this->status(200,$msg);
    }
}
