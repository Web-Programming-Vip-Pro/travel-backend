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
    public function get($id = -1, $page = 0, $limit = 20, $order = 'DESC')
    {
        if ($id == -1) {
            if ($limit = -1) {
                $sql = 'SELECT * FROM ' . $this->table . ' ORDER BY `id` ' . $order;
            } else {
                $firstRow = $page * $limit;
                $sql = 'SELECT * FROM ' . $this->table . ' ORDER BY `id` ' . $order . ' LIMIT ' . $firstRow . ',' . $limit;
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
