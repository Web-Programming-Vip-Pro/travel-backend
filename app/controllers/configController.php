<?php

namespace App\Controllers;

require_once('core/http/Container.php');
require_once('app/services/configService.php');

use Core\Http\BaseController;
use App\Services\ConfigService;

class configController extends BaseController
{
    private $configService;
    public function __construct()
    {
        $this->configService = new ConfigService();
    }
    public function index()
    {
        return $this->configService->getConfig();
    }
    public function update()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        return $this->configService->updateConfig($req);
    }
}
