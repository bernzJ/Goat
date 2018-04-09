<?php
namespace Goat\backend;

class Helper
{
    public $app;
    public $forwarded;
    public function __call($name, $arguments)
    {
        foreach ($this->forwarded as $forwardedFunction) {
            if ($name == $forwardedFunction) {
                return call_user_func_array([$this->app, $name], $arguments);
            }
        }
        return null;
    }
}
