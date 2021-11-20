<?php

namespace App\Controllers;

require_once('core/http/Container.php');
require_once('app/services/countryService.php');

use Core\Http\BaseController;
use App\Services\CountryService;

class countryController extends BaseController
{
    private $countryService;
    public function __construct()
    {
        $this->countryService = new CountryService();
    }
    public function index()
    {
        $req = $_REQUEST;
        return $this->countryService->list($req);
    }
    public function page()
    {
        $req = $_REQUEST;
        return $this->countryService->page($req);
    }
    public function postAdd()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        return $this->countryService->add($req);
    }
    public function getEdit()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        $id = (int)$req['id'];
        return $this->countryService->getEdit($id);
    }
    public function postEdit()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        $id = (int)$req['id'];
        return $this->countryService->postEdit($id, $req);
    }
    public function delete()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        $id = (int)$req['id'];
        return $this->countryService->delete($id);
    }
}
