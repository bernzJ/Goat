<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 1/19/2016
 * Time: 8:41 PM
 */
use goat\backend\Model;
class usertype extends Model {
    public function getUserTypeFromName($name)
    {
        if($name == 'all')
            return 'all';
        $ret = $this->query("SELECT `type` FROM `user_type` WHERE `name` = :n", ['binded' => [':n' => $name], 'fetch'=> 'true']);
        if(!isset($ret['type']))
            throw new Exception("Error type name :".$name.' not found in the database.');
        return $ret['type'];
    }
    public function getUserTypeName($int)
    {
        $ret = $this->query("SELECT `name` FROM `user_type` WHERE `type` = :t", ['binded' => [':t' => $int], 'fetch'=> 'true']);
        if(!isset($ret['name']))
            throw new Exception("Error type :".$int.' not found in the database.');
        return $ret['name'];
    }
    public function getUserTypes()
    {
        return $this->query("SELECT * FROM `user_type`", ['fetch' => 'all']);
    }
    public function create($params)
    {
        if(!is_array($params) || !isset($params['type']) || !isset($params['name']) || !isset($params['description']))
            return false;
        return $this->query("INSERT INTO `user_type` (`type`, `name`, `description`) VALUES (:t, :n, :d)", ['binded'=> [':t' => $params['type'], ':n' => $params['name'], ':d' => $params['description']]]);
    }
    public function update($params)
    {
        $query = 'UPDATE `user_type` SET '.$this->setNameType($params['name']).' = :v WHERE `type` = :id';

        return $this->query($query, ['binded'=> [':v'=> $params['value'], ':id'=> $params['pk']]]);
    }
    public function delete($pk)
    {
        $query = 'DELETE FROM `user_type` WHERE `type` = :pk';
        return $this->query($query, ['binded' => [':pk' => $pk]]);
    }
    //todo: do a proper model for this
    private function setNameType($n)
    {
        return "`$n`";
    }
}