<?php

namespace App\Models;

include_once('lib/database.php');

use Database\DB;

class PlaceModel
{
    public $conn;
    private $table = 'tb_place';
    private $_WHERE = '';
    public function __construct()
    {
        $this->conn = new DB();
    }
    public function getAll()
    {
        return $this->conn->getArray($this->table);
    }
    public function get($id = -1, $page = 0, $limit = 20, $type = -1, $city_id = -1, $order = 'recent')
    {
        $this->_WHERE = '';
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
            if ($type != -1) {
                $this->addWhere("type = $type");
            }
            if ($city_id != -1) {
                $this->addWhere("city_id = $city_id");
            }
            $sql = "SELECT * FROM $this->table $this->_WHERE ORDER BY $ORDER LIMIT $page, $limit";
            $data = $this->conn->query($sql);
            return $data;
        }
        return $this->conn->getRowArray($this->table, $id);
    }

    public function countPlaces($type = -1)
    {
        $WHERE = $type == -1 ? '' : 'WHERE type = ' . $type;
        $sql = "SELECT COUNT(*) as total FROM $this->table $WHERE";
        $data = $this->conn->query($sql);
        return $data[0]->total;
    }
    // search by title, location, type, city name (from city table), country name (from country table)
    public function search($q, $page = 0, $limit = 5)
    {
        // search by title, location, type
        $sql = "SELECT * FROM $this->table WHERE title LIKE '%$q%' OR location LIKE '%$q%' OR type LIKE '%$q%' LIMIT $page, $limit";
        $data = $this->conn->query($sql);
        $data = $data ? $data : [];
        // search by city name (from city table)
        $sql = "SELECT * FROM $this->table INNER JOIN tb_city ON tb_place.city_id = tb_city.id WHERE tb_city.name LIKE '%$q%' LIMIT $page, $limit";
        $data2 = $this->conn->query($sql);
        $data2 = $data2 ? $data2 : [];
        // search by country name (from country table)
        $sql = "SELECT * FROM $this->table INNER JOIN tb_country ON tb_place.country_id = tb_country.id WHERE tb_country.name LIKE '%$q%' LIMIT $page, $limit";
        $data3 = $this->conn->query($sql);
        $data3 = $data3 ? $data3 : [];

        $result = array_merge($data, $data2, $data3);
        //get first 10 results
        $result = array_slice($result, $page, $limit);
        return $result;
    }

    public function create($data)
    {
        return $this->conn->insert($this->table, $data);
    }

    public function getStatistic()
    {
        $sql = "SELECT type, COUNT(*) as total FROM $this->table GROUP BY type";
        $data = $this->conn->query($sql);
        return $data;
    }

    public function update($id, $data)
    {
        return $this->conn->update($this->table, $data, $id);
    }
    public function delete($id)
    {
        return $this->conn->delete($this->table, $id);
    }
    public function addWhere($condition)
    {
        // Check if WHERE is empty add WHERE, if not add AND 
        $this->_WHERE = $this->_WHERE == '' ? 'WHERE ' . $condition : $this->_WHERE . ' AND ' . $condition;
    }
}
