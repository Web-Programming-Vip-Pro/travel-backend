<?php
namespace App\Models;
include_once('lib/database.php');
use Database\DB;

class UserModel {
    public $conn;
    private $table = 'tb_users';
    public function __construct(){
        $this->conn = new DB();
    }
    public function get ($id = -1){
        if($id == -1){
            return $this->conn->getArray($this->table);
        }
        return $this->conn->getRowArray($this->table,$id);
    }
    public function create($data){
       return $this->conn->insert($this->table,$data);
    }
    public function update($id,$data){
        return $this->conn->update($this->table,$data,$id);
    }
    public function delete($id){
        return $conn->delete($this->table,$id);
    }
}

// function getAll (){
//     $conn = new DB();
//     $data = [
//         'name' => "Vnutu",
//         'password' => '01',
//         'role' =>'0'
//     ];
//     $sql = "SELECT * FROM tb_users";
//     return $conn->delete("tb_users",1);
// }
// $result = getAll();
// var_dump($result);
