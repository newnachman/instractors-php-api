<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');

ini_set('display_errors', true); 

include_once '../../config/DB.php';
include_once '../../models/Instructors.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Instantiate DB $ connect:
    $database = new DB();
    $db = $database->connect();

    // Instantiate Instructor object:
    $instructors = new Instructors($db);

    // Get posted data:
    $data = json_decode(file_get_contents("php://input"));
    
    // Remove special chars:
    $instructors->name      =   htmlspecialchars($data->name);
    $instructors->field     =   htmlspecialchars($data->field);
    $instructors->title     =   htmlspecialchars($data->title);
    $instructors->email     =   htmlspecialchars($data->email);
    $instructors->phone     =   htmlspecialchars($data->phone);
    $instructors->diploma   =   htmlspecialchars($data->diploma);
    $instructors->link      =   htmlspecialchars($data->link);
    $instructors->descr     =   htmlspecialchars($data->descr);
    $instructors->img       =   htmlspecialchars($data->img);

    if ($instructors->create()) {
        $id = $instructors->get_last();
        $name = $instructors->name;
        echo json_encode(
            array(
                'message' => 'Instructor created!',
                'created_id' => $id
            )
        );
    } else {
        echo json_encode(
            array('message' => 'Problem: instructor **NOT** created.')
        );
    }
} else{
   // (Not a POST request)
   echo "<h1>SORRY,</h1>";
   echo "<h2>No permission to enter this page :( </h2>";
}

