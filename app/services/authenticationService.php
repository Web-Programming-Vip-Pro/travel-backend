<?php

namespace App\Services;

require_once('vendor/autoload.php');

use \Firebase\JWT\JWT;

class AuthenticationService
{
    public $secretKey;
    public $algorithm;
    public function __construct()
    {
        $this->secretKey = $_ENV['JWT_SECRET'];
        $this->algorithm = 'HS512';
    }
    public function generateJWTToken($user)
    {
        $iat = time();
        // Default expiry time is 1 hour
        $exp = $iat + 3600;
        // Delete user password before creating token
        unset($user->password);
        $payload = array(
            'iat' => $iat,
            'exp' => $exp,
            'user' => $user
        );
        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    public function decodeJWTToken($token)
    {
        $decoded = JWT::decode($token, $this->secretKey, array($this->algorithm));
        return $decoded;
    }
}
