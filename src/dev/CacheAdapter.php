<?php
namespace Aos\dev;


interface StorageAdapter {
public function get($key);
public function set($key, $value);
public function touch($key);
public function getFromFile($key);
}


class CacheAdapter implements StorageAdapter {

    private $cacheService, $cacheType;

    public function __construct($cacheType) {
        $this->cacheType = $cacheType;
        // ez egy nagyon egyszerű módszer lesz, meg lehet csinálni interface typehintelt constructor injectionnel is

        if ($cacheType == "filesystem") {
        $this->cacheService = $this->set('test', 'hello'); // az átadott stringnek megfelelően példányosítjuk az osztályokat

        }
        else if ($cacheType == "randomcolleague") {
        $this->cacheService = 'new CacheLayerByRandomColleague';
			} else {
				throw new Exception('Unsupported cache type');
			}
    }

/*  Ez a megoldás egy fokkal stílusosabb
   public function __construct(MultiCacheInterface $cache) {  typehinteltük az interfészt, így csak olyan osztályt fogad el, ami azt implementálta
     $this->cacheService = $cache;  // a typehint garantálja, hogy jó osztályt kaptunk
     $this->cacheType = get_class($cache); // kinyerjük az osztály nevét
     } */

   public function get($key) {
        if($this->cacheType == 'filesystem') {
            $this->touch($key);
        } elseif ($this->cacheType == 'randomcolleague') {
            $this->touch($key);
        }

   }

	 public function set($key, $value) {
		 return $this->cacheService->$key[$value];
 }

 public function touch($key) {
	 return $key;
}

public function getFromFile($key){
return $key;
}
   // és a többi metódus
	 public function cacheToService($key, $value) {
		 return $key[$value];
	 } // ez nem csinál semmit


	 public function getFromService($key) {
		 return $key;
	 } // ez se
}
