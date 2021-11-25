<?php

namespace App\Controllers;

require_once('core/http/Container.php');
require_once('app/services/transactionService.php');

use Core\Http\BaseController;
use App\Services\TransactionService;

class transactionController extends BaseController
{
    private $transactionService;
    public function __construct()
    {
        $this->transactionService = new TransactionService();
    }
    public function index()
    {
        $req = $_REQUEST;
        return $this->transactionService->list($req);
    }

    public function user()
    {
        $req = $_REQUEST;
        return $this->transactionService->getPlacesByUserTransaction($req);
    }

    public function postAdd()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        return $this->transactionService->add($req);
    }
    public function get()
    {
        $req = $_REQUEST;
        return $this->transactionService->get($req);
    }
    public function getEdit()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        $id = (int)$req['id'];
        return $this->transactionService->getEdit($id);
    }
    public function postEdit()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        $id = (int)$req['id'];
        return $this->transactionService->postEdit($id, $req);
    }
}
