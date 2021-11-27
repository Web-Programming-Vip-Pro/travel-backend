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

    public function handleValidator($req)
    {
        $msgs = $this->validate->add($req);
        if (count($msgs) > 0) {
            return $msgs;
        }
        return false;
    }
}
