<?php

namespace Aos\Admin;
use SmartPage\Model\Custom;

use Aos\Core\AOSSingletonNoAbs as AOSSingleton;
class aosAPI extends AOSSingleton {

	protected $connection = [ "server" => null, "type" => null, "login" => null, "url" => null ];
	protected $default;



	protected function __construct($default = null){
		if ($default) {
			$this->default = $default;
			$this->default['url'] = $this->default['host'].':'.$this->default['port'].'/';
		}
	}

	public function logIn($type = 'client'){
		$this->connection['login'] = self::db_post('auth/local', $this->default['login'][$type])["user"];
		return $this;
	}

	public function get($param = null){
		$return = self::db_get($param);
		if ($return) {
			return $return;
		} else {
			$params = explode('/',$param);
			if ($params[0]) {
				$param = $params[0];

				$params = explode('?',$params[1]);
				$where = 'where';
				$params = explode('=',$params[1]);
				return self::localCustomDB($param, 'where');
			}

			return self::localCustomDB($param);
		}
	}

	public function checkConnection($def){
		$this->default = $def;
		$return = self::db_get("infos");
		if ($return) {
			self::logIn('client');
			$this->connection["server"] = true;
			$this->connection["type"] = 'STRAPI';
			$this->connection["url"] = $this->default["url"];
		} else {
			$this->connection["server"] = null;
			$this->connection["type"] = 'Local DB';
		}

		return $this;
	}

	// This model create an instance of the DB Model dynamically, so you can access trough this all the available table.
	private function localCustomDB($name = null, $method = null){
		$result = new Custom($name);
		$result = call_user_func(array($result, $method), 'name', '=', 'app')->get()->toArray();
		if (count($result) == 1) {
			return $result[0];
		}
		return $result;
	}

	// This function intend to use the classic predefined modell, to accass local DB.
	private function localDB($name = null, $method = null){
		$className = ucfirst($name);
		$result = call_user_func(array('SmartPage\Model\\' . $className, $method), 'name', '=', 'admin')->get()->toArray();
		if (!$result) {
			return ['msg' => 'The required local model: "' . $className . '" can not be found.'];
		}

		if (count($result) == 1) {
			return $result[0];
		}

		return $result;
	}

	private function db_post($path = null, $data = null){
		$data_string = json_encode($data);

		$ch = curl_init($this->default['url'] . $path);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'Content-Type: application/json',
		    'Content-Length: ' . strlen($data_string))
		);

		$result = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($result, true);
		return $result;
	}

	private function db_get($path = null){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->default['url'] . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
		$return = json_decode($output, true);

		if (count($return) == 1) {
			return $return[0];
		} else if (!count($return)){
			return false;
		}

		return $return;
	}

	public function __call($function, $args = null){


		return $this->$function;
	}

}
