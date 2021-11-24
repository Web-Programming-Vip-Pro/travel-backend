<?php

namespace App\Controllers;

require_once('core/http/Container.php');
require_once('app/services/wishlistService.php');

use Core\Http\BaseController;
use App\Services\WishlistService;

class wishlistController extends BaseController
{
    private $wishlistService;
    public function __construct()
    {
        $this->wishlistService = new WishlistService();
    }
    public function index()
    {
        $req = $_REQUEST;
        return $this->wishlistService->list($req);
    }
    public function toggle()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        return $this->wishlistService->toggle($req);
    }
    public function delete()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        $place_id = (int)$req['place_id'];
        $this->wishlistService->delete($place_id);
    }

    public function isInWishList()
    {
        $req = $_REQUEST;
        return $this->wishlistService->isInWishlist($req);
    }
}
