<?php

namespace Foundry;

final class CookiesHandler {

    public function __get($key) {
        if (array_key_exists($key, $_COOKIES)) {
            return $_COOKIES[$key];
        }
        return NULL;
    }

    public function set($name, $value,
                        $expires = 0, $path = '/',
                        $domain = '', $secure = FALSE,
                        $httpOnly = TRUE) {
        return setcookie($name, $value,
            $expires, $path, $domain, $secure, $httpOnly);
    }
}
