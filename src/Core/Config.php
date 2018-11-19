<?php
namespace Aos\Core;
use Aos\Core\AOSSingletonNoAbs as AosSingleton;

/**
 * Applying the Singleton pattern to the configuration storage is also a common
 * practice. Often you need to access application configurations from a lot of
 * different places of the program. Singleton gives you that comfort.
 */
class Config extends AosSingleton
{
    private $hashmap = [];

    public function getValue($key)
    {
        return $this->hashmap[$key];
    }

    public function setValue($key, $value)
    {
        $this->hashmap[$key] = $value;
    }
}
