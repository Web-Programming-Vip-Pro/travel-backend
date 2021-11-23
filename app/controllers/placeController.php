<?php

namespace App\Controllers;

require_once('core/http/Container.php');
require_once('app/services/placeService.php');

use Core\Http\BaseController;
use App\Services\PlaceService;

class placeController extends BaseController
{
    private $placeService;
    public function __construct()
    {
        $this->placeService = new PlaceService();
    }
    public function index()
    {
        $req = $_REQUEST;
        if (isset($req['id'])) return $this->placeService->getPlace((int)$req['id']);
        return $this->placeService->listAll($req);
    }
    public function pages()
    {
        $req = $_REQUEST;
        return $this->placeService->pages($req);
    }

    public function search()
    {
        $req = $_REQUEST;
        return $this->placeService->search($req);
    }

    public function getStatistic()
    {
        $req = $_REQUEST;
        return $this->placeService->getStatistic($req);
    }

    public function postAdd()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        return $this->placeService->add($req);
    }

    public function postEdit()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        $id = (int)$req['id'];
        return $this->placeService->postEdit($id, $req);
    }
    public function delete()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        $id = (int)$req['id'];
        return $this->placeService->delete($id);
    }
}
