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
    public function getAll(){
        return $this->conn->getArray($this->table);
    }
    public function get ($id = -1,$page=0,$limit=20){
        if($id == -1){
            $firstRow = $page * $limit;
            $sql = 'SELECT * FROM '.$this->table.' LIMIT '.$firstRow.','.$limit;
            return $this->conn->query($sql);
        }
        return $this->conn->getRowArray($this->table,$id);
    }
    public function listType($type,$page,$limit){
        $firstRow = $page * $limit;
        $sql = 'SELECT * FROM '.$this->table.' WHERE `type` = '.$type. ' LIMIT '.$firstRow.','.$limit;
        return $this->conn->query($sql);
    }
    public function listCity($city_id,$type,$page,$limit){
        $firstRow = $page * $limit;
        $sql = 'SELECT * FROM '.$this->table.' WHERE `type` = '.$type.' AND `city_id` = '.$city_id.' LIMIT '.$firstRow.','.$limit;
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
