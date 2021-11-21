<?php

namespace App\Models;

include_once('lib/database.php');

use Database\DB;

class PlaceModel
{
    public $conn;
    private $table = 'tb_place';
    public function __construct()
    {
        $this->conn = new DB();
    }
    public function getAll()
    {
        return $this->conn->getArray($this->table);
    }
    public function get($id = -1, $page = 0, $limit = 20, $type = -1, $order = 'recent')
    {
        $ORDER = "";
        switch ($order) {
            case 'max-price':
                $ORDER = 'price DESC';
                break;
            case 'min-price':
                $ORDER = 'price ASC';
                break;
            case 'rating':
                $ORDER = 'stars DESC';
                break;
            case 'recent':
                $ORDER = 'id DESC';
                break;
        }
        if ($id == -1) {
            $WHERE = $type == -1 ? '' : 'WHERE type = ' . $type;
            $sql = "SELECT * FROM $this->table $WHERE ORDER BY $ORDER LIMIT $page, $limit";
            $data = $this->conn->query($sql);
            return $data;
        }
        return $this->conn->getRowArray($this->table, $id);
    }
    public function listType($type, $page, $limit)
    {
        $firstRow = $page * $limit;
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE `type` = ' . $type . ' LIMIT ' . $firstRow . ',' . $limit;
        return $this->conn->query($sql);
    }
    public function listCity($city_id, $type, $page, $limit)
    {
        $firstRow = $page * $limit;
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE `type` = ' . $type . ' AND `city_id` = ' . $city_id . ' LIMIT ' . $firstRow . ',' . $limit;
        return $this->conn->query($sql);
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
