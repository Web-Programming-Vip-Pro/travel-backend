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
        $result = json_encode($result);
        echo '<pre>';
        print_r($result);
        echo '</pre>';
        return;
    }
    public function postAdd(){
        $req = $_POST;
        $msg = $this->validate->add($req);
        if(count($msg) > 0){
            echo "Một số trường chưa được điền đầy đủ";
            return;
        } 
        $data = [
            'name' => $req['name'],
        ];
        $result = $this->country->create($data);
        if($result == null){
            echo "Error add country";
            return;
        }
        echo "Add country success";
        return;
    }  
    public function getEdit(){
        $req = $_POST;
        $id = (int)$_REQUEST['id'];
        if($id ==0){
            echo " Vui lòng nhập id";
            return;
        }
        $resultById = $this->country->get($id);
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
        $msg = $this->validate->add($req);
        if($id ==0){
            echo " Vui lòng nhập id";
            return;
        }
        $resultById = $this->country->get($id);
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
        $result = $this->country->update($id,$data);
        echo "Update country success";
        return;
    }
    public function delete(){
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            echo "Vui lòng nhập Id";
            return;
        }
        $resultGetById = $this->country->get($id);
        if($resultGetById == null){
            echo "Id khong tồn tại";
            return;
        }
        $this->country->delete($id);
        print_r("Delete country success");
        return ;
    }
}
