<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require('vendor/autoload.php');

// instantiate the App object
$app = new \Slim\App();

require('assets/php/routes.php');

// Run application
$app->run();
