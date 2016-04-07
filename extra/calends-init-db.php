<?php

class_alias('Illuminate\Database\Capsule\Manager', 'DB');

$capsule = new DB;

$capsule->addConnection([
    'driver'    => getenv('DB_DRIVER') !== false ? getenv('DB_DRIVER') : 'mysql',
    'host'      => getenv('DB_HOST')   !== false ? getenv('DB_HOST')   : 'localhost',
    'port'      => getenv('DB_PORT')   !== false ? getenv('DB_PORT')   : '3306',
    'username'  => getenv('DB_USER')   !== false ? getenv('DB_USER')   : 'username',
    'password'  => getenv('DB_PASS')   !== false ? getenv('DB_PASS')   : 'password',
    'database'  => getenv('DB_NAME')   !== false ? getenv('DB_NAME')   : 'database',
    'prefix'    => getenv('DB_PREFIX') !== false ? getenv('DB_PREFIX') : '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();
