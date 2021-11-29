<?php

namespace App\Services;

require_once('core/http/Container.php');
require_once('app/models/transactionModel.php');
require_once('app/models/placeModel.php');
require_once('app/models/userModel.php');
require_once('storage/sendMail.php');

use App\Models\PlaceModel;
use Core\Http\BaseController;
use App\Models\TransactionModel;
use App\Models\UserModel;
use Storage\SendMail;

class TransactionService
{
    private $place;
    private $transaction;
    private $controller;
    private $user;
    private $mail;
    public function __construct()
    {
        $this->transaction  = new TransactionModel();
        $this->controller   =  new BaseController();
        $this->place        = new PlaceModel();
        $this->user         = new UserModel();
        $this->mail     = new SendMail();
    }

    public function list($req)
    {
        $userId = isset($req['user_id']) ? (int)$req['user_id'] : -1;
        $placeId = isset($req['place_id']) ? (int)$req['place_id'] : -1;
        $agencyId = isset($req['agency_id']) ? (int)$req['agency_id'] : -1;
        $page = isset($req['page']) ? (int)$req['page'] : 0;
        $limit = isset($req['limit']) ? (int)$req['limit'] : 10;
        $getPage = isset($req['getPage']) ? (int)$req['getPage'] : -1;
        $result = $this->transaction->get(-1, $userId, $placeId, $agencyId, -1, $page, $limit);
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
        $transaction = $this->transaction->get(-1, $userId, -1, $agencyId, $status, $page, $limit);
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
            $place['transaction'] = $transaction;
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
    public function postEdit($req)
    {
        $id = (int)$req['id'];
        $resultById = $this->transaction->get($id);
        $msgHandleId = $this->handleId($id, $resultById);
        if ($msgHandleId != false) {
            return $this->controller->status(500, $msgHandleId);
        }
        $data = [];
        if (isset($req['status_place'])) {
            $data['status_place'] = (int)$req['status_place'];
        }
        if (isset($req['message'])) {
            $data['message'] = $req['message'];
        }
        $result = $this->transaction->update($id, $data);
        if ($result == false) {
            $msg =  'Update transaction fail';
            return $this->controller->status(500, $msg);
        }
        $msg =  'Update transaction success';
        $this->sendEmailWhenTransactionUpdate($id);
        return $this->controller->status(200, $msg);
    }

    public function sendEmailWhenTransactionUpdate($id)
    {
        $resultById = $this->transaction->get($id);
        $msgHandleId = $this->handleId($id, $resultById);
        if ($msgHandleId != false) {
            return $this->controller->status(500, $msgHandleId);
        }
        $transactionMessage = $resultById['message'];
        $transactionStatus = $resultById['status_place'];
        $status = '';
        switch ($transactionStatus) {
            case 0:
                $status = 'Waiting for confirmation';
                break;
            case 1:
                $status = 'Booking';
                break;
            case 2:
                $status = 'Cancelled';
                break;
            default:
                $status = 'Finished';
                break;
        }
        $mailToUser = new SendMail();
        $mailToAgency = new SendMail();
        $agency = $this->user->get((int)$resultById['agency_id']);
        $user = $this->user->get((int)$resultById['user_id']);
        $userName = $user['name'];
        $userEmail = $user['email'];
        $title = 'Your booking has been updated!';
        // email with body include transaction message, status
        $body = '<h1>Your booking has been updated!</h1>
                <p>Your booking has been updated to ' . $status . '</p>
                <p>' . $transactionMessage . '</p>
                <p>You can contact with agency ' . $agency['name'] . ' at ' . $agency['email'] . '</p>
                <p>Thank you for using our service!</p>';
        $mailToUser->sendMail($userEmail, $title, $body);
        $title = 'Your booking has been updated!';
        // email for agency with body include user name, transaction message, status and place id
        $body = '<h1>Your booking has been updated!</h1>
                <p>' . $userName . ' has updated booking to ' . $status . '</p>
                <p>' . $transactionMessage . '</p>
                <p>You can contact with user ' . $userName . ' at ' . $userEmail . '</p>
                <p>Thank you for using our service!</p>';

        $mailToAgency->sendMail($agency['email'], $title, $body);
        return true;
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
