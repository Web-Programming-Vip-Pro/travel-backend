<?php

namespace App\Services;

require_once('core/http/Container.php');
require_once('app/models/transactionModel.php');
require_once('app/models/placeModel.php');
require_once('app/models/userModel.php');

use App\Models\PlaceModel;
use Core\Http\BaseController;
use App\Models\TransactionModel;
use App\Models\UserModel;

class TransactionService
{
    private $place;
    private $transaction;
    private $controller;
    private $user;
    public function __construct()
    {
        $this->transaction  = new TransactionModel();
        $this->controller   =  new BaseController();
        $this->place        = new PlaceModel();
        $this->user         = new UserModel();
    }

    public function list($req)
    {
        $userId = isset($req['user_id']) ? (int)$req['user_id'] : -1;
        $placeId = isset($req['place_id']) ? (int)$req['place_id'] : -1;
        $agencyId = isset($req['agency_id']) ? (int)$req['agency_id'] : -1;
        $page = isset($req['page']) ? (int)$req['page'] : 0;
        $limit = isset($req['limit']) ? (int)$req['limit'] : 10;
        $getPage = isset($req['getPage']) ? (int)$req['getPage'] : -1;
        $result = $this->transaction->get(-1, $userId, $placeId, $agencyId, $page, $limit);
        // if $result, get place name from place model; if not, return error
        if ($result) {
            // loop through result and get place name
            foreach ($result as $key => $value) {
                $placeName = $this->place->get((int)$value->place_id);
                $result[$key]->place_title = $placeName['title'];
                // get agency name by agency id
                $agencyName = $this->user->get((int)$value->agency_id);
                $result[$key]->agency_name = $agencyName['name'];
                // get yser name by user id
                $userName = $this->user->get((int)$value->user_id);
                $result[$key]->user_name = $userName['name'];
            }
            if ($getPage) {
                $count = $this->transaction->count();
                $totalPage = ceil($count / $limit);
                $result = [
                    'total_pages' => $totalPage,
                    'data' => $result
                ];
            }
            return $this->controller->status(200, $result);
        } else {
            return $this->controller->status(400, 'No transaction found');
        }
    }

    public function add($req)
    {
        $placeId = (int)$req['place_id'];
        $userId = (int)$req['user_id'];
        $resultByIdPlace = $this->place->get($placeId);
        $msgHandleId = $this->handleId($placeId, $resultByIdPlace);
        if ($msgHandleId != false) {
            return $this->controller->status(500, $msgHandleId);
        }
        $data = [
            'user_id'           => $userId,
            'place_id'          => $placeId,
            'agency_id'         => $resultByIdPlace['author_id'],
            'value'            => $resultByIdPlace['price'],
            'status_place'      => 0,
        ];
        $result = $this->transaction->create($data);
        if ($result == false) {
            $msg = 'Add transaction to database fail';
            return $this->controller->status(500, $msg);
        }
        $msg = 'Add transaction to database success';
        return $this->controller->status(200, $msg);
    }
    public function get($req)
    {
        $userId = isset($req['user_id']) ? (int)$req['user_id'] : -1;
        $placeId = isset($req['place_id']) ? (int)$req['place_id'] : -1;
        $agencyId = isset($req['agency_id']) ? (int)$req['agency_id'] : -1;
        $status = isset($req['status']) ? json_decode($req['status']) : -1;
        $transaction = $this->transaction->findTransaction($placeId, $userId, $agencyId, $status);
        if ($transaction == false) {
            $msg = 'Transaction not found';
            return $this->controller->status(500, $msg);
        }
        return $this->controller->status(200, $transaction[0]);
    }

    public function getUserTransactions($userId, $agencyId, $status, $page, $limit)
    {
        $transaction = $this->transaction->get(-1, $userId, $agencyId, $status, $page, $limit);
        return $transaction;
    }

    public function getPlacesByUserTransaction($req)
    {
        $userId = isset($req['user_id']) ? (int)$req['user_id'] : -1;
        $agencyId = isset($req['agency_id']) ? (int)$req['agency_id'] : -1;
        $status = isset($req['status']) ? json_decode($req['status']) : -1;
        $page = isset($req['page']) ? (int)$req['page'] : 0;
        $limit = isset($req['limit']) ? (int)$req['limit'] : 10;
        $transactions = $this->getUserTransactions($userId, $agencyId, $status, $page, $limit);
        if ($transactions == false) {
            $msg = 'Transaction not found';
            return $this->controller->status(500, $msg);
        }
        $places = [];
        foreach ($transactions as $transaction) {
            $place = $this->place->get((int)$transaction->place_id);
            $place['status_place'] = $transaction->status_place;
            $place['images'] = isset($place['images']) ? json_decode($place['images']) : [];
            $places[] = $place;
        }
        return $this->controller->status(200, $places);
    }

    public function getEdit($id)
    {
        if ($this->user == false) {
            return $this->controller->status(401, "Unauthorized");
        }
        $resultById = $this->transaction->get($id);
        $msgHandleId = $this->handleId($id, $resultById);
        if ($msgHandleId != false) {
            return $this->controller->status(500, $msgHandleId);
        }
        return $this->controller->status(200, $resultById);
    }
    /**
     * agency update status, add notify for user
     * user update status, add notify for agency
     */
    public function postEdit($id, $req)
    {
        if ($this->user == false) {
            return $this->controller->status(401, "Unauthorized");
        }
        $resultById = $this->transaction->get($id);
        $msgHandleId = $this->handleId($id, $resultById);
        if ($msgHandleId != false) {
            return $this->controller->status(500, $msgHandleId);
        }
        if ($this->user->role == 0) {
            $data = [
                'status_place'    => $req['status_place']
            ];
            $result = $this->transaction->update($id, $data);
            if ($result == false) {
                $msg =  'Update transaction fail';
                return $this->controller->status(500, $msg);
            }
            $msg =  'Update transaction success';
            return $this->controller->status(200, $msg);
        } else if ($this->user->role == 1) {
            $data = [
                'status_place'    => $req['status_place'],
                'message'         => $req['message'],
            ];
            $result = $this->transaction->update($id, $data);
            if ($result == false) {
                $msg =  'Update transaction fail';
                return $this->controller->status(500, $msg);
            }
            $msg =  'Update transaction success';
            return $this->controller->status(200, $msg);
        }
    }
    public function handleId($id, $result = null)
    {
        if ($id == 0) {
            return 'Id not fill in';
        }
        if ($result == null) {
            return  'Id not exactly';
        }
        return false;
    }
}
