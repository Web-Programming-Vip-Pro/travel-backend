<?php

namespace App\Services;

require_once('core/http/Container.php');
require_once('app/models/placeModel.php');
require_once('app/models/cityModel.php');
require_once('app/models/userModel.php');
require_once('app/validators/placeValidate.php');
require_once('app/middleware/middleware.php');

use App\Models\PlaceModel;
use App\Models\CityModel;
use App\Models\UserModel;
use App\Middleware\Middleware;
use App\Validator\PlaceValidate;
use Core\Http\BaseController;

class PlaceService
{
    private $place;
    private $middleware;
    private $validate;
    private $container;
    private $user;
    private $city;
    public function __construct()
    {
        $this->container    = new BaseController();
        $this->validate     = new PlaceValidate();
        $this->place        = new PlaceModel();
        $this->middleware   = new Middleware();
        $this->city         = new CityModel();
        $this->user         = new UserModel();
        $this->admin         = $this->middleware->handleAdmin();
    }
    public function listAll($req)
    {
        $page = isset($req['page']) ? (int)($req['page']) : 0;
        $limit = isset($req['limit']) ? (int)($req['limit']) : 20;
        $type = isset($req['type']) ? $req['type'] : -1;
        $order = isset($req['order']) ? $req['order'] : 'recent';
        $city_id = isset($req['city_id']) ? $req['city_id'] : -1;
        $result = $this->place->get(-1, $page, $limit, $type, $city_id, $order);
        if ($result) {
            foreach ($result as $key => $value) {
                $city = $this->city->get((int)$value->city_id);
                $result[$key]->city = $city;
                $result[$key]->images = json_decode($value->images);
                $result[$key]->amenities = json_decode($value->amenities);
                $result[$key]->author = $this->getAuthor((int)$value->author_id);
            }
            return $this->container->status(200, $result);
        }
        return $this->container->status(200, []);
    }

    public function getPlace($id)
    {
        $result = $this->place->get($id);
        if ($result) {
            $result = (object)$result;
            $city = $this->city->get((int)$result->city_id);
            $result->city = $city;
            $result->images = json_decode($result->images);
            $result->amenities = json_decode($result->amenities);
            $result->author = $this->getAuthor((int)$result->author_id);
            return $this->container->status(200, $result);
        }
        return $this->container->status(200, $result);
    }

    public function getAuthor($id)
    {
        $result = $this->user->get($id);
        if ($result) {
            unset($result['password']);
            unset($result['bio']);
            return $result;
        }
        return null;
    }

    public function pages($req)
    {
        $type = isset($req['type']) ? $req['type'] : -1;
        $limit = isset($req['limit']) ? $req['limit'] : 20;
        $totalPlaces = $this->place->countPlaces($type);
        $totalPages = ceil($totalPlaces / $limit);
        return $this->container->status(200, $totalPages);
    }
    public function add($req)
    {

        $msgs = $this->handleValidator($req, 'add');
        if ($msgs != false) {
            return $this->container->status(422, $msgs);
        }
        $req['amenities'] = isset($req['amenities']) ? json_encode($req['amenities']) : '';
        $req['images'] = isset($req['images']) ? json_encode($req['images']) : '';
        $data = [
            'title'         => $req['title'],
            'city_id'       => $req['city_id'],
            'type'          => $req['type'],
            'price'         => $req['price'],
            'images'        => $req['images'],
            'location'      => $req['location'],
            'description'   => $req['description'],
            'amenities'   => $req['amenities'],
            'stars'         => 0.0,
            'reviews'       => 0,
            'status'        => 0,
            'author_id'     => $req['author_id'],
        ];
        $result = $this->place->create($data);
        if ($result == false) {
            $msg = 'Add place to database fail';
            return $this->container->status(500, $msg);
        }
        //$this->addPlacesCity($req['city_id']);
        $msg = 'Add place to database success';
        return $this->container->status(200, $msg);
    }
    // function get edit  place 
    public function getEdit($id)
    {

        $msgHandleId = $this->handleId($id);
        if ($msgHandleId != false) {
            return $this->container->status(500, $msgHandleId);
        }
        $msg = $this->place->get($id);
        return $this->container->status(200, $msg);
    }
    // function post edit place
    public function postEdit($id, $req)
    {

        $msgHandleId = $this->handleId($id);
        if ($msgHandleId != false) {
            return $this->container->status(500, $msgHandleId);
        }
        $msgs = $this->handleValidator($req, 'edit');
        if ($msgs != false) {
            return $this->container->status(422, $msgs);
        }
        $data = [
            'title'         => $req['title'],
            'city_id'       => $req['city_id'],
            'type'          => $req['type'],
            'price'         => $req['price'],
            'location'      => $req['location'],
            'description'   => $req['description'],
        ];
        if (isset($req['status'])) {
            $data['status']        = $req['status'];
        }
        if (isset($req['images'])) {
            $data['images'] = $req['images'];
        }
        $result = $this->place->update($id, $data);
        if ($result == true) {
            $msg =  'Update place success';
            return $this->container->status(200, $msg);
        }
        $msg = 'Update place error';
        return $this->container->status(500, $msg);
    }
    // function delete place
    public function delete($id)
    {

        $msgHandleId = $this->handleId($id);
        if ($msgHandleId != false) {
            return $this->container->status(500, $msgHandleId);
        }
        $this->place->delete($id);
        $msg = 'Delete place success';
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
        $resultGetById = $this->place->get($id);
        if ($resultGetById == null) {
            return  'Id not exactly';
        }
        return false;
    }
    public function addPlacesCity($cityId)
    {
        $result = $this->city->get($cityId);
        $data['total_places'] = $result['total_places'] + 1;
        $this->city->update($cityId, $data);
    }
}
