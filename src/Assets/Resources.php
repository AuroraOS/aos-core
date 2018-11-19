<?php namespace Aos\Assets;

use Closure;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

use Aos\Exception;
use Stolz\Assets\Manager as Manager;

class Resources extends Manager
{
	protected $conf = [
			'async' => false,
			'enable' => false];
	protected $enable = null;
	protected $async = null;

	protected $res = [
		'static' => [],
		'async' => []
	];



	/**
	 * Class constructor.
	 *
	 * @param  array $options See config() method for details.
	 * @return void
	 */
	public function __construct(array $options = array())
	{
		// Forward config options
		if($options)
			parent::__construct($options);

		$this->checkCollection($this->collections);
		$this->moveCSS();
	}

	public function config(array $config)
	{
		// Set regex options
		foreach(array('asset_regex', 'css_regex', 'js_regex', 'no_minification_regex') as $option)
			if(isset($config[$option]) and (@preg_match($config[$option], null) !== false))
				$this->$option = $config[$option];

		// Set common options
		foreach(array('conf', 'enable', 'async', 'css', 'public_dir', 'css_dir', 'js_dir', 'packages_dir', 'pipeline',  'pipeline_dir', 'pipeline_gzip') as $option)
			if(isset($config[$option]))
				$this->$option = $config[$option];

		// Set pipeline options
		foreach(array('fetch_command', 'notify_command', 'css_minifier', 'js_minifier') as $option)
			if(isset($config[$option]) and ($config[$option] instanceof Closure))
				$this->$option = $config[$option];

		// Set collections
		if(isset($config['collections']) and is_array($config['collections']))
			$this->collections = $config['collections'];

		// Autoload assets
		if(isset($config['autoload']) and is_array($config['autoload'])){
			foreach($config['autoload'] as $val => $asset){
				$this->add($asset);
			}

		}

		return $this;
	}

	private function moveCSS(){
		$css = array_merge_recursive($this->res['async']['css'], $this->res['static']['css']);
		unset($this->res['static']['css']);
		$this->res['async']['css'] = $css;
		return $this;
	}

	public function checkCollection($array){

		foreach ($array as $key => $value) {
			$this->async = $value['conf']['async'];
			$this->enable = $value['conf']['enable'];

			if (is_array($value) && !isset($value['default'])) {
				unset($array[$key]['conf'], $array[$key]['defaults']);
				unset($value['conf'], $value['defaults']);

				foreach ($value as $a_key => $a_value) {
					if (preg_match($this->css_regex, $a_value)) {
						$type = 'css';
					} else {
						$type = 'js';
					}

					if ($this->async && $this->enable) {
						$this->res['async'][$type][] = $a_value;
					} else if (!$this->async && $this->enable) {
						$this->res['static'][$type][] = $a_value;
					}


				}
			}
		}


		return $this;
	}

	public function add($asset)
	{
		// More than one asset
		if(is_array($asset))
		{
			foreach($asset as $a)
				$this->add($a);
		}

		// Collection
		elseif(isset($this->collections[$asset]))
			$this->add($this->collections[$asset]);

		// JavaScript asset
		elseif(preg_match($this->js_regex, $asset))
			$this->addJs($asset);

		// CSS asset
		elseif(preg_match($this->css_regex, $asset))
			$this->addCss($asset);

		return $this;
	}


	public function addJs($asset)
	{
		if(is_array($asset))
		{
			foreach($asset as $a)
				$this->addJs($a);

			return $this;
		}

		if( ! $this->isRemoteLink($asset))
			$asset = $this->buildLocalLink($asset, $this->js_dir);

		if( ! in_array($asset, $this->res['static']['js']) && ! in_array($asset, $this->res['async']['js'])){
			if ($this->async) {
				$this->res['async']['js'][] = $asset;
			} elseif($this->async) {
				$this->res['static']['js'][] = $asset;
			}
		}


		return $this;
	}

	public function addCss($asset)
	{
		if(is_array($asset))
		{
			foreach($asset as $a)
				$this->addCss($a);

			return $this;
		}

		if( ! $this->isRemoteLink($asset))
			$asset = $this->buildLocalLink($asset, $this->css_dir);

		if( !in_array($asset, $this->res['static']['css']) && ! in_array($asset, $this->res['async']['css'])){
			if ($this->async) {
				$this->res['async']['css'][] = $asset;
			} elseif(!$this->async){
				$this->res['static']['css'][] = $asset;
			}
		}
		return $this;
	}
}
