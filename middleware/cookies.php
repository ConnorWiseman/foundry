<?php

require_once('../includes/CookiesHandler.php');

$cookies = function(Foundry\Context $ctx, \Closure $next) {
    $ctx->cookies = new Foundry\CookiesHandler;
    return $next();
};
