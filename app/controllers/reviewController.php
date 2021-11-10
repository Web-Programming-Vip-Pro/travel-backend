<?php

namespace App\Controllers;

include_once('app/models/reviewModel.php');
include_once('app/models/placeModel.php');
include_once('app/models/notifyModel.php');
include_once('core/http/Container.php');
require_once('app/validators/reviewValidate.php');
use App\Models\ReviewModel;
use Core\Http\BaseController;
use App\Validator\ReviewValidate;
use App\Models\PlaceModel;
use App\Models\NotifyModel;
class ReviewController extends BaseController{
    private $review;
    private $validate;
    private $place;
    private $notify;
    public function __construct(){
        $this->review = new ReviewModel();
        $this->validate = new ReviewValidate();
        $this->place = new PlaceModel();
        $this->notify = new NotifyModel();
    }
    // get review with place_id
    public function index()
    {
        $place_id = (int)$_REQUEST['id'];
        $result = $this->review->getByPlaceId($place_id);
        $msg = $result;
        return $this->status(200,$msg);
    }
    // add review with place_id
    public function postAdd(){
        $place_id = (int)$_REQUEST['id'];
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $msgs = $this->validate->add($req);
        if(count($msgs) > 0){
            $msg = 'Some fielt not filled in';
            return $this->status(422,$msg);
        } 
        $data = [
            'user_id'       => 1,
            'place_id'      => $place_id,
            'rate'          => $req['rate'],
            'comment'       => $req['comment'],
        ];
        $result = $this->review->create($data);
        if($result == false){
            $msg= 'Add review to database fail';
            return $this->status(500,$msg);
        }
        $place = $this->place->get($place_id);
        $dataPlace = [
            'reviews'   => $place['reviews'] +1
        ];
        if($place['stars'] == 0.0){
            $dataPlace['stars'] = $req['rate'];
        }else{
            $dataPlace['stars'] =  (float)($place['stars']+$req['rate'])/2;
        }
        $this->place->update($place_id,$dataPlace);
        $dataNotify = [
            'title' => 'Your place have a new review',
            'content' => $data['comment'],
            'seen' => false,
            'user_id' => $place['author_id']
        ];
        $this->notify->create($dataNotify);
        $msg= 'Add review to database success';
        return $this->status(200,$msg);
    }  
}