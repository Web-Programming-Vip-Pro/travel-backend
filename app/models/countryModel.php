<?php

namespace App\Models;

include_once('lib/database.php');

use Database\DB;

class CountryModel
{
    public $conn;
    private $table = 'tb_country';
    public function __construct()
    {
        $this->conn = new DB();
    }
    public function getAll()
    {
        return $this->conn->getArray($this->table);
    }
    public function get($id = -1, $page = 0, $limit = 20, $text = null)
    {
        if ($id == -1) {
            if ($limit == -1) {
                return $this->getAll();
            }
            // if text, search by name
            $sql = '';
            if ($text != null) {
                $sql = "SELECT * FROM $this->table WHERE name LIKE '%$text%'";
            } else {
                $sql = "SELECT * FROM $this->table LIMIT $page, $limit";
            }
            return $this->conn->query($sql);
        }
        return $this->conn->getRowArray($this->table, $id);
    }

    public function getCities($country_id)
    {
        $sql = "SELECT * FROM tb_city WHERE country_id = $country_id";
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
