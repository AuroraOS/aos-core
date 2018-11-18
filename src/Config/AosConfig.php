<?php

namespace Aos\Config;

use Aos\Exception;
use Wangoviridans\Config\Nested as Nested;
/**
 * [AosConfig Class]
 * You can easy manage your config data int this array nested class.
 * @package Wangoviriadns\Config
 */
class AosConfig extends Nested {
	protected $container;
	protected $defaults;

	/**
	 * Construct
	 * @param array $container [Array of settings.]
	 */
	public function __construct($container = array(), bool $merge = null) {
		if ($container) {
			$this->setDefaults($container, $merge)->renderDefaults();
		}
	}

	public function search($value, $key, $column){
		$array = $this->cache($key);
		$key = array_search($value, array_column($array, $column));
		return $key+1;
	}

	/**
	 * @param string $option
	 * @param mixed $value
	 * @return $this
	 */
	public function setOption($option, $value) {
		self::setNestedOption($this->container, $option, $value);
		return $this;
	}

	/**
	 * @param array $options
	 * @return $this
	 */
	public function setOptions(array $options) {
		foreach($options as $option => $value) {
			$this->setOption($option, $value);
		}
		return $this;
	}

	/**
	 * @param string $option
	 * @param mixed|null $default
	 * @return mixed
	 */
	public function getOption($option, $default = null) {
		return self::getNestedOption($this->container, $option, $default);
	}

	/**
	 * @param array $options
	 * @return array
	 */
	public function getOptions(array $options) {
		$result = array();
		foreach($options as $option => $default) {
			if (is_numeric($option)) {
				$option = $default;
				$default = null;
			}
			$result[$option] = $this->getOption($option, $default);
		}

		return $result;
	}

	/**
	 * @param $option
	 */
	public function unsetOption($option) {
		self::unsetNestedOption($this->container, $option);
	}

	/**
	 * @param array $options
	 */
	public function unsetOptions(array $options) {
		foreach($options as $option) {
			$this->unsetOption($option);
		}
	}

	/**
	 * @param $option
	 * @return bool
	 */
	public function hasOption($option) {
		return self::hasNestedOption($this->container, $option);
	}


	/**
	 * @param array $context
	 * @param mixed $option
	 * @return bool
	 */
	public static function hasNestedOption(&$context, $option) {
		$pieces = explode('.', $option);
		foreach($pieces as $piece) {
			if (array_key_exists($piece, $context)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @return array
	 */
	public function toArray() {
		return $this->container;
	}

	/**
	 * [defaults description]
	 * This function will return all defaults the available data in elems. Except default paramaters.
	 * @return array [Elems data]
	 */
	public function def(string $key = null){
		if ($key && isset($this->defaults[$key])) {
			return $this->defaults[$key];
		} else if ($key && !isset($this->defaults[$key])) {
			return ['msg.err' => 'This key ['.$key.'] in Default values does not exists.'];
		}
    return $this->defaults;
  }

	/**
	 * [all description]
	 * This function will return all the available data in elems. Except default paramaters.
	 * @return array [Elems data]
	 */
	public function all(){
    return $this->container;
  }

	public function __call($function, $args = null){
		$arr = array();
		$c = count($args);
		if ($args) {
			foreach ($args as $key => $value) {
				$arr[$value] = $this->getOption($function.'.'.$value);
			}
			if ($c > 1 ) {
					return $arr;
			}

			return $arr[array_keys($arr)[0]];


		}

		return $arr[$function] = $this->getOption($function);
	}

	/**
	 * [get description]
	 * This function will return the value of the element is if it is exists.
	 * As a second paramater you can overwrite the previously defined properties.
	 * @param  string 			$key		 [Elem key]
	 * @param  array|string $default [This data will overwrite the previously setted data.]
	 * @return string|array          [If data is not exists, and defult is not set will return the default data in here.]
	 */
	public function get($key, $default = null){
		if ($this->hasOption($key)) {
			return $this->getOption($key, $default);
		}

  }

	public function cache($key = null){
		$source = $this->container;
		if ($key) {
			if (is_array($key)) {
				$source = $this->getOptions($key);
			}
			$source = $this->getOption($key);
		}
		$flatten = function ($input, $parent = []) use (&$flatten) {
		    $return = [];

		    foreach ($input as $k => $v) {
		        if (is_array($v)) {
		            $return = array_merge($return, $flatten($v, array_merge($parent, [$k])));
		        } else {
		            if ($parent) {
		                $key = implode('.', $parent) . '.' . $k;

		                //if (substr_count($key, '[') != substr_count($key, ']')) {
		                //    $key = preg_replace('/\]/', '', $key, 1);
		                //}
		            } else {
		                $key = $k;
		            }

		            $return[$key] = $v;
		        }
		    }

		    return $return;
		};
		return $flatten($source);
	}

	/**
	 * [Add new and call the original function....]
	 * @param  [type] $key   [description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function new($key = null, $value = null){
		return $this->setOption($key, $value);
		return $this;
  }

	/**
	 * [set description]
	 * This method will pre set arrays, and populate the elems propeprty.
	 * @param string 			 $key   [description]
	 * @param string|array $value [description]
	 */
  public function set($key = null, $value = null){
		return $this->processDefaultData($key, $value);
  }

	public function has($option){
		return $this->hasOption($option);
	}

	/**
	 * [setDefaults description]
	 * This function will store all the default values in the variable: default. When new paramater is used, without a value, these values will be used.
	 * If the merge paramater is set to true, the function will merge the default arrays into one.
	 * @param array $config [Store all the default values in case of the new paramater is not contains a valid value.]
	 * @param array $merge [If you pass multiple arrays in a config array, you have an option to merge them as defaults.]
	 */
	private function setDefaults(array $config = null, bool $merge = null){
		if ($merge) {
			$merged = [];
			foreach ($config as $key => $value) {
				$merged = array_merge($merged, $value);
			}
			$this->defaults = $merged;
		} else {
			$this->defaults = $config;
		}

		return $this;
	}

	public function return($function, $args = null, $param = null){
		$new =  &$this->getOption($function);

		return $new->$args($param);
	}
	private function renderDefaults(){
		$this->setOptions($this->defaults);
		return $this;
	}

	/**
	 * [processDefaultData description]
	 * As a set() function can call this private function what has to process the data.
	 * First case scenario: The key is specified, and the values is in array. In that case make a new sub level, and process the values.
	 * Second case scenario: The key is missing, only one paramater presented as an array. In that case this will merge with the default settings.
	 * Third case scenario: Neither statment is true, so the system will return a configuration error message as a paramater.
	 * @param  string|array 		 $_key   [If it is string new subarray will generated.]
	 * @param  array  					 $_value [If this presented and this is an array, the paramaters will merged together with the __constructed paramaters.]
	 * @return object         					 [Return $this as object.s]
	 */
	private function processDefaultData($_key = null, $_value = null){
		/*** First case scenario ***/
		if (is_string($_key) && is_array($_value)) {
			foreach ($_value as $key => $value) {
				$this->setOption($_key.'.'.$key, $value);
			}
			return $this;
		}
		/*** Second case scenario ***/
		else if (is_array($_key) && !$_value) {
			foreach ($_key as $key => $value) {
				$this->setOption($key, $value);
			}
			return $this;
		}
		/*** Third case scenario ***/
		else{
			return $this->setOption($_key, $_value);
		}
  }
}
