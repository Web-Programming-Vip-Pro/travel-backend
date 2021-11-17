<?php

namespace App\Controllers;

require_once('core/http/Container.php');
require_once('app/services/transactionService.php');

use Core\Http\BaseController;
use App\Services\TransactionService;
class transactionController extends BaseController{
    private $transactionService;
    public function __construct(){
        $this->transactionService = new TransactionService();
    }
    public function index()
    {
        return $this->transactionService->list();
    }
    public function postAdd(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON, true); 
        $place_id = (int)$req['place_id'];
        return $this->transactionService->add($place_id);
    }  
    public function getEdit(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $id = (int)$req['id'];
        return $this->transactionService->getEdit($id);
    }
    public function postEdit(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true ); 
        $id = (int)$req['id'];
        return $this->transactionService->postEdit($id,$req);
    }
}