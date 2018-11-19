<?php

namespace Aos\ServiceLocator;

class LogService
{
	private static $data;
	public static function doIt(array $arg = []){
		return self::$data = $arg;
	}
}
