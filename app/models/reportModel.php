<?php
namespace App\Models;
include_once('lib/database.php');
use Database\DB;

class ReportModel {
    public $conn;
    private $table = 'tb_report';
    public function __construct(){
        $this->conn = new DB();
    }
    public function get ($id = -1,$page=0,$limit=20){
        if($id == -1){
            $firstRow = $page * $limit;
            $sql = 'SELECT * FROM '.$this->table.' LIMIT '.$firstRow.','.$limit;
            return $this->conn->query($sql);
        }
        return $this->conn->getRowArray($this->table,$id);
    }
    public function create($data){
       return $this->conn->insert($this->table,$data);
    }
}
