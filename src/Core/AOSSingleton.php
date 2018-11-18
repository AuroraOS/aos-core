<?php
namespace Aos\Core;

class AOSSingleton
{
	public static $log;
    public static function getInstance()
    {
        static $instance;

        if (null === $instance) {
            $instance = new self();
        }

        return $instance;
    }




	public static function Log(string $msg = null, $err = 0){
		self::$log[] = array(
			'msg' => $msg,
			'err' => $err
		);
	}

	public static function read(){
		return self::$log;
	}

    // prevent creating multiple instances due to "private" constructor
    private function __construct(){}

    // prevent the instance from being cloned
    private function __clone(){}

    // prevent from being unserialized
    private function __wakeup(){}
}
