<?php
namespace App\Services;

require_once('app/models/reviewModel.php');
require_once('app/models/placeModel.php');
require_once('app/middleware/middleware.php');
include_once('app/models/notifyModel.php');
require_once('core/http/Container.php');
require_once('app/validators/reviewValidate.php');

use App\Models\ReviewModel;
use App\Middleware\Middleware;
use App\Models\PlaceModel;
use Core\Http\BaseController;
use App\Models\NotifyModel;
use App\Validator\ReviewValidate;

class ReviewService 
{  
    private $review;
    private $middleware;
    private $controller;
    private $validate;
    private $place;
    private $notify;
    public function __construct()
    {   
        $this->controller   = new BaseController();
        $this->review       = new ReviewModel();
        $this->place        = new PlaceModel();
        $this->notify       = new NotifyModel();
        $this->validate     = new ReviewValidate();
        $this->middleware   = new Middleware();
        $this->user         = $this->middleware->handleUser();
    }
    /**
     * param @place_id
     * return @response
     */
    public function list($id){
        if($this->user == false){
            return $this->controller->status(401,"Unauthorized");
        }
        $result = $this->review->getByPlaceId($id);
        return $this->controller->status(200,$result);
    }
    /**
     * param @place_id,@req
     * return response
     */
    public function add($place_id,$req){
        if($this->user == false){
            return $this->controller->status(401,"Unauthorized");
        }
        $msgs = $this->handleValidator($req);
        if($msgs != false){
            return $this->container->status(422,$msgs);
        }
        $data = [
            'user_id'       => $this->user->id,
            'place_id'      => $place_id,
            'rate'          => $req['rate'],
            'comment'       => $req['comment'],
        ];
        $result = $this->review->create($data);
        if($result == false){
            $msg= 'Add review to database fail';
            return $this->controller->status(500,$msg);
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
        $title = $this->user->name .'commented place' .$this->place['title'];
        echo $title;
        $dataNotify = [
            'title'     => 'Your place have a new review',
            'content'   => $data['comment'],
            'seen'      => false,
            'user_id'   => $place['author_id']
        ];
        $this->notify->create($dataNotify);
        $msg= 'Add review to database success';
        return $this->controller->status(200,$msg);
    }
    public function handleValidator($req){
        $msgs = $this->validate->add($req);
        if(count($msgs) > 0){
            return $msgs;
        } 
        return false;
    }
}
