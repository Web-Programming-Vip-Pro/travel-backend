<?php

namespace App\Services;

require_once('app/models/reviewModel.php');
require_once('app/models/placeModel.php');
require_once('app/models/userModel.php');
require_once('app/models/transactionModel.php');
require_once('core/http/Container.php');
require_once('app/validators/reviewValidate.php');

use App\Models\ReviewModel;
use App\Models\PlaceModel;
use App\Models\UserModel;
use App\Models\TransactionModel;
use Core\Http\BaseController;
use App\Validator\ReviewValidate;

class ReviewService
{
    private $review;
    private $controller;
    private $validate;
    private $place;
    private $user;
    private $transaction;
    public function __construct()
    {
        $this->controller   = new BaseController();
        $this->review       = new ReviewModel();
        $this->place        = new PlaceModel();
        $this->validate     = new ReviewValidate();
        $this->user        = new UserModel();
        $this->transaction = new TransactionModel();
    }

    public function list($req)
    {
        $page = isset($req['page']) ? (int)$req['page'] : 0;
        $limit = isset($req['limit']) ? (int)$req['limit'] : 20;
        $place_id = isset($req['place_id']) ? (int)$req['place_id'] : -1;
        $user_id = isset($req['user_id']) ? (int)$req['user_id'] : -1;
        $agency_id = isset($req['agency_id']) ? (int)$req['agency_id'] : -1;
        $order = isset($req['order']) ? $req['order'] : 'recent';
        if ($agency_id !== -1) {
            // get all places by agency_id
            $places = $this->place->getByAuthorId($agency_id);
            // get array place_id from places
            $place_ids = array_column($places, 'id');
            // count all reviews by place_id
            $total = $this->review->countByPlaceId($place_ids);
            // loops through all places and get all reviews
            $reviews = [];
            foreach ($places as $place) {
                $reviewsInPlace = $this->review->getByPlaceId((int)$place->id, $page, $limit, $order);
                if ($reviewsInPlace) {
                    $reviews = array_merge($reviews, $reviewsInPlace);
                    // add name of place to each review
                    foreach ($reviewsInPlace as $review) {
                        $review->place_title = $place->title;
                    }
                    // get user name from user_id
                    foreach ($reviews as $key => $value) {
                        $user = $this->getUserInfo((int)$value->user_id);
                        $reviews[$key]->user = $user;
                    }
                }
            }
            $totalPages = ceil($total / $limit);
            // limit reviews by page, limit
            $reviews = array_slice($reviews, $page * $limit, $limit);
            return $this->controller->status(200, [
                'total' => $totalPages, 'reviews' => $reviews
            ]);
        }
        $reviews = $this->review->get(-1, $place_id, $user_id, $page, $limit, $order);
        $total = $this->review->countAll();
        // if result, loop through and get user info
        if ($reviews) {
            foreach ($reviews as $key => $value) {
                $user = $this->getUserInfo((int)$value->user_id);
                $reviews[$key]->user = $user;
            }
            // loop through and get place title
            foreach ($reviews as $key => $value) {
                $place = $this->place->get((int)$value->place_id);
                $reviews[$key]->place_title = $place['title'];
            }
        }
        $totalPages = ceil($total / $limit);
        return $this->controller->status(200, [
            'total' => $totalPages, 'reviews' => $reviews
        ]);
    }

    public function getByPlace($req)
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

        $msg = 'Add review to database success';
        return $this->controller->status(200, $msg);
    }

    public function check($req)
    {
        $userId = (int)$req['user_id'];
        $placeId = (int)$req['place_id'];
        // check if user has reviewed this place
        $result = $this->review->get(-1, $placeId, $userId);
        if ($result) {
            return $this->controller->status(500, 'User has reviewed this place');
        }
        // check if user not finished booking this place
        $transactions = $this->transaction->findTransaction($placeId, $userId, -1, 3);
        // loop transactions if array, and get last transaction
        if (is_array($transactions)) {
            $transaction = end($transactions);
        } else {
            $transaction = $transactions;
        }
        // check if transaction is not null
        if ($transaction) {
            // if transaction status_place = 3, user can review
            if ((int)$transaction->status_place == 3) {
                return $this->controller->status(200, 'User can review');
            }
        }
        return $this->controller->status(500, 'User has not finished booking this place');
    }

    public function delete($req)
    {
        $id = (int)$req['id'];
        // check if review exist
        $review = $this->review->get($id);
        if (!$review) {
            return $this->controller->status(500, 'Review not found');
        }
        // get place of review and update stars (by sum all rate of reviews and divide by number of reviews)
        $place = $this->place->get((int)$review['place_id']);
        $dataPlace = [
            'reviews'   => $place['reviews'] - 1
        ];
        if ($place['stars'] == 0.0) {
            $dataPlace['stars'] = 0.0;
        } else {
            $dataPlace['stars'] =  (float)($place['stars'] - $review['rate']) / ($place['reviews'] - 1);
        }

        $result = $this->review->delete($id);
        if ($result == false) {
            $msg = 'Delete review fail';
            return $this->controller->status(500, $msg);
        }
        $msg = 'Delete review success';
        //
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
