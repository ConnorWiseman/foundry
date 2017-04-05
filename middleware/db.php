<?php

require_once('../config/db.php');
require_once('../includes/Context.php');
require_once('../includes/DatabaseHandler.php');

$db = function(Foundry\Context $ctx, \Closure $next) use ($dbconfig) {
    $ctx->dbh = new Foundry\DatabaseHandler;
    $ctx->dbh->connect($dbconfig);
    return $next();
};
