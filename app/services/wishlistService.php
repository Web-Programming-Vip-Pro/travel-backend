<?php

namespace App\Services;

require_once('app/models/wishlistModel.php');
require_once('app/models/placeModel.php');
require_once('app/models/cityModel.php');
require_once('app/middleware/middleware.php');
require_once('core/http/Container.php');

use App\Models\WishlistModel;
use App\Models\PlaceModel;
use App\Models\CityModel;
use App\Middleware\Middleware;
use Core\Http\BaseController;

class WishlistService
{
    private $place;
    private $wishlist;
    private $controller;
    public function __construct()
    {
        $this->controller = new BaseController();
        $this->place    = new PlaceModel();
        $this->city    = new CityModel();
        $this->wishlist = new WishlistModel();
        $this->middleware   = new Middleware();
    }
    public function list($req)
    {
        $user_id = (int)$req['user_id'];
        $page = isset($req['page']) ? (int)($req['page']) : 0;
        $limit = isset($req['limit']) ? (int)($req['limit']) : 20;
        $type = isset($req['type']) ? $req['type'] : -1;
        $order = isset($req['order']) ? $req['order'] : 'recent';
        $result = $this->wishlist->getForUser($user_id);
        if ($result) {
            foreach ($result as $key => $value) {
                $place = $this->place->get((int)$value->place_id);
                // check type of place
                if ($type !== -1 && $place['type'] !== $type) {
                    unset($result[$key]);
                    continue;
                }
                $result[$key]->place = $place;
                $result[$key]->place['images'] = json_decode($result[$key]->place['images']);
                $result[$key]->city = $this->city->get((int)$result[$key]->place['city_id']);
            }
            // paginate and limit
            $result = array_slice($result, $page * $limit, $limit);
            // order
            if ($order == 'recent') {
                usort($result, function ($a, $b) {
                    return $b->created_at <=> $a->created_at;
                });
            } else if ($order == 'rating') {
                usort($result, function ($a, $b) {
                    return $b->place['stars'] <=> $a->place['stars'];
                });
            } else if ($order == 'max-price') {
                // convert price to int and sort by price (desc)
                usort($result, function ($a, $b) {
                    return (int)$b->place['price'] <=> (int)$a->place['price'];
                });
            } else if ($order == 'min-price') {
                // convert price to int and sort by price (asc)
                usort($result, function ($a, $b) {
                    return (int)$a->place['price'] <=> (int)$b->place['price'];
                });
            }
            return $this->controller->status(200, $result);
        } else {
            return  $this->controller->status(200, []);
        }
    }
    public function toggle($req)
    {
        // get if wishlist is exists
        $userId = $req['user_id'];
        $placeId = $req['place_id'];
        $result = $this->wishlist->findWishlist($placeId, $userId);
        $data = [
            'user_id'       => $userId,
            'place_id'      => $placeId,
        ];
        if ($result == false) {
            $this->wishlist->create($data);
            return $this->controller->status(200, "Added to wishlist");
        } else {
            $this->wishlist->delete((int)$result[0]->id);
            return $this->controller->status(200, "Removed from wishlist");
        }
    }

    public function isInWishlist($req)
    {
        $placeId = $req['place_id'];
        $userId = $req['user_id'];
        $result = $this->wishlist->findWishlist($placeId, $userId);
        if ($result == false) {
            return $this->controller->status(200, null);
        }
        return $this->controller->status(200, $result[0]);
    }

    public function delete($place_id)
    {
        $msgHandleId = $this->handleId($place_id);
        if ($msgHandleId != false) {
            return $this->controller->status(500, $msgHandleId);
        }
        $result = $this->wishlist->delete($place_id);
        if ($result == false) {
            $msg = 'Delete wishlist fail';
            return $this->controller->status(500, $msg);
        }
        $msg = "Delete wishlist success";
        return $this->controller->status(200, $msg);
    }
    public function handleId($id)
    {
        if ($id == 0) {
            return 'Id not fill in';
        }
        $resultByIdPlace = $this->place->get($id);
        if ($resultByIdPlace == false) {
            return 'Place not existed';
        }
        return false;
    }
}
