<?php

namespace App\Controllers;

include_once('app/models/transactionModel.php');
include_once('app/models/placeModel.php');
include_once('core/http/Container.php');

use App\Models\TransactionModel;
use App\Models\PlaceModel;
use Core\Http\BaseController;
class transactionController extends BaseController{
    private $transaction;
    private $place;
    public function __construct(){
        $this->transaction = new TransactionModel();
        $this->place = new PlaceModel();

    }
    public function index()
    {
        $user_id = (int)$_REQUEST['id'];//get user_id qua token
        $role = 1;// get role qua token
        if($role == 1){
            $result = $this->transaction->getForAcengy($user_id);
            $msgs = [
                'status'    =>  'success',
                'msg'       =>  'Get transaction',
                'data'      =>  $result
            ];
            return $this->status(200,$msgs);
        }
        if($role == 2){
            $result = $this->transaction->getForUser($user_id);
            $msgs = [
                'status'    =>  'success',
                'msg'       =>  'Get transaction',
                'data'      =>  $result
            ];
            return $this->status(200,$msgs);
        }
       
    }
    public function postAdd(){
        $place_id = (int)$_REQUEST['id'];
        if($place_id ==0){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'Id not fill in',
                'data'      =>  null
            ];
            return $this->status(500,$msg);
        }
        $resultByIdPlace = $this->place->get($place_id);
        if($resultByIdPlace == false){
            $msg = [
                'status'    => 'error',
                'msg'       => 'Place not existed',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $data=[
            'user_id'       => 1,//get user_id qua token
            'place_id'      => $place_id,
            'agency_id'      => $resultByIdPlace['author_id'],
            'status_place'   => 0,
        ];
        $result = $this->transaction->create($data);
        if($result == false){
            $msg = [
                'status'    => 'error',
                'msg'       => 'Error add transaction',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $msg = [    
            'status'    => ' success',
            'msg'       => 'Add transaction success',
            'data'      => null
        ]; 
        return $this->status(200,$msg);
    }  
    public function getEdit(){
        $id = (int)$_REQUEST['id'];
        if($id == 0){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'Id not fill in',
                'data'      =>  null
            ];
            return $this->status(500,$msg);
        }
        $resultById = $this->transaction->get($id);
        if($resultById == null){
            $msg = [
                'status'    => 'error',
                'msg'       => 'Id not existed',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $msg = [
            'status'    =>  'success',
            'msg'       =>  'Get transaction with id = '.$id,
            'data'      => $resultById
        ];
        return $this->status(200,$msg);
    }
    public function postEdit(){
        $req = $_POST;
        $id = (int)$_REQUEST['id'];
        if($id ==0){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'Id not fill in',
                'data'      =>  null
            ];
            return $this->status(500,$msg);
        }
        $resultById = $this->transaction->get($id);
        if($resultById == false){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'transaction not exist',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $data = [
            'status_place'    => $req['status_place'],
            'message'         => $req['message'],
        ];
        $result = $this->transaction->update($id,$data);
        if($result == false){
            $msg = [
                'status'    =>  'error',
                'msg'       =>  'update transaction fail',
                'data'      => null
            ];
            return $this->status(500,$msg);
        }
        $msg = [
            'status'    =>  'success',
            'msg'       =>  'update transaction success',
            'data'      => null
        ];
        return $this->status(200,$msg);
    }
}