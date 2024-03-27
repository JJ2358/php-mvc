<?php
session_start(); // Ensure this is the first PHP statement in the file

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/app/functions.php';

define('APP_ROOT', dirname(__DIR__));
define('VIEWS_PATH', APP_ROOT . '/views/');
define('UPLOADS_DIR', 'uploads');
define('UPLOADS_PATH', APP_ROOT . '/public/' . UPLOADS_DIR . '/');

use App\App;
use App\Controllers\HomeController;
use App\Controllers\JobController;
use App\Controllers\AdminController;

$app = new App();

// Add routes
$app->addRoute('/', HomeController::class, 'index');
$app->addRoute('/jobs/{id}', JobController::class, 'showJob');
$app->addRoute('/admin', AdminController::class, 'admin');
$app->addRoute('/admin/fetch-jobs', AdminController::class, 'fetchJobs');
$app->addRoute('/setup-admin', AdminController::class, 'setupAdmin');
$app->addRoute('/admin/create', AdminController::class, 'createAdminUser');
$app->addRoute('/login', AdminController::class, 'login');

// Run app
$app->run();
