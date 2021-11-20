<?php

namespace App\Services;

require_once('core/http/Container.php');
require_once('app/models/cityModel.php');
require_once('app/models/countryModel.php');
require_once('app/validators/cityValidate.php');
require_once('app/middleware/middleware.php');

use App\Models\CityModel;
use App\Models\CountryModel;
use App\Validator\CityValidate;
use App\Middleware\Middleware;
use Core\Http\BaseController;

class CityService
{
    private $city;
    private $validate;
    private $middleware;
    private $container;
    private $user;
    public function __construct()
    {
        $this->container    = new BaseController();
        $this->validate     = new CityValidate();
        $this->city         = new CityModel();
        $this->country     = new CountryModel();
        $this->middleware   = new Middleware();
        $this->user         = $this->middleware->handleAdmin();
    }
    public function list($req)
    {
        $page = isset($req['page']) ? (int)($req['page']) : 0;
        $limit = isset($req['limit']) ? (int)($req['limit']) : 20;
        $order = isset($req['order']) ? $req['order'] : 'DESC';
        $result = $this->city->get(-1, $page, $limit, $order);
        foreach ($result as $key => $value) {
            $result[$key]->country = $this->country->get((int)$value->country_id);
            unset($result[$key]->country_id);
        }
        return $this->container->status(200, $result);
    }
    public function page($req){
        $limit = isset($req['limit']) ? (int)($req['limit']) : 20;
        $result = $this->city->getAll($limit);
        $totalRow = count($result);
        $pages = (int)($totalRow / $limit) + 1;
        return $this->container->status(200,$pages);
    }
    public function add($req)
    {
        $msgs = $this->handleValidator($req, 'add');
        if ($msgs != false) {
            return $this->container->status(422, $msgs);
        }
        $data = [
            'name'          => $req['name'],
            'country_id'    => $req['country_id'],
            'description'   => $req['description'],
            'total_places'  => 0,
            'image_cover'   => $req['image_cover'],
        ];
        // image
        $result = $this->city->create($data);
        if ($result == false) {
            $msg = 'Add city to database fail';
            return $this->container->status(500, $msg);
        }
        $msg = 'Add city to database success';
        return $this->container->status(200, $msg);
    }
    // function get edit  city 
    public function getEdit($id)
    {
        if ($this->user == false) {
            return $this->container->status(401, "Unauthorized");
        }
        $msgHandleId = $this->handleId($id);
        if ($msgHandleId != false) {
            return $this->container->status(500, $msgHandleId);
        }
        $msg = $this->city->get($id);
        return $this->container->status(200, $msg);
    }
    // function post edit city
    public function postEdit($id, $req)
    {
        if ($this->user == false) {
            return $this->container->status(401, "Unauthorized");
        }
        $msgHandleId = $this->handleId($id);
        if ($msgHandleId != false) {
            return $this->container->status(500, $msgHandleId);
        }
        $msgs = $this->handleValidator($req, 'edit');
        if ($msgs != false) {
            return $this->container->status(422, $msgs);
        }
        $data = [
            'name'          => $req['name'],
            'country_id'    => $req['country_id'],
            'description'   => $req['description'],
        ];
        if (isset($req['image_cover'])) {
            $data['image_cover'] = $req['image_cover'];
        }
        $result = $this->city->update($id, $data);
        if ($result == true) {
            $msg =  'Update city success';
            return $this->container->status(200, $msg);
        }
        $msg = 'Update city error';
        return $this->container->status(500, $msg);
    }
    // function delete city
    public function delete($id)
    {
        if ($this->user == false) {
            return $this->container->status(401, "Unauthorized");
        }
        $msgHandleId = $this->handleId($id);
        if ($msgHandleId != false) {
            return $this->container->status(500, $msgHandleId);
        }
        $this->city->delete($id);
        $msg = 'Delete city success';
        return $this->container->status(200, $msg);
    }
    // fucntion handle validate 
    public function handleValidator($req, $action)
    {
        $msgs = null;
        if ($action == 'add') {
            $msgs = $this->validate->add($req);
        } else {
            $msgs = $this->validate->edit($req);
        }
        if (count($msgs) > 0) {
            return $msgs;
        }
        return false;
    }
    // fucntion handle id if not fill or not exact
    public function handleId($id)
    {
        if ($id == 0) {
            return 'Id not fill in';
        }
        $resultGetById = $this->city->get($id);
        if ($resultGetById == null) {
            return  'Id not exactly';
        }
        return false;
    }
}
