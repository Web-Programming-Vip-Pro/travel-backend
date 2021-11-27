<?php

namespace App\Controllers;

require_once('core/http/Container.php');
require_once('app/services/appService.php');

use Core\Http\BaseController;
use App\Services\AppService;

class appController extends BaseController
{
    private $appService;
    public function __construct()
    {
        $this->appService = new AppService();
    }

    public function contact()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);

        $this->appService->contact($req);
    }
    public function stats()
    {
        $req = $_REQUEST;
        return $this->appService->stats($req);
    }
}
