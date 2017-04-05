<?php

require_once('../includes/Context.php');
require_once('../includes/SessionHandler.php');

$session = function(Foundry\Context $ctx, \Closure $next) {
    $ctx->session = new Foundry\SessionHandler;
    $ctx->session->start();
    return $next();
};
