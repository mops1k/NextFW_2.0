<?php
namespace NextFW\Bundles\Test\Config;


class Database {
    public static $prefix = 'mc';
    public static $data = [
        'default' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'user' => 'mops1k',
            'pass' => 'ma5Haash',
            'db' => 'mc',
            'charset' => 'utf8',
            'persistent' => false
        ],
        'server' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'user' => 'mops1k',
            'pass' => 'ma5Haash',
            'db' => 'mc_server',
            'charset' => 'utf8',
            'persistent' => false
        ],
    ];
}