<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";
require_once 'app/model/Course.php';

// Set up Doctrine ORM configuration
$path = ['../app/model/Course.php'];
$isDevMode = true;
$config = ORMSetup::createAttributeMetadataConfiguration($path, $isDevMode, null, null, false);

// Database connection settings
$dbParams = array(
    'driver'   => 'pdo_pgsql',
    'user'     => 'postgres',
    'password' => 'atazhanov99M',
    'dbname'   => 'php_project',
    'host' => 'localhost',
    'port' => '5454',
    'url' => 'pdo_pgsql://localhost:5454/php_project'
);

// Create the EntityManager
$connection = DriverManager::getConnection($dbParams, $config);
$entityManager = new EntityManager($connection, $config);
