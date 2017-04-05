<?php

namespace Foundry;

require_once('ReflectionObject.php');
require_once('Request.php');
require_once('Response.php');

final class Context extends ReflectionObject {

    public $req, $res;

    public function __construct() {
        parent::__construct();
        $this->req = new Request;
        $this->res = new Response;
    }

}
