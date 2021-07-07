<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');

ini_set('display_errors', true); 

include_once '../../config/DB.php';
include_once '../../models/Instructors.php';
include_once '../../models/Users.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Instantiate DB $ connect:
    $database = new DB();
    $db = $database->connect();

    // Instantiate Instructor object:
    $instructors = new Instructors($db);

    // Get posted data:
    $data = json_decode(file_get_contents("php://input"));
    $authkey = $data->authKey;
    $id = $data->instId;
    $is_approve = $data->isApprove;

    // Check if $authkey is an authorized key:
    $user = new Users($db);
    if ($user->check_authkey($authkey)) {

      // Make the update and check the result:
      if ($instructors->toggle_approve($id, $is_approve)) {
          echo json_encode(
              array(
                  'message' => 'Instructor approve status updated!',
              )
          );
      } else {
        // Problem with update in DB:
          echo json_encode(
              array('message' => 'Problem: instructor **NOT** updated, maybe wrong id or same content (no changes made).')
          );
      }
    } else {
      // Wrong authentication key:
      echo json_encode(
        array('message' => '{-- Security problem, access denied --}')
      );
      die();
    }
} else{
   // Not a POST request:
   echo "<h1>SORRY,</h1>";
   echo "<h2>No permission to enter this page :( </h2>";
}

