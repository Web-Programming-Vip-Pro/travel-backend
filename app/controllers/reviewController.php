<?php

namespace App\Controllers;

include_once('core/http/Container.php');
require_once('app/services/reviewService.php');

use App\Services\ReviewService;
use Core\Http\BaseController;

class reviewController extends BaseController
{
    private $reviewService;
    public function __construct()
    {
        $this->reviewService = new ReviewService();
    }
    // get review with place_id
    public function index()
    {
        $req = $_REQUEST;
        return $this->reviewService->list($req);
    }

    public function delete()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, TRUE);
        return $this->reviewService->delete($req);
    }

    public function getByPlace()
    {
        $req = $_REQUEST;
        return $this->reviewService->getByPlace($req);
    }

    // add review with place_id
    public function postAdd()
    {
        $inputJSON = file_get_contents('php://input');
        $req = json_decode($inputJSON, true);
        return $this->reviewService->add($req);
    }

    public function check()
    {
        $req = $_REQUEST;
        return $this->reviewService->check($req);
    }
}
