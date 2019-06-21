<?php

use Phalcon\Loader;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Shoutzor\Listener\ErrorListener;

$eventsManager = new EventsManager();
$loader = new Loader;

//Register our Shoutzor namespaces
$loader->registerNamespaces([
  'Shoutzor\Controller'   => $config->application->controllersDir,
  'Shoutzor\Model'        => $config->application->modelsDir,
  'Shoutzor'              => $config->application->libDir
]);

$loader->register();
