<?php

use Huid\PhpcDep\Command\GetCommand;
use Huid\PhpcDep\Command\UpdateCommand;
use Symfony\Component\Console\Application;


require 'vendor/autoload.php';

$app = new Application();
$app->add(new GetCommand());
$app->add(new UpdateCommand());

$app->setName('phpdep');
$app->setVersion('0.0.1');
$app->run();



