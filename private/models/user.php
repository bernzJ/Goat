<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 1/19/2016
 * Time: 8:41 PM
 */
use goat\backend\Model;
class user extends Model {

    public function actionAuth($data)
    {

        $data['username'] = filter_var($data['username'], FILTER_SANITIZE_EMAIL);
        if (!filter_var($data['username'], FILTER_VALIDATE_EMAIL))
        {
            return false;
        }

        $result = $this->query('SELECT * FROM `user` WHERE `username` = :u',[ 'binded' =>[':u' => $data['username']] , 'fetch' => 'true']); //[ 'binded' =>[':u' => $data['username']] , 'fetch' => 'true']

        if(!empty($result) && password_verify($data['password'], $result['password']))
        {
            $this->csrf($result['id'], $result['type']);
            return true;
        }
        return false;
    }
    private function get_ip_address(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe

                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
        return "NAN";
    }
    private function csrf($id, $type)
    {
        $expiration = time() + 86400; //24 hours
        $token = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM).'_'.$expiration.'_'.$type;
        $_SESSION['csrf'] = $token;

        $this->query('UPDATE `user` SET `auth_token` = :t, `ip` = :i WHERE id = :id', [ 'binded'  => [':t' => bin2hex($_SESSION['csrf']), ':id' => $id, ':i'=> $this->get_ip_address()]]);
        return true;
    }
    public function getUser()
    {
        $token =  $_SESSION['csrf'];
        if(empty($token))
            return false; // should never happen.
        return $this->query('SELECT `username`, `type`, `id` FROM `user` WHERE `auth_token` = :t', [ 'binded'  => [':t' => bin2hex($token)], 'fetch'=>'true']);
    }
    public function getAllUsers()
    {

    }
    public function updateUser($data)
    {
        $token =  $_SESSION['csrf'];
        if(empty($token))
            return false; // should never happen.

        $this->query('UPDATE `user` SET `username` = :u, `password` = :p WHERE `auth_token` = :t', ['binded' => [':u' => $data['user'], ':p' => password_hash($data['pass'], PASSWORD_DEFAULT), ':t' => bin2hex($token)]]);

        return true;
    }
    //TODO: make sure they are admin since these two function are exposed into usersettings panel
    public function updateUserAdmin($params)
    {
        $query = 'UPDATE `user` SET '.$this->setNameType($params['name']).' = :v WHERE `id` = :id';

        return $this->query($query, ['binded'=> [':v'=> $params['value'], ':id'=> $params['pk']]]);
    }
    public function deleteUserAdmin($pk)
    {
        $query = 'DELETE FROM `user` WHERE id = :pk';
        return $this->query($query, ['binded' => [':pk' => $pk]]);
    }
    //todo: do a proper model for this
    private function setNameType($n)
    {
        return "`$n`";
    }
    public function createUser($data)
    {
        //verify
        if(!isset($data['username']) || !isset($data['password']) || !isset($data['type']) || !is_numeric($data['type']))
            return "Missing parameter, aborted user creation.";


        return $this->query('INSERT INTO `user` (`username`, `password`, `type`) VALUES (:u, :p , :t)', ['binded' => [':u' => $data['username'] , ':p' => password_hash($data['password'], PASSWORD_DEFAULT), ':t' => $data['type']]]);
    }
}