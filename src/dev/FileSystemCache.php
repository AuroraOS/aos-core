<?php
namespace Aos\dev;
use Aos\dev\CacheLayerByRandomColleague;
use Aos\dev\FileSystemCache;
use Aos\dev\CacheAdapter;
use Aos\dev\StorageAdapter;
use Aos\dev\MultiCacheInterface;

interface MultiCacheInterface {
      public function getFromService($key);
      public function cacheToService($key, $value);
      public function getFromFile($key);
      public function cacheToFile($key, $value);
      public function touch($key);
 }




class FileSystemCache implements MultiCacheInterface {
 // az interfész által előírt metódusok


   public function cacheToService($key, $value) {} // ez nem csinál semmit


   public function getFromService($key) {} // ez se

}
