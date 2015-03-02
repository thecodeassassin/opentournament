<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Routing\Manager as RoutingManager;
use OpenTournament\App as App;
use OpenTournament\App\Exception as AppException;

// application specific constants
define('APPLICATION_DIR', realpath(__DIR__ . '/../app'));
define('CONFIG_DIR', realpath(APPLICATION_DIR . '/config'));
define('CACHE_DIR', realpath(APPLICATION_DIR . '/cache'));
define('LOG_DIR', realpath(APPLICATION_DIR . '/logs'));
define('CONTROLLER_DIR', realpath(__DIR__ . '/../src/OpenTournament/Controller'));


if (!is_file(CONFIG_DIR.'/config.php')) {
    die('No configuration file found');
}

$config = require_once CONFIG_DIR . '/config.php';

try {

    if (!is_writable(LOG_DIR)) {
        throw new AppException(sprintf('Cannot write to log dir (%s)', LOG_DIR));
    }

    $app = new App(array('debug' => false));

    // Initialize the Routing Manager
    $routingManager = new RoutingManager(
        array(
            CONTROLLER_DIR
        ), CACHE_DIR . '/routing'
    );
    $routingManager->generateRoutes();

    $app->run();

} catch (AppException $e) {
    printf('App fault: %s', $e->getMessage() . PHP_EOL);
    echo $e->getTraceAsString();
    die;

} catch (\Exception $e) {
    printf('Unknown fatal error: %s', $e->getMessage());
    echo $e->getTraceAsString();
    die;
}

