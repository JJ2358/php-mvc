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
$app->addRoute('/admin/fetch-jobs', AdminController::class, 'fetchAndSaveJobs');
$app->addRoute('/setup-admin', AdminController::class, 'setupAdmin');
$app->addRoute('/admin/create', AdminController::class, 'createAdminUser');
$app->addRoute('/login', AdminController::class, 'login');
$app->addRoute('/admin', AdminController::class, 'adminDashboard');
$app->addRoute('/logout', AdminController::class, 'logout');
$app->addRoute('/apply-for-job/{id}', JobController::class, 'applyForJob');




// Run app
$app->run();
