<?php

$autoloader = function () {
    foreach (['/../vendor', '/../../..'] as $path) {
        if (file_exists(__DIR__ . $path . '/autoload.php')) {
            return require_once(__DIR__ . $path . '/autoload.php');
        }
    }

    throw new Exception('Composer autoloader not found.  Cannot continue.');
};

$progress = function($message) {
    if ( ! (defined('MIGRATE_SILENTLY') && MIGRATE_SILENTLY))
    {
        file_put_contents('php://stdout', $message);
    }
};

$autoloader();
unset($autoloader);

require_once(__DIR__ . '/calends-init-db.php');
require_once(__DIR__ . '/SchemaFacade.php');

$repository = new Illuminate\Database\Migrations\DatabaseMigrationRepository($capsule->getDatabaseManager(), 'migrations');
if ( ! $repository->repositoryExists()) {
    $progress("Creating migrations table...");
    $repository->createRepository();
    $progress("done.\n");
}

$path  = __DIR__ . '/../laravel/database/migrations';

$files = glob($path . '/*_*.php');
$files = array_map(function ($file) {
    return str_replace('.php', '', basename($file));
}, $files);
sort($files);

$ran = $repository->getRan();

$migrations = array_diff($files, $ran);

if (count($migrations) == 0) {
    $progress("Nothing to migrate.\n");
    return;
}

foreach ($migrations as $file) {
    require_once($path . '/' . $file . '.php');
}

$batch = $repository->getNextBatchNumber();

foreach ($migrations as $file) {
    $class = implode('_', array_slice(explode('_', $file), 4));
    $class = Illuminate\Support\Str::studly($class);
    $migration = new $class;

    $progress("Running {$class}::up() migration...");
    $migration->up();
    $progress("done.\n");

    $repository->log($file, $batch);
}

$progress("All migrations complete.\n");
unset($progress);
