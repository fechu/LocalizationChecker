<?php
require_once('vendor/autoload.php');

use Symfony\Component\Console\Application;

$application = new Application();
$application->setName("Localization Checker");

// Add commands to applicatio


$application->run();
