<?php

namespace App\Services;

require_once('core/http/Container.php');
require_once('app/models/appModel.php');
require_once('storage/sendMail.php');

use Core\Http\BaseController;
use App\Models\AppModel;
use Storage\SendMail;

class AppService
{
    private $container;
    private $app;
    private $mail;

    public function __construct()
    {
        $this->container    = new BaseController();
        $this->app = new AppModel();
        $this->mail = new SendMail();
    }

    public function contact($req)
    {
        $name = isset($req['name']) ? $req['name'] : '';
        $email = isset($req['email']) ? $req['email'] : '';
        $subject = isset($req['subject']) ? $req['subject'] : '';
        $message = isset($req['message']) ? $req['message'] : '';

        $result = $this->mail->sendContactMail($name, $email, $subject, $message);
        if ($result) {
            $this->container->status(200, 'Send success');
        } else {
            $this->container->status(500, 'Send failed');
        }
    }
}
