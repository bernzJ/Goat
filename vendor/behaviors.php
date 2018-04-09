<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 11/2/2016
 * Time: 4:59 AM
 */
namespace Goat\backend;

class Behavior{
    private $instances = [];
    public $behaviors = [];

    public static function className()
    {
        return static::class;
    }

    function initiate()
    {
        if(!empty($this->behaviors))
        {
            foreach($this->behaviors as $behavior){
                $className = $behavior['className'];
                $this->instances[$className] = new $className; //instantiate it based on class name
                $inst = $this->instances[$className]; // get our instance
                array_walk($behavior['properties'], function(&$value, $key) use ($inst){
                    $inst->$key = $value;
                });
                array_walk($behavior['functions'], function(&$value, $key) use ($inst){
                    if(method_exists($inst, $key))
                    {
                        $inst->$key($value);
                    }else{
                        $inst->$value();
                    }
                });
            }
        }
    }
    //now the magic
    function __set($name, $value)
    {
        foreach($this->behaviors as $behavior){
            if(in_array($name, $behavior['properties']))
            {
                $this->instances[$behavior['className']]->$name = $value;
                return true;
            }

        }
        throw new \InvalidArgumentException("Cannot set property : " . $name ." not found!" );
    }

    function __get($name)
    {
        foreach($this->behaviors as $behavior){
            if(in_array($name, $behavior['properties']))
                return $this->instances[$behavior['className']]->$name;
        }
        throw new \InvalidArgumentException("Behavior name : " . $name ." not found!" );
    }

    function __call($name, $arguments)
    {
        foreach($this->behaviors as $behavior){
            if(in_array($name, $behavior['functions']))
                return $this->instances[$behavior['className']]->$name($arguments);
        }

        throw new \InvalidArgumentException("Behavior function : " . $name ." not found!" );
    }
}