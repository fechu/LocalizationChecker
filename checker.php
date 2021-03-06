<?php
require_once('vendor/autoload.php');

use Symfony\Component\Console\Application;
use LocalizationChecker\Command\StringsCommand;
use LocalizationChecker\Command\StringsFolderCommand;

$application = new Application();
$application->setName("Localization Checker");
$application->setVersion("0.1.0");

// Add commands to applicatio
$application->add(new StringsCommand());
$application->add(new StringsFolderCommand());

$application->run();
