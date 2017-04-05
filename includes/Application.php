<?php

namespace Foundry;

require_once('Context.php');

final class Application {

    private $middleware;

    public function __construct() {
        $this->middleware = Array();
    }

    public function apply(\Closure ...$middleware) {
        foreach ($middleware as $fn) {
            array_push($this->middleware, $fn);
        }
    }

    public function listen() {
        $ctx = new Context;
        $middleware = $this->middleware;
        $offset = 0;
        $args = Array($ctx, &$next);
        $next = function() use(&$offset, $middleware, &$args) {
            $offset++;
            if (array_key_exists($offset, $middleware)) {
                $fn = $middleware[$offset];
                return call_user_func_array($fn, $args);
            }
            return;
        };

        ob_start();
        try {
            call_user_func_array($this->middleware[$offset], $args);
        } catch (\Exception $e) {
            ob_clean();
            $msg   = $e->getMessage();
            $trace = $e->getTrace();
            $file  = $trace[0]['file'];
            $line  = $trace[0]['line'];
            print "<b>Exception:</b> {$msg} in " .
                  "<b>{$file}</b> on line {$line}";
        }
        header('Content-Length: ' . ob_get_length());
        ob_get_flush();
        exit();
    }

}
