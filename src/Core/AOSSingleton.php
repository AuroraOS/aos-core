<?php
namespace Aos\Core;

abstract class AOSSingleton{

	/**
     *  holds an single instance of the child class
     *
     *  @var array of objects
     */

    protected static $instance = [];

    /**
     *  @desc provides a single slot to hold an instance interchanble between all child classes.
     *  @return object
     */
    public static final function getInstance($init = null) : AosSingleton{
        $class = get_called_class(); // or get_class(new static());
        if(!isset(self::$instance[$class]) || !self::$instance[$class] instanceof $class){
            self::$instance[$class] = new static($init); // create and instance of child class which extends Singleton super class
            //echo "new ". $class . PHP_EOL; // remove this line after testing

            return  self::$instance[$class]; // remove this line after testing
        }
        //echo "old ". $class . PHP_EOL; // remove this line after testing
        return static::$instance[$class];
    }

    /**
     * Make constructor abstract to force protected implementation of the __constructor() method, so that nobody can call directly "new Class()".
     */
    abstract protected function __construct();
    /**
     * Make clone magic method private, so nobody can clone instance.
     */
    private function __clone() {}

    /**
     * Make sleep magic method private, so nobody can serialize instance.
     */
    private function __sleep() {}

    /**
     * Make wakeup magic method private, so nobody can unserialize instance.
     */
    private function __wakeup() {
			throw new Exception("Cannot unserialize singleton");
		}


}
//class Bambi extends AosSingleton {}
//class Bombi extends AosSingleton {}
