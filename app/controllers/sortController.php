<?php

namespace App\Controllers;

include_once('app/models/sortModel.php');
include_once('core/http/Container.php');

use App\Models\SortModel;
use Core\Http\BaseController;
class sortController extends BaseController{
    private $sortModel;
    public function __construct(){
        $this->sortModel = new SortModel();
    }
    // get all report 
    public function recent()
    {
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true );
        $page = isset($req['page']) ? (int)($req['page']) : 0;
        $limit = isset($req['limit']) ? (int)($req['limit']) : 20;
       $result = $this->sortModel->recent($page,$limit);
       return $this->status(200,$result);
    }
    public function maxPrice(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true );
        $q = $req['search'];
        $page = isset($req['page']) ? (int)($req['page']) : 0;
        $limit = isset($req['limit']) ? (int)($req['limit']) : 20;
        $result = $this->sortModel->maxPrice($page,$limit);
        return $this->status(200,$result);
    }
    public function minPrice(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true );
        $q = $req['search'];
        $page = isset($req['page']) ? (int)($req['page']) : 0;
        $limit = isset($req['limit']) ? (int)($req['limit']) : 20;
        $result = $this->sortModel->minPrice($page,$limit);
        return $this->status(200,$result);
    }
    public function rating(){
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true );
        $q = $req['search'];
        $page = isset($req['page']) ? (int)($req['page']) : 0;
        $limit = isset($req['limit']) ? (int)($req['limit']) : 20;
        $result = $this->sortModel->rating($page,$limit);
        return $this->status(200,$result);
    }
}