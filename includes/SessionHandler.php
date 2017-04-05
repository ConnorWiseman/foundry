<?php

namespace Foundry;

final class SessionHandler {

    public function __get($key) {
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
        return NULL;
    }

    public function __set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function destroy() {
        return session_destroy();
    }

    public function move($key) {
        if (array_key_exists($key, $_SESSION)) {
            $value = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $value;
        }
        return NULL;
    }

    public function regenerate($del = false) {
        $this->regenerateToken();
        return session_regenerate_id($del);
    }

    public function regenerateToken($length = 32) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes($length));
    }

    public function remove($key) {
        if (array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key]);
        }
    }

    public function start() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            ini_set('session.hash_function', 'sha256');
            ini_set('session.sid_bits_per_character', 4);
            ini_set('session.use_strict_mode', 1);

            session_name('session_id');
            session_set_cookie_params(0, '/', '', FALSE, TRUE);
            session_start();
        }
    }

    public function status() {
        return session_status();
    }

    public function token() {
        if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
            $this->regenerateToken();
        }
        return $_SESSION['csrf_token'];
    }
}
