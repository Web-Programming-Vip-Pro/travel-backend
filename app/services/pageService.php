<?php

namespace App\Services;

require_once('core/http/Container.php');
require_once('app/models/pageModel.php');
require_once('app/validators/configValidate.php');
require_once('app/middleware/middleware.php');

use App\Models\PageModel;
use Core\Http\BaseController;
use App\Middleware\Middleware;

class PageService
{
    private $page;
    private $container;
    private $middleware;
    public function __construct()
    {
        $this->container    = new BaseController();
        $this->page     = new PageModel();
        $this->middleware   = new Middleware();
        $this->user = $this->middleware->handleAdmin();
    }
    public function index()
    {
        $pages = $this->page->get();
        return $this->container->status(200, $pages);
    }

    public function update($id, $content)
    {
        $page = $this->page->get($id);
        echo $id;
        if ($page) {
            $data = [
                'content' => $content
            ];
            $this->page->update($id, $data);
            return $this->container->status(200, true);
        }
        return $this->container->status(404, 'Page not found');
    }
}
