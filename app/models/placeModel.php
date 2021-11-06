<?php
namespace App\Models;
include_once('lib/database.php');
use Database\DB;

class PlaceModel {
    public $conn;
    private $table = 'tb_place';
    public function __construct(){
        $this->conn = new DB();
    }
    public function get ($id = -1){
        if($id == -1){
            return $this->conn->getArray($this->table);
        }
        return $this->conn->getRowArray($this->table,$id);
    }
    public function getByEmail ($email){
        $where = 'email= "'.$email.'"';
        $sql = 'SELECT * FROM '. $this->table . ' WHERE '. $where;
        return $this->conn->query($sql);
    }
    public function create($data){
       return $this->conn->insert($this->table,$data);
    }
    public function update($id,$data){
        return $this->conn->update($this->table,$data,$id);
    }
    public function delete($id){
        return $this->conn->delete($this->table,$id);
    }
}
