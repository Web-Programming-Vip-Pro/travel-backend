<?php

namespace App\Controllers;

require_once('core/http/Container.php');
require_once('app/service/wishlistService.php');

use Core\Http\BaseController;
use App\Services\WishlistService;
class wishlistController extends BaseController{
    private $wishlistService;
    public function __construct(){
        $this->wishlistService = new WishlistService();
    }
    public function index()
    {
       return $this->wishlistService->list();
    }
    public function postAdd(){
        $place_id = (int)$_REQUEST['id'];
        return $this->wishlistService->add($place_id);
    }  
    public function delete(){
        $place_id = (int)$_REQUEST['id'];
        $this->wishlistService->delete($place_id);
    }
}