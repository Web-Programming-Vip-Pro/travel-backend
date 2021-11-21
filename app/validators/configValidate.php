<?php

/**
 *  class validate user
 * 
 * 
 */

namespace App\Validator;

class ConfigValidate
{
    public function __construct()
    {
        return;
    }
    public function edit($req)
    {
        $msg = [];
        if (!isset($req['title']) || trim($req['title']) == '') {
            array_push($msg, 'Please fill out title');
        }
        if (!isset($req['description']) || trim($req['description']) == '') {
            array_push($msg, 'Please fill out description');
        }
        if (!isset($req['image']) || trim($req['image']) == '') {
            array_push($msg, 'Please fill out image');
        }
        return $msg;
    }
}
