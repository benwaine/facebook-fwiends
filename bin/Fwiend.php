<?php 

require_once __DIR__ . '/../vendor/autoload.php';

use Fwiends\CreateCommand;
use Fwiends\DeleteCommand;
use Symfony\Component\Console\Application;
use Bwaine\FacebookTestUserClient\Client;

$client = Client::factory();

$create = new CreateCommand($client);
$delete = new DeleteCommand($client);

$application = new Application();
$application->add($create);
$application->add($delete);

$application->run();

