<?php

namespace Foundry;

require_once('ReflectionObject.php');

final class Request extends ReflectionObject {

    public $ip, $method, $params, $path, $referrer;

    private function getIpAddress() {
        return getHostByName(getHostName()); // But get the actual IP address.
    }

    private function getReferrer() {
        if (isset($_SERVER["HTTP_REFERER"])) {
            $referrer = $_SERVER["HTTP_REFERER"];
            mb_convert_encoding($referrer, 'UTF-8', 'UTF-8');
            return htmlentities($referrer, ENT_QUOTES, 'UTF-8');
        }
        return '';
    }

    public function __construct() {
        parent::__construct();
        $this->ip       = $this->getIpAddress();
        $this->method   = $_SERVER['REQUEST_METHOD'];
        $this->params   = Array();
        $this->referrer = $this->getReferrer();

        if (count($_GET) === 0) {
            $this->path = '/';
        } else {
            reset($_GET);
            if (key($_GET) === 'action') {
                $this->path = '/' . rtrim(implode('/', $_GET), '/');
            } else {
                $this->path = explode('/', $_SERVER['SCRIPT_NAME']);
                array_pop($this->path);
                $this->path = implode('/', $this->path);
                $this->path = str_replace(
                    $this->path, '',
                    rtrim($_SERVER['REQUEST_URI'], '/')
                );
            }
        }
    }

    public function redirect($path) {
        header("Location: {$path}");
        exit();
    }
}
