<?php

namespace App\Services;
require_once('core/http/Container.php');
require_once('app/models/transactionModel.php');
require_once('app/middleware/middleware.php');
require_once('app/models/placeModel.php');
require_once('app/models/notifyModel.php');

use App\Models\PlaceModel;
use App\Models\NotifyModel;
use App\Middleware\Middleware;
use Core\Http\BaseController;
use App\Models\TransactionModel;

class TransactionService{
    private $place;
    private $middleware;
    private $transaction;
    private $controller;
    private $notify;
    public function __construct()
    {
        $this->transaction  = new TransactionModel();
        $this->controller   =  new BaseController();
        $this->place        = new PlaceModel();
        $this->middleware   = new Middleware();
        $this->notify       = new NotifyModel();
        $this->user         = $this->middleware->handle();
    }
    public function list(){
        if($this->user == false){
            return $this->container->status(401,"Unauthorized");
        }
        $role = $this->user->role;
        $user_id = $this->user->id;
        if($role == 1){
            $result = $this->transaction->getForAcengy($user_id);
            return $this->controller->status(200,$result);
        }
        if($role == 0){
            $result = $this->transaction->getForUser($user_id);
            return $this->controller->status(200,$result);
        }
    }
    /**
     * add notify for agency
     */
    public function add($place_id){
        if($this->user == false){
            return $this->controller->status(401,"Unauthorized");
        }
        $resultByIdPlace = $this->place->get($place_id);
        $msgHandleId = $this->handleId($place_id,$resultByIdPlace);
        if($msgHandleId != false){
            return $this->controller->status(500,$msgHandleId);
        }
        $data=[
            'user_id'           => $this->user->id,
            'place_id'          => $place_id,
            'agency_id'         => $resultByIdPlace['author_id'],
            'status_place'      => 0,
        ];
        $result = $this->transaction->create($data);
        if($result == false){
            $msg= 'Add transaction to database fail';
            return $this->controller->status(500,$msg);
        }
        $this->addNotify($this->user,$resultByIdPlace);
        $msg= 'Add transaction to database success';
        return $this->controller->status(200,$msg);
    }
    public function getEdit($id){
        if($this->user == false){
            return $this->controller->status(401,"Unauthorized");
        }
        $resultById = $this->transaction->get($id);
        $msgHandleId = $this->handleId($id,$resultById);
        if($msgHandleId != false){
            return $this->controller->status(500,$msgHandleId);
        }
        return $this->controller->status(200,$resultById);
    }
    /**
     * agency update status, add notify for user
     * user update status, add notify for agency
     */
    public function postEdit($id,$req){
        if($this->user == false){
            return $this->controller->status(401,"Unauthorized");
        }
        $resultById = $this->transaction->get($id);
        $msgHandleId = $this->handleId($id,$resultById);
        if($msgHandleId != false){
            return $this->controller->status(500,$msgHandleId);
        }
        if($this->user->role == 0){
            $data = [
                'status_place'    => $req['status_place']
            ];
            $result = $this->transaction->update($id,$data);
            if($result == false){
                $msg =  'Update transaction fail';
                return $this->controller->status(500,$msg);
            }
            $this->editUserNotify($this->user,$resultById);
            $msg =  'Update transaction success';
            return $this->controller->status(200,$msg);
        }else if($this->user->role == 1){
            $data = [
                'status_place'    => $req['status_place'],
                'message'         => $req['message'],
            ];
            $result = $this->transaction->update($id,$data);
            if($result == false){
                $msg =  'Update transaction fail';
                return $this->controller->status(500,$msg);
            }
            $msg =  'Update transaction success';
            return $this->controller->status(200,$msg);
        }
    }
    public function handleId($id,$result=null){
        if($id == 0){
            return 'Id not fill in';
        }
        if($result == null){
            return  'Id not exactly';
        }
        return false;
    }
    public function addNotify($user,$place){
        $content = $user->name ." created transaction with place ". $place['title']; 
        $data = [
            'title'     => 'You have a transaction',
            'content'   => $content,
            'seen'      => 0,
            'user_id'   => $place['author_id']
        ];
        $this->notify->create($data);
    }
    public function editUserNotify($user,$transaction){
        $content = $user->name ." update status transaction with transaction id ". $transaction['id']; 
        $data = [
            'title'     => 'You have a new notify',
            'content'   => $content,
            'seen'      => 0,
            'user_id'   => $transaction['agency_id']
        ];
        $this->notify->create($data);
    }
    public function editAgencyNotify($user,$transaction){
        $content = $user->name ." update status transaction with transaction id ". $transaction['id']; 
        $data = [
            'title'     => 'You have a new notify',
            'content'   => $content,
            'seen'      => 0,
            'user_id'   => $transaction['user_id']
        ];
        $this->notify->create($data);
    }
}



?>