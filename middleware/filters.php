<?php

$filters = function(Foundry\Context $ctx, \Closure $next) {
    $ctx->filters = Array(
        'FILTER_EMAIL' => Array(
            'filter'  => FILTER_VALIDATE_EMAIL
        ),

        'FILTER_INT' => Array(
            'filter'  => FILTER_VALIDATE_INT
        ),

        'FILTER_UNSAFE' => Array(
            'filter'  => FILTER_CALLBACK,
            'options' => function($input) {
                $filtered = filter_var($input, FILTER_UNSAFE_RAW);
                return ($filtered) ? $filtered: FALSE;
            }
        ),

        'FILTER_SPECIAL_CHARS' => Array(
            'filter'  => FILTER_CALLBACK,
            'options' => function($input) {
                $filtered = filter_var($input, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                return ($filtered) ? $filtered: FALSE;
            }
        )
    );

    return $next();
};
