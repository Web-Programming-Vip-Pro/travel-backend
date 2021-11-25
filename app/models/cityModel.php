<?php

namespace App\Models;

include_once('lib/database.php');

use Database\DB;

class CityModel
{
    public $conn;
    private $table = 'tb_city';
    public function __construct()
    {
        $this->conn = new DB();
    }
    public function getAll()
    {
        return $this->conn->getArray($this->table);
    }
    public function get($id = -1, $page = 0, $limit = -1, $order = 'DESC', $text = null)
    {
        if ($id == -1) {
            $sql = '';
            if ($limit == -1) {
                $sql = "SELECT * FROM $this->table ORDER BY id $order";
            } else {
                // if text, search by name or description
                if ($text) {
                    $sql = "SELECT * FROM $this->table WHERE name LIKE '%$text%' OR description LIKE '%$text%' ORDER BY id $order";
                } else {
                    $sql = "SELECT * FROM $this->table ORDER BY id $order LIMIT $page, $limit";
                }
            }
            $data = $this->conn->query($sql);
            return $data;
        }
        return $this->conn->getRowArray($this->table, $id);
    }

    public function search($e)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE `name` LIKE "%' . $e . '%" OR `country_id` = (SELECT `id` FROM `tb_country` WHERE `name` LIKE "%' . $e . '%")';
        $data = $this->conn->query($sql);
        return $data;
    }

    public function create($data)
    {
        return $this->conn->insert($this->table, $data);
    }
    public function update($id, $data)
    {
        return $this->conn->update($this->table, $data, $id);
    }
    public function delete($id)
    {
        return $this->conn->delete($this->table, $id);
    }
}
