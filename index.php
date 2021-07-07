<?php
ini_set('display_errors', true); 
include_once 'config/DB.php';
include_once 'models/Instructors.php';

echo 'index';

// Instantiate DB $ connect:
$database = new DB();
$db = $database->connect();

// Instantiate Instructor object:
$instructors = new Instructors($db);
$result = $instructors->read(true);
print_r($result);