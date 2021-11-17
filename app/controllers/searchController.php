<?php

namespace App\Controllers;

require_once('core/http/Container.php');
require_once('app/models/searchModel.php');
use App\Models\SearchModel;
use App\Services\ReportService;
use Core\Http\BaseController;
class searchController extends BaseController{
    private $searchModel;
    public function __construct(){
        $this->searchModel = new SearchModel();
    }

    // search all place 
    public function search()
    {   
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true );
        $q = isset($req['search']) ? $req['search'] : NULL;
        $page = isset($req['page']) ? (int)($req['page']) : 0;
        $limit = isset($req['limit']) ? (int)($req['limit']) : 20;
        $result = $this->searchModel->searchAll($q,$page,$limit);
        return $this->status(200,$result);
    }
    // search place with city and type
    public function searchInCity()
    {   
        $inputJSON = file_get_contents('php://input');
        $req= json_decode( $inputJSON,true );
        $q = isset($req['search']) ?$req['search'] : NULL;
        $city_id = $req['city_id'];
        $page = isset($req['page']) ? (int)($req['page']) : 0;
        $limit = isset($req['limit']) ? (int)($req['limit']) : 20;
        $result = $this->searchModel->searchInCity($q,$city_id,$page,$limit);
        return $this->status(200,$result);
    }
    
}