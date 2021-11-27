<?php

namespace App\Models;

require_once('helpers/condition.php');
include_once('lib/database.php');

use Database\DB;
use App\Helpers\Condition;

class TransactionModel
{
    public $conn;
    private $condition;
    private $table = 'tb_transaction';
    public function __construct()
    {
        $this->conn = new DB();
        $this->condition = new Condition();
    }

    public function getAll()
    {
        $result = $this->conn->getArray($this->table);
        return $result;
    }

    public function get($id = -1, $userId = -1, $agency_id = -1, $status_place = -1, $page = 0, $limit = 10)
    {
        if ($id == -1) {
            // if user_id add condition
            if ($userId != -1) {
                $this->condition->addCondition('user_id', $userId);
            }
            // if agency_id add condition
            if ($agency_id != -1) {
                $this->condition->addCondition('agency_id', $agency_id);
            }
            // if status_place add condition
            if ($status_place != -1) {
                if (is_array($status_place)) {
                    // convert status_place to string and add '' to each element
                    $status_place = implode("','", $status_place);
                    $this->condition->addRawCondition("status_place IN ('$status_place')");
                } else {
                    $this->condition->addCondition('status_place', $status_place, '=');
                }
            }

            $condition = $this->condition->getCondition() ? 'WHERE ' . $this->condition->getCondition() : '';
            $sql = "SELECT * FROM $this->table $condition ORDER BY id DESC LIMIT $page, $limit";
            $result = $this->conn->query($sql);
            return $result;
        }
        return $this->conn->getRowArray($this->table, $id);
    }

    public function findTransaction($place_id = -1, $user_id = -1, $agency_id = -1, $status_place = -1)
    {
        // check if place_id, add where
        if ($place_id != -1) {
            $this->condition->addCondition('place_id', $place_id, '=');
        }
        // check if user_id, add where
        if ($user_id != -1) {
            $this->condition->addCondition('user_id', $user_id, '=');
        }
        // check if agency_id, add where
        if ($agency_id != -1) {
            $this->condition->addCondition('agency_id', $agency_id, '=');
        }
        // check if status_place, add where
        if ($status_place != -1) {
            // if status_place is array, add raw condition 
            if (is_array($status_place)) {
                // convert status_place to string and add '' to each element
                $status_place = implode("','", $status_place);
                $this->condition->addRawCondition("status_place IN ('$status_place')");
            } else {
                $this->condition->addCondition('status_place', $status_place, '=');
            }
        }
        $condition = $this->condition->getCondition();
        // get transaction from place_id and user_id
        $sql = "SELECT * FROM $this->table WHERE $condition";
        $result = $this->conn->query($sql);
        return $result;
    }

    public function count()
    {
        $condition = $this->condition->getCondition() ? 'WHERE ' . $this->condition->getCondition() : '';
        $sql = "SELECT COUNT(*) as total FROM $this->table $condition";
        $result = $this->conn->query($sql);
        if ($result) {
            return $result[0]->total;
        }
        return $result;
    }

    public function create($data)
    {
        return $this->conn->insert($this->table, $data);
    }
    public function update($id, $data)
    {
        return $this->conn->update($this->table, $data, $id);
    }
}
