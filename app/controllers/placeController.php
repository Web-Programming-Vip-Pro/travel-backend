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
       $msg = $result;
       return $this->status(200,$msg);
    }
    public function postAdd(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $msgs = $this->validate->add($req);
        if(count($msgs) >0){
            $msg = 'Some fielt not filled in';
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
            $msg= 'Add cate to database fail';
            return $this->status(500,$msg);
        }
        $msg= 'Add cate to database success';
        return $this->status(200,$msg);
    }  
    public function getEdit(){
        $id = (int)$_REQUEST['id'];
        if($id == 0 ){
            $msg = 'Id not filled in';
            return $this->status(500,$msg);
        }
        $result = $this->place->get($id);
        if($result == false){
            $msg = 'Id not existed';
            return $this->status(500,$msg);
        }
        $msg = $result;
        return $this->status(200,$msg);
        
    }
    public function postEdit(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            $msg =   'Id not fill in';
            return $this->status(500,$msg);
        }
        $resultById = $this->place->get($id);
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
            $msg =  'Update cate success';
            return $this->status(200,$msg);
        }
        $msg = 'Update cate error';
        return $this->status(500,$msg);
    }
    public function delete(){
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            $msg =   'Id not fill in';
            return $this->status(500,$msg);
        }
        $resultGetById = $this->place->get($id);
        if($resultGetById == false){
            $msg =   'Id not exactly';
            return $this->status(500,$msg);
        }
        $this->place->delete($id);
        $msg = 'Delete cate success';
        return $this->status(200,$msg);
    }
}
