<?
namespace Aos\dev;
interface MultiCacheInterface {
      public function getFromService($key);
      public function cacheToService($key, $value);
      public function getFromFile($key);
      public function cacheToFile($key, $value);
      public function touch($key);
 }
