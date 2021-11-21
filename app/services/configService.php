<?php

namespace App\Services;

require_once('core/http/Container.php');
require_once('app/models/configModel.php');
require_once('app/validators/configValidate.php');
require_once('app/middleware/middleware.php');

use App\Models\ConfigModel;
use Core\Http\BaseController;
use App\Middleware\Middleware;
use App\Validator\ConfigValidate;

class ConfigService
{
    private $config;
    private $container;
    private $middleware;
    private $user;
    private $validate;
    public function __construct()
    {
        $this->container    = new BaseController();
        $this->config     = new ConfigModel();
        $this->middleware   = new Middleware();
        $this->validate = new ConfigValidate();
        $this->user = $this->middleware->handleAdmin();
    }
    public function getConfig()
    {
        $config = $this->config->get();
        return $this->container->status(200, $config);
    }

    public function updateConfig($req)
    {
        if ($this->user == false) {
            return $this->container->status(401, "Unauthorized");
        }
        $msgsValidate = $this->validate->edit($req);
        if ($msgsValidate != false) {
            return $this->container->status(422, $msgsValidate);
        }
        $data = [
            'title' => $req['title'],
            'description' => $req['description'],
            'image' => $req['image'],
        ];
        $update = $this->config->update($data);
        if ($update == false) {
            return $this->container->status(500, "Internal Server Error");
        }
        return $this->container->status(200, "Update Success");
    }
    // function post edit category
    // public function postEdit($id, $req)
    // {
    //     if ($this->user == false) {
    //         return $this->container->status(401, "Unauthorized");
    //     }
    //     $msgHandleId = $this->handleId($id);
    //     if ($msgHandleId != false) {
    //         return $this->container->status(500, $msgHandleId);
    //     }
    //     $msgs = $this->handleValidator($req, 'edit');
    //     if ($msgs != false) {
    //         return $this->container->status(422, $msgs);
    //     }
    //     $data = [
    //         'title'         => $req['title'],
    //         'description'   => $req['description']
    //     ];
    //     $result = $this->category->update($id, $data);
    //     if ($result == true) {
    //         $msg =  'Update cate success';
    //         return $this->container->status(200, $msg);
    //     }
    //     $msg = 'Update cate error';
    //     return $this->container->status(500, $msg);
    // }
}
