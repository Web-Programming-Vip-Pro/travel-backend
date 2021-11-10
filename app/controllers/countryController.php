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
            'name' => $req['name'],
        ];
        $result = $this->country->create($data);
        if($result == false){
            $msg= 'Add country to database fail';
            return $this->status(500,$msg);
        }
        $msg= 'Add country to database success';
        return $this->status(200,$msg);
    }  
    public function getEdit(){
        $id = (int)$_REQUEST['id'];
        if($id ==0){
            $msg = 'Id not fill in';
            return $this->status(500,$msg);
        }
        $resultById = $this->country->get($id);
        if($resultById == false){
            $msg = 'Id not existed';
            return $this->status(500,$msg);
        }
        $msg =  $resultById;
        return $this->status(200,$msg);
    }
    public function postEdit(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $id = (int)$_REQUEST['id'];
        if($id ==0){
            $msg = 'Id not filled in';
            return $this->status(500,$msg);
        }
        $resultById = $this->country->get($id);
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
            'name' => $req['name'],
        ];    
        $result = $this->country->update($id,$data);
        if($result == true){
            $msg =  'Update cate success';
            return $this->status(200,$msg);
        }
        $msg =  'Update cate error';
        return $this->status(500,$msg);
    }
    public function delete(){
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            $msg =   'Id not fill in';
            return $this->status(500,$msg);
        }
        $resultGetById = $this->country->get($id);
        if($resultGetById == null){
            $msg =   'Id not exactly';
            return $this->status(500,$msg);
        }
        $this->country->delete($id);
        $msg = 'Delete cate success';
        return $this->status(200,$msg);
    }
}
