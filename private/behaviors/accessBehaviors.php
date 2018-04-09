<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 10/31/2016
 * Time: 5:00 PM
 */
namespace Goat\backend;
use Goat\goat;
class accessBehavior extends Behavior{
    public $isLogged = false;
    public $user = null;

    public function init()
    {
        if($this->isLogged)
        {
            $helper = goat::$app->loadHelper('authverify');
            $model = goat::$app->loadModel('usertype');
            $resp = $helper->verify($model->getUserTypeFromName($this->user));
            if(isset($resp['Error']))
            {
                if(isset($resp['Error'])) {
                    (isset(goat::$app->config['display_login_error']) && goat::$app->config['display_login_error'] == 'true') ? goat::$app->setFlash("_login_error", $resp) : null;
                    goat::$app->redirect(goat::$app->config['error_controller'] . '/' . goat::$app->config['error_type']['403']);
                }
            }
        }


    }

}