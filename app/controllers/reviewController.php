<?php

namespace App\Controllers;

include_once('core/http/Container.php');
require_once('app/services/reviewService.php');
use App\Services\ReviewService;
use Core\Http\BaseController;
class reviewController extends BaseController{
    private $reviewService;
    public function __construct(){
        $this->reviewService = new ReviewService();
    }
    // get review with place_id
    public function index()
    {
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $place_id = (int)$req['place_id'];
        return $this->reviewService->list($place_id);
    }
    public function getByYou()
    {
        return $this->reviewService->getByYou();
    }
    public function getAboutYou()
    {
        return $this->reviewService->getAboutYou();
    }
    // add review with place_id
    public function postAdd(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $place_id = (int)$req['place_id'];
        return $this->reviewService->add($place_id,$req);
    }  
}