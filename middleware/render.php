<?php

require_once('../includes/Context.php');
require_once('../includes/TemplateRenderer.php');

$render = function(Array $options = Array()) {
    $options = array_merge(Array(
        'layoutsDir'    => '/layouts',
        'partialsDir'   => '/partials',
        'templatesPath' => realpath(__DIR__ . '/../templates'),
        'viewsDir'      => '/views'
    ), $options);

    return function(Foundry\Context $ctx, \Closure $next) use($options) {
        $ctx->res->render = function(
            $viewName,
            Array $context = Array(),
            $layoutName = 'default'
        ) use($options, $ctx) {
            $renderer = new Foundry\TemplateRenderer($options);
            $txt = $renderer->render($viewName, $context, $layoutName);
            $ctx->res->header('Content-Type: text/html; charset=utf-8');
            $ctx->res->send($txt);
        };
        return $next();
    };
};
