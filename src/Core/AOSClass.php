<?php

namespace Aos\Core;
use Closure;
use Aos\Core\AOSSingletonNoAbs as AosSingleton;
use SmartPage\Aos\Exception;
use SmartPage\Model\Custom;
class AosClass extends AosSingleton{
	protected $config;
	protected $data;
	protected $log;
	protected $api;
	protected $opt;

	public $args;
	public $response;

	protected function __construct(){}

		function clientCode()
	{
			$s1 = AosClass::getInstance();
			$s2 = AosClass::getInstance();
			if ($s1 === $s2) {
					return("Singleton works, both variables contain the same instance.");
			} else {
					return("Singleton failed, variables contain different instances.");
			}

			return [$s1, $s2];
	}

	protected function config(array $config)
	{

		// Set common options
		foreach(array('config', 'enable', 'async', 'css', 'public_dir', 'css_dir', 'js_dir', 'packages_dir', 'pipeline',  'pipeline_dir', 'pipeline_gzip') as $opt)

			if(isset($config[$opt]))
				$this->config[] = $config[$opt];


		return $this;
	}

	public function setLabel($conf = null){
		$this->conf = $conf;
	}

	public function getLabel(){
		return $this->conf;
	}



	public function __get($property) {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }

	public function __set($property, $value) {
    if (!property_exists($key, $property)) {
      return $this->$key = $property;
    }
  }


	public function __call($name, $arguments) {

    //Getting and setting with $this->property($optional);

    if (property_exists(get_class($this), $name)) {


        //Always set the value if a parameter is passed
        if (count($arguments) == 1) {
            /* set */
            $this->$name = $arguments[0];
        } else if (count($arguments) > 1) {
            throw new \Exception("Setter for $name only accepts one parameter.");
        }

        //Always return the value (Even on the set)
        return $this->$name;
    }

    //If it doesn't chech if its a normal old type setter ot getter
    //Getting and setting with $this->getProperty($optional);
    //Getting and setting with $this->setProperty($optional);
    $prefix = substr($name, 0, 3);
    $property = strtolower($name[3]) . substr($name, 4);
    switch ($prefix) {
        case 'get':
            return $this->$property;
            break;
        case 'set':
            //Always set the value if a parameter is passed
            if (count($arguments) != 1) {
                throw new \Exception("Setter for $name requires exactly one parameter.");
            }
            $this->$property = $arguments[0];
            //Always return the value (Even on the set)
            return $this->$name;
        default:
            throw new \Exception("Property $name doesn't exist.");
            break;
    }
}



	    public function __isset( $name )
	    {
	        return method_exists( $this , 'get' . ucfirst( $name  ) )
	            || method_exists( $this , 'set' . ucfirst( $name  ) );
	    }


			public function doAction($array)
    {
        $this->args       =   $array;
        $name             =   __FUNCTION__;
        $this->response[] =   $this->executeCoreCall("APIService.{$name}");
    }

		public function doActionTwo($array, $id)
    {
        $this->args     			=   	$array;
		 		$db 									= 		new Custom($id);
        $name           			=   	__FUNCTION__;
        $this->response[] 		= 		$this->executeCoreCall("APIService.{$id}");
				$this->conf->new($id, $db->get()->toArray());
    }

public function doActionThree($array)
    {
        $this->args[]     =   $array;
        $name           =   __FUNCTION__;
        $this->response[] =   $this->executeCoreCall("APIService.{$name}");
    }

protected function executeCoreCall($service)
    {
        return  $service. 'saved...';
    }

		public function __toString() {
		 return 'How do you sleep?';
 }

 public static function __set_state($an_array) // As of PHP 5.1.0
	 {
			 $obj = new SPDatabase;
			 $obj->var1 = $an_array['var1'];
			 $obj->var2 = $an_array['var2'];
			 return $obj;
	 }


	// public function __debugInfo() {
      //  return [
      //      'conf' => $this->conf->all(),
      //  ];
  //  }
		public static function  __callStatic($name, $arguments) {
        // make sure our class has this method
				if(method_exists($this, $method)) {
		            call_user_func_array(array($this, $method), $args);
		    }
        return null;
    }



function callFunction ($method, $args, $blah) {
	return $args;
}

function myfunction ($method, $args, $blah) {
	return 'fsafsa';
}
function getNames(array $users, $excludeId)
{
    return array_reduce($users, function ($acc, $u) use ($excludeId) {
        if ($u['id'] == $excludeId) {
            return $acc;
        }

        return array_merge($acc, [ $u['name'] ]);
    }, []);
}

}
