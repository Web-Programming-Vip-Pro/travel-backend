<?php

namespace App\Helpers;
// create class to build sql condition
class Condition
{
    private $_condition = '';

    // get condition
    public function getCondition()
    {
        return $this->_condition;
    }

    public function addCondition($field, $value, $operator = '=', $is_or = false)
    {
        if ($this->_condition != '') {
            if ($is_or) {
                $this->_condition .= ' OR ';
            } else {
                $this->_condition .= ' AND ';
            }
        }
        $this->_condition .= "$field $operator $value";
    }

    public function addRawCondition($condition, $is_or = false)
    {
        if ($this->_condition != '') {
            if ($is_or) {
                $this->_condition .= ' OR ';
            } else {
                $this->_condition .= ' AND ';
            }
        }
        $this->_condition .= $condition;
    }

    public function isOr($type)
    {
        if ($type == 'or') {
            return true;
        }
        return false;
    }
}
