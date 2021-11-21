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
                $ORDER = 'ORDER BY rating DESC';
                break;
            case 'least-rated':
                $ORDER = 'ORDER BY rating ASC';
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
