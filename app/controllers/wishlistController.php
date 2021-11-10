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
        $msgs =  $result;
        return $this->status(200,$msgs);
    }
    public function postAdd(){
        $place_id = (int)$_REQUEST['id'];
        if($place_id ==0){
            $msg = 'Id not fill in';
            return $this->status(500,$msg);
        }
        $resultByIdPlace = $this->place->get($place_id);
        if($resultByIdPlace == false){
            $msg = 'Place not existed';
            return $this->status(500,$msg);
        }
        $data=[
            'user_id'       => 1,//get user_id qua token
            'place_id'      => $place_id,
        ];
        $result = $this->wishlist->create($data);
        if($result == false){
            $msg =  'Error add wishlist';
            return $this->status(500,$msg);
        }
        $msg = 'Add wishlist success';
        return $this->status(200,$msg);
    }  
    public function delete(){
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            $msg = 'Id not fill in';
            return $this->status(500,$msg);
        }
        $resultGetById = $this->city->get($id);
        if($resultGetById == null){
            $msg =  'Id not existed';
            return $this->status(500,$msg);
        }
        $result = $this->wishlist->delete($id);
        if($result == false){
            $msg = 'Delete wishlist fail';
            return $this->status(500,$msg);
        }
        $msg = "Delete wishlist success";
        return $this->status(200,$msg);
    }
}