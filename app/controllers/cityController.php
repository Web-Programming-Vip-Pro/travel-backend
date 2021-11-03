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
        $result = json_encode($result);
        echo '<pre>';
        print_r($result);
        echo '</pre>';
        return;
    }
    public function postAdd(){
        $req = $_POST;
        $msg = [];
        
        if(count($msg) > 0){
            echo "Một số trường chưa được điền đầy đủ";
            return;
        } 
        $data = [
            'name' => $req['name'],
        ];
        $result = $this->city->create($data);
        if($result == null){
            echo "Error add city";
            return;
        }
        echo "Add city success";
        return;
    }  
    public function getEdit(){
        $req = $_POST;
        $id = (int)$_REQUEST['id'];
        if($id ==0){
            echo " Vui lòng nhập id";
            return;
        }
        $resultById = $this->city->get($id);
        if($resultById == null){
            echo " Id không tồn tại";
            return;
        }
        echo '<pre>';
        print_r($resultById);
        echo '</pre>';
        return;
    }
    public function postEdit(){
        $req = $_POST;
        $id = (int)$_REQUEST['id'];
        $msg =[];
        if($id ==0){
            echo " Vui lòng nhập id";
            return;
        }
        $resultById = $this->city->get($id);
        if($resultById == null){
            echo " Id không tồn tại";
            return;
        }
        if (!isset($req['name'])) {
            array_push($msg, 'Vui lòng điền tên đất nước');
        }
        if(count($msg) > 0){
            echo "Một số trường chưa được điền đầy đủ";
            return;
        } 
        $data = [
            'name' => $req['name'],
        ];    
        $result = $this->city->update($id,$data);
        echo "Update city success";
        return;
    }
    public function delete(){
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            echo "Vui lòng nhập Id";
            return;
        }
        $resultGetById = $this->city->get($id);
        if($resultGetById == null){
            echo "Id khong tồn tại";
            return;
        }
        $this->city->delete($id);
        print_r("Delete user success");
        return ;
    }
}