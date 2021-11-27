<?php

namespace App\Services;

require_once('core/http/Container.php');
require_once('app/models/appModel.php');
require_once('storage/sendMail.php');
require_once('app/models/userModel.php');
require_once('app/models/transactionModel.php');

use Core\Http\BaseController;
use App\Models\AppModel;
use Storage\SendMail;
use App\Models\UserModel;
use App\Models\TransactionModel;

class AppService
{
    private $container;
    private $app;
    private $mail;
    private $user;
    private $transaction;

    public function __construct()
    {
        $this->container    = new BaseController();
        $this->app = new AppModel();
        $this->mail = new SendMail();
        $this->user = new UserModel();
        $this->transaction = new TransactionModel();
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

    public function stats()
    {
        // count number of users
        $users = $this->user->getAll();
        // get number of users with role = 1
        $agencyCount = 0;
        foreach ($users as $user) {
            if ((int)$user['role'] == 1) {
                $agencyCount++;
            }
        }
        // get number of users with role = 0
        $customerCount = 0;
        foreach ($users as $user) {
            if ((int)$user['role'] == 0) {
                $customerCount++;
            }
        }
        // sum value of all transactions
        $transactions = $this->transaction->getAll();
        $transactionsSum = 0;
        foreach ($transactions as $transaction) {
            $transactionsSum += $transaction['value'];
        }
        // count number of transactions
        $transactionCount = count($transactions);
        $result = [
            'totalAgencies' => $agencyCount,
            'totalUsers' => $customerCount,
            'totalTransactions' => $transactionCount,
            'totalRevenue' => $transactionsSum
        ];
        return $this->container->status(200, $result);
    }
}
