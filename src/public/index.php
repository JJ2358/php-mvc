<!-- INDEX -->
<?php

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/app/functions.php';

define('APP_ROOT', dirname(__DIR__));
define('VIEWS_PATH', APP_ROOT . '/views/');
define('UPLOADS_DIR', 'uploads');
define('UPLOADS_PATH', APP_ROOT . '/public/' . UPLOADS_DIR . '/');

use App\App;
use App\Config;
use App\Controllers\HomeController;

$app = new App();

// add routes
$app->addRoute('/', HomeController::class, 'index');

// run app
$app->run();
