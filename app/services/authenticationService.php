<?php

namespace App\Services;

require_once('vendor/autoload.php');
require_once('config/config.php');

use Exception;
use \Firebase\JWT\JWT;

class AuthenticationService
{
    public $secretKey;
    public $algorithm;
    public function __construct()
    {
        $this->secretKey = JWT_SECRET;
        $this->algorithm = 'HS256';
    }
    public function generateJWTToken($user)
    {
        $iat = time();
        // Default expiry time is 1 hour
        $exp = $iat + 3600;
        // Delete user password before creating token
        unset($user[0]->password);
        $payload = array(
            'iat' => $iat,
            'exp' => $exp,
            'user' => $user[0]
        );
        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    public function decodeJWTToken($token)
    {
        try {
            $decoded = JWT::decode($token, $this->secretKey, array($this->algorithm));
            return $decoded;
        } catch (Exception $e) {
            return null;
        }
    }
}
