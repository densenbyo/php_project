<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Include required files
use controller\CourseController;
use model\Course;
use repository\CourseRepository;
use service\CourseService;
require_once 'vendor/autoload.php';
require_once 'app/bootstrap.php';
require_once 'app/model/Course.php';

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

// Set up Doctrine ORM configuration
$path = ['app/model/Course.php'];
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

// Create instances of your Repositories, Services, and Controllers
$meta = $entityManager -> getClassMetadata(Course::class);
$courseRepository = new CourseRepository($entityManager, $meta);
$courseService = new CourseService($courseRepository);
$courseController = new CourseController($courseService);

$course = $courseService->createCourse("Say always yes!", "Psychology", "Psychology", new DateTime(), 1000);

echo $course->toArray();
echo 'Hello World';
