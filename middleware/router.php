<?php

require_once('../includes/Context.php');
require_once('../includes/Router.php');

$router = function(Array $options = Array()) {

    // Require routes here, for the sake of correct scoping without `use`.

    $auth = function(Foundry\Context $ctx, \Closure $next) {
        if ($ctx->session->userId === NULL) {
            return $ctx->req->redirect('/signin');
        }
        return $next();
    };

    $unAuth = function(Foundry\Context $ctx, \Closure $next) {
        if($ctx->session->userId !== NULL) {
            return $ctx->req->redirect('/');
        }
        return $next();
    };

    $instance = new Foundry\Router;
    // Assign routes here.

    return function(Foundry\Context $ctx, \Closure $next) use($instance) {
        $instance->handleRequest($ctx);
        return $next();
    };
};
