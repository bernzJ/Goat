<?php
/*
 * Goat V.0.0.1
 * Copyrights to benz
 * -features to add:
 *  real behavours extensions with custom extends
 *  real oop queries (maybe)
 *  cool plugin designers for html objects or php modules
 * todo: actually make use of php reflection in furhter versions of this mvc.
 * */
namespace Goat;

use Goat\backend\View;

class goat{
    public static $app;
    public $config;
    
    //safe trim
    // Does not support flag GLOB_BRACE
    function __construct($config)
    {

        //set encoding
        header('Content-Type: text/html; charset='.$config["encoding"]);
        //NOTE: this shit is important on chrome/ie browser
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'/>";
        // Set our defaults

        $this::$app = $this;
        $this->config = $config;
        $controller = $config['default_controller'];
        $action = 'index';
        $url = '';


        // Get request url and script url
        $request_url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
        $script_url  = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '';

        if($request_url != $script_url && $request_url.'.php' != $script_url)
            $url = trim(preg_replace('/'. str_replace('/', '\/', str_replace('index.php', '', $script_url)) .'/', '', $request_url, 1), '/');

        $url = strtok($url, '?');
        $segments = explode('/', $url, 2);
        //sanitize each segments
        $segments = Generic::safe_array($segments);

        // Do our default checks
        if(isset($segments[0]) && $segments[0] != '')
        {
            $controller = $segments[0];
        }

        if(isset($segments[1]) && $segments[1] != '') $action = $segments[1];
        if(strstr($segments[0], "?") && strstr($segments[0], "=")) $controller = explode('?',$segments[0])[0];



        $path = APP_DIR . 'controllers/' . $controller.'.php';

        if(file_exists($path)){
            require_once($path);
        } else {
            return $this->errorPage();
        }

        // Check the action exists
        if(!method_exists($controller, $action)){
            return $this->errorPage();
        }
        //include the css/js
        $_assets['assets'] = (isset($config['assets'])) ? (file_exists(APP_DIR.'config/'.$config['assets'])) ? require_once(APP_DIR.'config/'.$config['assets']) : null  : null;
        if(!empty($_assets['assets']))
            $_assets['assets'] = array_map(function($dat){return substr_replace($dat, WEB_DIR, 0 , 0);}, $_assets['assets']);

        if(isset($_assets['assets']['js']))
        {
            foreach($_assets['assets']['js'] as $js)
            {
                echo '<script type="text/javascript"  src="'. $js .'"></script>';
            }
        }

        if(isset($_assets['assets']['css']))
        {
            foreach($_assets['assets']['css'] as $css)
            {
                echo '<link rel="stylesheet" href="'. $css .'" type="text/css"></link>';
            }
        }


        // Create object and call method
        $obj = new $controller();
        $obj->forwarded = ['loadModel', 'loadView','loadHelper', 'redirect', 'route'];
        $obj->app = $this;

        return call_user_func_array(array($obj, $action), []);
    }

    public function errorPage()
    {
        $controller = $this->config['error_controller'];
        require_once(APP_DIR . 'controllers/' . $controller . '.php');
        $action = 'index';
        $obj = new $controller();
        return call_user_func_array(array($obj, $action), array());
        //exit(0);
    }
    public function cleanPage()
    {
        ob_end_clean();
        return true;
    }
    //flashes session values can be set/seen once. now can support views count
    public function setFlash($key, $mixed, $views = 0)
    {
        $_SESSION['_flash'][$key] = $mixed;
        $_SESSION['_flash'][$key."_views"] = $views;
    }
    // allow to ignore the delete behaviour
    public function getFlash($key, $ignore = false)
    {
        if(!isset($_SESSION['_flash'][$key]))
            return null;
        $flash = $_SESSION['_flash'][$key];
        if(!$ignore)
        {
            $_views = $_SESSION['_flash'][$key."_views"];
            if($_views == 0)
            {
                unset($_SESSION['_flash'][$key."_views"]);
                unset($_SESSION['_flash'][$key]);
            }else{
                $_views = (int)$_views - 1;
                $_SESSION['_flash'][$key."_views"] = $_views;
            }
        }

        return $flash;
    }
    //get rid of all pending flashes
    public function clearFlash()
    {
        unset($_SESSION['_flash']);
        return true;
    }

    final public function loadModel($name)
    {
        //todo: fix this to trim good thing when unix env
        $path = realpath(APP_DIR .'models/'. Generic::safe_escape_name(strtolower($name)) .'.php'); // ;
        if(!in_array($path, get_required_files()))
        {
            require($path);
        }
        $model = new $name;
        return $model;
    }

    final public function loadView($name)
    {
        $view = new View(Generic::safe_escape_name($name));
        return $view;
    }

    final public function loadHelper($name)
    {
        require(realpath(APP_DIR .'helpers/'. Generic::safe_escape_name(strtolower($name)) .'.php'));
        $helper =  new $name;//(!empty($params)) ? new $name($params): new $name;
        return $helper;
    }
    //unsafe?
    final public function redirect($loc)
    {
        //goat::$app->config['base_url'] .
        //todo: fix this shit
        header('Location: '.  $loc);
        exit;
    }
    //TODO: split / as array, send out as array and rebuild ? or jsut decode
    final public function route()
    {
        $t = $_SERVER['REQUEST_URI'];
        if(strstr($t, "?"))
            $t = strtok($t, "?");
        //$t = Generic::safe_url_escape($t);
        return $t;
    }
}