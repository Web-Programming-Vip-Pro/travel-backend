<?php

namespace App\Models;

include_once('lib/database.php');

use Database\DB;

class WishlistModel
{
    public $conn;
    private $table = 'tb_wishlist';
    public function __construct()
    {
        $this->conn = new DB();
    }
    public function get($id = -1, $page = 0, $limit = 20)
    {
        if ($id == -1) {
            $firstRow = $page * $limit;
            $sql = 'SELECT * FROM ' . $this->table . ' LIMIT ' . $firstRow . ',' . $limit;
            return $this->conn->query($sql);
        }
        return $this->conn->getRowArray($this->table, $id);
    }
    public function getForUser($user_id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE user_id = ' . $user_id;
        return $this->conn->query($sql);
    }

    public function findWishlist($place_id, $user_id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE user_id = ' . $user_id . ' AND place_id = ' . $place_id;
        return $this->conn->query($sql);
    }
    public function create($data)
    {
        return $this->conn->insert($this->table, $data);
    }
    public function delete($id)
    {
        return $this->conn->delete($this->table, $id);
    }
}
