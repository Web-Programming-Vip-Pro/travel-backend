<?php
namespace App\Models;
include_once('lib/database.php');
use Database\DB;

class NotifyModel {
    public $conn;
    private $table = 'tb_report';
    public function __construct(){
        $this->conn = new DB();
    }
    public function get ($user_id){
        $where = 'place_id= "'.$user_id.'"';
        $sql = 'SELECT * FROM '. $this->table . ' WHERE '. $where;
        return $this->conn->query($sql);
    }
    public function create($data){
       return $this->conn->insert($this->table,$data);
    }
}