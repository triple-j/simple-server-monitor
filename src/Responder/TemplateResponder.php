<?php

namespace trejeraos\SimpleServerMonitor\Responder;

use League\Plates\Engine;
//use League\Plates\Template\Template;
//use Spark\Adr\PayloadInterface;
use Spark\Responder\PlatesResponder;

class TemplateResponder extends PlatesResponder
{

    public function __construct()
    {
        $engine = new Engine(__DIR__ . '/../../templates');

        parent::__construct($engine);
    }

}
