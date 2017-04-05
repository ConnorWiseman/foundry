<?php

namespace Foundry;

require_once('Context.php');

final class Router {

    private $notFound, $routes;

    private function addRoutes($method, $path, Array $callbacks) {
        if (!isset($this->routes[$method])) {
            $this->routes[$method] = Array();
        }
        if (!isset($this->routes[$method][$path])) {
            $this->routes[$method][$path] = Array();
        }
        foreach ($callbacks as $callback) {
            array_push($this->routes[$method][$path], $callback);
        }
    }

    public function __construct() {
        $this->notFound = NULL;
        $this->routes   = Array();
    }

    public function get($path, \Closure ...$callbacks) {
        $this->addRoutes('GET', $path, $callbacks);
        return $this;
    }

    public function handleRequest(Context $ctx) {
        if (array_key_exists($ctx->req->method, $this->routes)) {
            $routes = $this->routes[$ctx->req->method];
        } else {
            return;
        }

        foreach ($routes as $path => $callbacks) {
            $regex = rtrim(preg_replace('/(:\w+)/', '([\w-%]+)', $path), '/');
            if (!preg_match("@^{$regex}/?$@i", $ctx->req->path, $rawParams)) {
                continue;
            }
            array_shift($rawParams);

            if (preg_match_all("/:([\w-%]+)/", $path, $paramKeys)) {
                $paramKeys = $paramKeys[1];
                if(count($paramKeys) !== count($rawParams)) {
                    continue;
                }

                foreach ($paramKeys as $key => $value) {
                    if (!isset($rawParams[$key])) {
                        continue;
                    }
                    $ctx->req->params[$value] = filter_var(
                        $rawParams[$key],
                        FILTER_SANITIZE_FULL_SPECIAL_CHARS
                    );
                }
            }

            $offset = 0;
            $args = Array($ctx, &$next);
            $next = function() use(&$offset, $callbacks, &$args) {
                $offset++;
                if (array_key_exists($offset, $callbacks)) {
                    $fn = $callbacks[$offset];
                    return call_user_func_array($fn, $args);
                }
                return;
            };
            return call_user_func_array($callbacks[$offset], $args);
        }

        if (is_callable($this->notFound)) {
            call_user_func_array($this->notFound, Array($ctx, NULL));
        }
    }

    public function notFound(\Closure $callback) {
        $this->notFound = $callback;
    }

    public function post($path, \Closure ...$callbacks) {
        $this->addRoutes('POST', $path, $callbacks);
        return $this;
    }

}
