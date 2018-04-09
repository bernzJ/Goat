<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 1/23/2016
 * Time: 4:15 PM
 */
use Goat\backend\Model;
class AuthVerify extends \Goat\backend\Helper
{
    //private $db;
    public function verify($allowedType = 'all')
    {
        if(!isset($_SESSION['csrf']) || empty($_SESSION['csrf']))
            return ['Error' => 'Session expired !'];

        $t = bin2hex($_SESSION['csrf']);
        $model = new Model();
        $result = $model->query('SELECT `auth_token` FROM `user` WHERE `auth_token` = :t', ['binded' => [':t' => $t] , 'fetch' => true]);


        if($result == null)
            return ['Error' => "Unable to authenticate, please retry or contact administration."];

        
        $token = hex2bin($result['auth_token']);
        $expirate = explode("_", $token)[1];
        $type = explode("_", $token)[2];
        if($allowedType != 'all' && $type != $allowedType)
            return ['Error'=> "You are not allowed to view this content."];

        if(!isset($expirate[1]))
            return ['Error' => 'Wrong token value.'];

        if($expirate < time())
        {
            session_destroy();
            session_start();
            return ['Error'=> 'Session expired, please re-login.'];
        }
        return true;
    }
}