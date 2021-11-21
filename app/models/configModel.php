<?php

namespace App\Models;

include_once('lib/database.php');

use Database\DB;

class ConfigModel
{
    public $conn;
    // Id of config always = 1
    private $id = 1;
    private $table = 'tb_config';
    public function __construct()
    {
        $this->conn = new DB();
    }
    public function get()
    {
        return $this->conn->getRowArray($this->table, $this->id);
    }

    public function update($data)
    {
        return $this->conn->update($this->table, $data, $this->id);
    }
}
