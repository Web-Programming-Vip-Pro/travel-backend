<?php

namespace App\Controllers;

include_once('app/models/wishlistModel.php');
include_once('app/models/placeModel.php');
include_once('core/http/Container.php');

use App\Models\WishlistModel;
use App\Models\PlaceModel;
use Core\Http\BaseController;
class wishlistController extends BaseController{
    private $wishlist;
    private $place;
    public function __construct(){
        $this->wishlist = new WishlistModel();
        $this->place = new PlaceModel();
    }
    public function index()
    {
        $user_id = (int)$_REQUEST['id'];//get user_id qua token
        $result = $this->wishlist->getForUser($user_id);
        $msgs = [
            'status'    =>  'success',
            'msg'       =>  'Get wishlist',
            'data'      =>  $result
        ];
        return $this->status(200,$msgs);
    }
    public function postAdd(){
        $place_id = (int)$_REQUEST['id'];
        if($place_id ==0){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'Id not fill in',
                'data'      =>  null
            ];
            return $this->status(500,$msg);
        }
        $resultByIdPlace = $this->place->get($place_id);
        if($resultByIdPlace == false){
            $msg = [
                'status'    => 'error',
                'msg'       => 'Place not existed',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $data=[
            'user_id'       => 1,//get user_id qua token
            'place_id'      => $place_id,
        ];
        $result = $this->wishlist->create($data);
        if($result == false){
            $msg = [
                'status'    => 'error',
                'msg'       => 'Error add wishlist',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $msg = [    
            'status'    => ' success',
            'msg'       => 'Add wishlist success',
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
        $result = $this->wishlist->delete($id);
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