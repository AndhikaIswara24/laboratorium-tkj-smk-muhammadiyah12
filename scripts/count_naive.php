<?php
// Count rows in t_naive_bayes_dataset using Laravel bootstrap
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Use the DB connection from the container
$db = $app->make('db');
$count = $db->table('t_naive_bayes_dataset')->count();

echo $count . PHP_EOL;
