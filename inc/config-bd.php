<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Charge composer

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

define('DNS', $_ENV['DB_DSN']);
define('UTILISATRICE', $_ENV['DB_USER']);
define('MOTDEPASSE', $_ENV['DB_PASS']);
