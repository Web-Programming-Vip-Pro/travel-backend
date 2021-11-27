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

    public function get($id = -1, $place_id = -1, $user_id = -1)
    {
        $sql = "SELECT * FROM $this->table WHERE 1";
        if ($id != -1) {
            $sql .= " AND id = $id";
        }
        if ($place_id != -1) {
            $sql .= " AND place_id = $place_id";
        }
        if ($user_id != -1) {
            $sql .= " AND user_id = $user_id";
        }
        $result = $this->conn->query($sql);
        return $result;
    }

    public function getByPlaceId($id, $page = 0, $limit = 20, $order = 'recent')
    {
        $ORDER = '';
        switch ($order) {
            case 'recent':
                $ORDER = 'ORDER BY id DESC';
                break;
            case 'oldest':
                $ORDER = 'ORDER BY id ASC';
                break;
            case 'most-rated':
                $ORDER = 'ORDER BY rate DESC';
                break;
            case 'least-rated':
                $ORDER = 'ORDER BY rate ASC';
                break;
        }
        $sql = "SELECT * FROM $this->table WHERE place_id = $id $ORDER LIMIT $page, $limit";
        return $this->conn->query($sql);
    }

    public function create($data)
    {
        return $this->conn->insert($this->table, $data);
    }
}
