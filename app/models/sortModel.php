<?php
namespace App\Models;
include_once('lib/database.php');
use Database\DB;

class SortModel {
    public $conn;
    private $table = 'tb_place';
    public function __construct(){
        $this->conn = new DB();
    }
    public function maxPrice ($page, $limit){
        $firstRow = $page * $limit;
        $sql = 'SELECT * FROM '.$this->table.' ORDER BY price DESC LIMIT ' .$firstRow. ',' .$limit;
        return $this->conn->query($sql);
    }
    public function minPrice ($page, $limit){
        $firstRow = $page * $limit;
        $sql ='SELECT * FROM '.$this->table.' ORDER BY price ASC LIMIT ' .$firstRow. ',' .$limit;
        return $this->conn->query($sql);
    }
    public function rating ($page, $limit){
        $firstRow = $page * $limit;
        $sql ='SELECT * FROM '.$this->table.' ORDER BY stars DESC LIMIT ' .$firstRow. ',' .$limit;
        return $this->conn->query($sql);
    }
    public function recent ($page, $limit){
        $firstRow  = 0;
        if($page != 0){
            $firstRow = $page * $limit;
        }
       $sql ='SELECT * FROM '.$this->table.' ORDER BY updated_at DESC LIMIT ' .$firstRow. ',' .$limit;
       return $this->conn->query($sql);
    }
}
