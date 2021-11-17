<?php
namespace App\Services;

require_once('app/models/wishlistModel.php');
require_once('app/models/placeModel.php');
require_once('app/middleware/middleware.php');
require_once('core/http/Container.php');

use App\Models\WishlistModel;
use App\Models\PlaceModel;
use App\Middleware\Middleware;
use Core\Http\BaseController;


class WishlistService 
{   
    private $place;
    private $wishlist;
    private $middleware;
    private $controller;
    public function __construct()
    {   
        $this->controller = new BaseController();
        $this->place    = new PlaceModel();
        $this->wishlist = new WishlistModel();
        $this->middleware   = new Middleware();
        $this->user         = $this->middleware->handleAdmin();
        echo "Hello";
    }
    public function list(){
        if($this->user == false){
            return $this->controller->status(401,"Unauthorized");
        }
        $user_id = $this->user->id;
        $result = $this->wishlist->getForUser($user_id);
        return $this->controller->status(200,$result);
    }
    public function add($place_id){
        if($this->user == false){
            return $this->controller->status(401,"Unauthorized");
        }
        $msgHandleId = $this->handleId($place_id);
        if($msgHandleId != false){
            return $this->controller->status(500,$msgHandleId);
        }
        $data=[
            'user_id'       => $this->user->id,
            'place_id'      => $place_id,
        ];
        $result = $this->wishlist->create($data);
        if($result == false){
            $msg =  'Error add wishlist';
            return $this->controller->status(500,$msg);
        }
        $msg = 'Add wishlist success';
        return $this->controller->status(200,$msg);
    }
    public function delete($place_id){
        if($this->user == false){
            return $this->controller->status(401,"Unauthorized");
        }
        $msgHandleId = $this->handleId($place_id);
        if($msgHandleId != false){
            return $this->controller->status(500,$msgHandleId);
        }
        $result = $this->wishlist->delete($place_id);
        if($result == false){
            $msg = 'Delete wishlist fail';
            return $this->controller->status(500,$msg);
        }
        $msg = "Delete wishlist success";
        return $this->controller->status(200,$msg);
    }
    public function handleId($id){
        if($id ==0){
            return 'Id not fill in';
        }
        $resultByIdPlace = $this->place->get($id);
        if($resultByIdPlace == false){
            return 'Place not existed';
        }
        return false;
    }
}
