<?php

use Psr\Container\ContainerInterface;

use function DI\factory;

return [

    'db' => factory(function () {
        $aConnParams = [
                    'dbname' => DEFAULT_DATABASE,
                    'user' => DATABASE_USER,
                    'password' => DATABASE_PASS,
                    'host' => DATABASE_HOST,
                    'driver' => 'pdo_mysql',
                ];

        $conn = \Doctrine\DBAL\DriverManager::getConnection($aConnParams);

        return $conn;

    }),

    'db.write' => factory(function (ContainerInterface $c) {
        return $c->get('db');

    }),

    'db.reporting' => factory(function (ContainerInterface $c) {
        return $c->get('db');
    }),

];
