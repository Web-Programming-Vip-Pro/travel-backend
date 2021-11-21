<?php

namespace App\Services;

require_once('app/models/reviewModel.php');
require_once('app/models/placeModel.php');
require_once('app/models/userModel.php');
require_once('app/middleware/middleware.php');
include_once('app/models/notifyModel.php');
require_once('core/http/Container.php');
require_once('app/validators/reviewValidate.php');

use App\Models\ReviewModel;
use App\Middleware\Middleware;
use App\Models\PlaceModel;
use App\Models\UserModel;
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
    private $user;
    public function __construct()
    {
        $this->controller   = new BaseController();
        $this->review       = new ReviewModel();
        $this->place        = new PlaceModel();
        $this->notify       = new NotifyModel();
        $this->validate     = new ReviewValidate();
        $this->middleware   = new Middleware();
        $this->user        = new UserModel();
    }

    public function list($req)
    {
        $id = (int)$req['id'];
        $page = isset($req['page']) ? (int)$req['page'] : 0;
        $limit = isset($req['limit']) ? (int)$req['limit'] : 20;
        $order = isset($req['order']) ? $req['order'] : 'recent';
        $result = $this->review->getByPlaceId($id, $page, $limit, $order);
        // if result, loop through and get user info
        if ($result) {
            foreach ($result as $key => $value) {
                $user = $this->getUserInfo((int)$value->user_id);
                $result[$key]->user = $user;
            }
        }
        return $this->controller->status(200, $result);
    }

    public function getUserInfo($userId)
    {
        $user = $this->user->get($userId);
        $data = [
            'id' => $user['id'],
            'name' => $user['name'],
            'avatar' => $user['avatar'],
            'blocked' => $user['blocked'],
        ];
        return $data;
    }

    public function add($req)
    {
        $msgs = $this->handleValidator($req);
        if ($msgs != false) {
            return $this->container->status(422, $msgs);
        }
        $place_id = (int)$req['place_id'];
        $place = $this->place->get($place_id);
        $data = [
            'user_id'       => $req['user_id'],
            'place_id'      => $place_id,
            'rate'          => $req['rate'],
            'comment'       => $req['comment'],
        ];
        $result = $this->review->create($data);
        if ($result == false) {
            $msg = 'Add review to database fail';
            return $this->controller->status(500, $msg);
        }
        // update stars place
        $dataPlace = [
            'reviews'   => $place['reviews'] + 1
        ];
        if ($place['stars'] == 0.0) {
            $dataPlace['stars'] = $req['rate'];
        } else {
            $dataPlace['stars'] =  (float)($place['stars'] + $req['rate']) / 2;
        }
        $this->place->update($place_id, $dataPlace);
        // add notify for agency
        // $title = $this->user->name . 'commented place ' . $this->place['title'];
        // $dataNotify = [
        //     'title'     => $title,
        //     'content'   => $data['comment'],
        //     'seen'      => false,
        //     'user_id'   => $place['author_id']
        // ];
        // $this->notify->create($dataNotify);
        $msg = 'Add review to database success';
        return $this->controller->status(200, $msg);
    }
    public function handleValidator($req)
    {
        $msgs = $this->validate->add($req);
        if (count($msgs) > 0) {
            return $msgs;
        }
        return false;
    }
}
