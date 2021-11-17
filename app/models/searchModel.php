<?php
namespace App\Models;
include_once('lib/database.php');
use Database\DB;

class SearchModel {
    public $conn;
    private $table = 'tb_place';
    public function __construct(){
        $this->conn = new DB();
    }
    public function searchAll($q,$page = 0, $limit =20){
        $firstRow = $page * $limit;
        $sql = 'SELECT * FROM ' .$this->table.  
        ' WHERE `title` LIKE "%' .$q. '%" OR `location` LIKE "%' .$q. '%" 
        ORDER BY `stars` DESC, `reviews` DESC LIMIT ' .$firstRow. ',' .$limit;
        return $this->conn->query($sql);
    }
    public function searchInCity($q,$city_id,$page, $limit){
        $firstRow = $page * $limit;
        $sql = 'SELECT * FROM ' .$this->table.  
        ' WHERE (`title` LIKE "%' .$q. '%" OR `location` LIKE "%' .$q. '%) AND `city_id` =' .$city_id. '
        ORDER BY `stars` DESC, `reviews` DESC LIMIT ' .$firstRow. ',' .$limit;
        return $this->conn->query($sql);
    }
}

