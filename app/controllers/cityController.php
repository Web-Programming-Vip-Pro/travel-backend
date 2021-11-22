<?php

namespace App\Controllers;

require_once('core/http/Container.php');
require_once('app/services/cityService.php');

use Core\Http\BaseController;
use App\Services\CityService;

class cityController extends BaseController
{
    private $cityService;
    public function __construct()
    {
        $this->cityService = new CityService();
    }
    public function index()
    {
        $req = $_REQUEST;
        return $this->cityService->list($req);
    }

    public function get()
    {
        $req = $_REQUEST;
        return $this->cityService->get($req);
    }

    public function search()
    {
        $req = $_REQUEST;
        return $this->cityService->search($req);
    }

    public function page()
    {
        $req = $_REQUEST;
        return $this->cityService->page($req);
    }
    public function postAdd()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        return $this->cityService->add($req);
    }

    public function postEdit()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        $id = (int)$req['id'];
        return $this->cityService->postEdit($id, $req);
    }
    public function delete()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        $id = (int)$req['id'];
        return $this->cityService->delete($id);
    }
}
