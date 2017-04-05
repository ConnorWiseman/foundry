<?php

namespace Foundry;

require_once('ReflectionObject.php');

final class Response extends ReflectionObject {

    public $status;

    public function __construct() {
        $this->status = NULL;
    }

    public function debug($var) {
        ob_start();
        var_dump($var);
        $result = ob_get_clean();
        return $this->send("<pre>{$result}</pre>");
    }

    public function header($string) {
        return header($string);
    }

    public function json($data) {
        $this->header('Content-Type: application/json; charset=utf-8');
        return $this->send(json_encode($data));
    }

    public function send($string) {
        return print $string;
    }

}
