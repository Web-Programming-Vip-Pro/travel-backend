<?php

namespace App\Models;

include_once('lib/database.php');

use Database\DB;

class PageModel
{
    public $conn;
    private $table = 'tb_page';
    public function __construct()
    {
        $this->conn = new DB();
    }
    public function get($id = null)
    {
        if (!isset($id)) {
            $sql = 'SELECT * FROM ' . $this->table;
            return $this->conn->query($sql);
        }
        return $this->conn->getRowArray($this->table, $id);
    }

    public function update($id, $content)
    {
        return $this->conn->update($this->table, $content, $id);
    }
}
