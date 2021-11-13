<?php

namespace App\Controllers;

include_once('core/http/Container.php');
use App\Services\ReviewService;
use Core\Http\BaseController;
class ReviewController extends BaseController{
    private $reviewService;
    public function __construct(){
        $this->reviewService = new ReviewService();
    }
    // get review with place_id
    public function index()
    {
        $place_id = (int)$_REQUEST['id'];
        return $this->reviewService->list($place_id);
    }
    // add review with place_id
    public function postAdd(){
        $place_id = (int)$_REQUEST['id'];
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
       return $this->reviewService->add($place_id,$req);
    }  
}