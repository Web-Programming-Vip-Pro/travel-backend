<?php
namespace Storage;
class Helper {
    public function __construct()
    {
        return null;
    }
    function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function jsonEncodeInfo($req){
        $info = [];
        if(isset($req['address']) && trim($req['address']) != ''){
            $info['address'] = $req['address'];
        }
        if(isset($req['phone']) && trim($req['phone']) != ''){
            $info['phone'] = $req['phone'];
        }
        if(isset($req['birthday']) && trim($req['birthday']) != ''){
            $info['birthday'] = $req['birthday'];
        }
        return json_encode($info);
    }
    public function jsonEncodeSocial($req){
        $social = [];
        if(isset($req['facebook']) && trim($req['facebook']) != ''){
            $social['facebook'] = $req['facebook'];
        }
        if(isset($req['twiter']) && trim($req['twiter']) != ''){
            $social['twiter'] = $req['twiter'];
        }
        if(isset($req['website']) && trim($req['website']) != ''){
            $social['website'] = $req['website'];
        }
        return json_encode($social);
    }
}