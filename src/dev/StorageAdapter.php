<?

namespace Aos\dev;
interface StorageAdapter {
public function get($key);
public function set($key, $value);
public function touch($key);
}
