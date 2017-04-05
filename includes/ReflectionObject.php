<?php

namespace Foundry;

abstract class ReflectionObject {

    private $methods, $properties;

    public function __construct() {
        $this->methods    = Array();
        $this->properties = Array();
    }

    public function __call($method, Array $args = Array()) {
        if (array_key_exists($method, $this->methods)) {
            return call_user_func_array($this->methods[$method], $args);
        } else {
            throw new \Exception("Method {$method} does not exist");
        }
    }

    public function __get($key) {
        if (array_key_exists($key, $this->properties)) {
            return $this->properties[$key];
        } else {
            return NULL;
        }
    }

    public function __set($key, $value) {
        if ($value instanceof \Closure) {
            $this->methods[$key] = \Closure::bind($value, $this, get_class());
        } else {
            $this->properties[$key] = $value;
        }
    }

}
