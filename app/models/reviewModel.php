<?php
namespace App\Models;
include_once('lib/database.php');
use Database\DB;

class ReviewModel {
    public $conn;
    private $table = 'tb_review';
    public function __construct(){
        $this->conn = new DB();
    }
    public function getByPlaceId ($place_id){
        $where = 'place_id= "'.$place_id.'"';
        $sql = 'SELECT * FROM '. $this->table . ' WHERE '. $where;
        return $this->conn->query($sql);
    }
    public function create($data){
       return $this->conn->insert($this->table,$data);
    }
}
