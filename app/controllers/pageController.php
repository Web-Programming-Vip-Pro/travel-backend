<?php

namespace App\Controllers;

require_once('core/http/Container.php');
require_once('app/services/pageService.php');

use Core\Http\BaseController;
use App\Services\PageService;

class pageController extends BaseController
{
    private $pageService;
    public function __construct()
    {
        $this->pageService = new PageService();
    }
    public function index()
    {
        return $this->pageService->index();
    }

    public function update()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        $id = (int)$req['id'];
        $content = $req['content'];
        return $this->pageService->update($id, $content);
    }
}
