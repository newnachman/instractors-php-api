<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');

ini_set('display_errors', true); 

include_once '../../config/DB.php';
include_once '../../models/Users.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Instantiate DB $ connect:
    $database = new DB();
    $db = $database->connect();

    // Instantiate Instructor object:
    $user = new Users($db);
    
    // Get posted data:
    $data = json_decode(file_get_contents("php://input"));

    // Get user data based on *username* login input:
    $user->get_user($data->user);

    // Check if equal
    if ($user->user_name) {
      if 
      (
          $data->user       ==  $user->user_name       
          &&
          $data->password   ==  $user->user_password   
      ){
          echo json_encode(
            array(
              'status'  => 'true',
              'name' => $user->user_name,
              'authKey' => $user->user_special
            )
          );
      } else {
        echo json_encode(
          array(
            'status'  => 'false',
            'message' => 'Wrong password!'
          )
        );
      }
    } else {
        echo json_encode(
            array(
              'status'  => 'false',
              'message' => 'Wrong user name!'
            )
        );
    }
} else{ 
  // (Not a POST request)
    echo "<h1>SORRY,</h1>";
    echo "<h2>No permission to enter this page :( </h2>";
}

