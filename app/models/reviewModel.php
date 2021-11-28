<?php

namespace App\Models;

include_once('lib/database.php');

use Database\DB;

class ReviewModel
{
    public $conn;
    private $table = 'tb_review';
    public function __construct()
    {
        $this->conn = new DB();
    }

    public function get($id = -1, $place_id = -1, $user_id = -1, $page = 0, $limit = 10, $order = 'recent')
    {
        $ORDER = 'ORDER BY';
        switch ($order) {
            case 'recent':
                $ORDER .= ' id DESC';
                break;
            case 'oldest':
                $ORDER .= ' id ASC';
                break;
            case 'most-rated':
                $ORDER .= ' rate DESC';
                break;
            case 'least-rated':
                $ORDER .= ' rate ASC';
                break;
        }
        $sql = "SELECT * FROM $this->table WHERE 1 ";
        if ($id != -1) {
            $sql .= " AND id = $id";
        }
        if ($place_id != -1) {
            $sql .= " AND place_id = $place_id";
        }
        if ($user_id != -1) {
            $sql .= " AND user_id = $user_id";
        }
        $sql .= "$ORDER LIMIT $page, $limit";
        $result = $this->conn->query($sql);
        return $result;
    }

    public function getByPlaceId($id, $page = 0, $limit = 20, $order = 'recent')
    {
        $ORDER = 'ORDER BY';
        switch ($order) {
            case 'recent':
                $ORDER .= ' id DESC';
                break;
            case 'oldest':
                $ORDER .= ' id ASC';
                break;
            case 'most-rated':
                $ORDER .= ' rate DESC';
                break;
            case 'least-rated':
                $ORDER .= ' rate ASC';
                break;
        }
        $sql = "SELECT * FROM $this->table WHERE place_id = $id $ORDER LIMIT $page, $limit";
        return $this->conn->query($sql);
    }

    public function countByPlaceId($id)
    {
        // id can either be number or array
        if (is_array($id)) {
            $id = implode(',', $id);
        }
        $sql = "SELECT COUNT(*) AS count FROM $this->table WHERE place_id IN ($id)";
        $result = $this->conn->query($sql);
        return $result[0]->count;
    }

    public function countAll()
    {
        $sql = "SELECT COUNT(*) AS count FROM $this->table";
        $result = $this->conn->query($sql);
        return $result[0]->count;
    }

    public function create($data)
    {
        return $this->conn->insert($this->table, $data);
    }
}
