<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');

ini_set('display_errors', true); 

include_once '../../config/DB.php';
include_once '../../models/Instructors.php';
include_once '../../models/Users.php';
include_once 'delete_img.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Instantiate DB $ connect:
    $database = new DB();
    $db = $database->connect();

    // Instantiate Instructor object:
    $instructors = new Instructors($db);

    // Get posted data:
    $data = json_decode(file_get_contents("php://input"));
    
    // Get data from request & remove special chars:
    $instructors->name      =   htmlspecialchars($data->instName);
    $instructors->field     =   htmlspecialchars($data->instField);
    $instructors->title     =   htmlspecialchars($data->instTitle);
    $instructors->email     =   htmlspecialchars($data->instEmail);
    $instructors->phone     =   htmlspecialchars($data->instPhone);
    $instructors->link      =   htmlspecialchars($data->instLink);
    $instructors->diploma   =   htmlspecialchars($data->instDiploma);
    $instructors->descr     =   htmlspecialchars($data->instDescription);
    $instructors->img       =   htmlspecialchars($data->instImage);

    $authkey      =   $data->authKey;
    $id           =   $data->instId;

    // Check if $authkey is an authorized key:
    $user = new Users($db);
    if ($user->check_authkey($authkey)) {
      $result_dlt_img = "";
      // Check - if image was deleted in the front, delete also in server:
      // (there is an option for admin to delete image only, without deleting instructor)
      $img_name =  $data->instImage;
      $delete_code = substr($img_name, -11);
      if ($delete_code === "%%DELETE_ME") {
        //  Get the image name without the 'delete me' extention:
        $only_name = substr($img_name, 0, strpos($img_name, $delete_code));
        // Delete the image:
        $result_dlt_img = deleteImg($only_name, $target_dir);
        // Insert an empty string instead of the image name:
        $instructors->img = "";
      }
      // Make the update and check the result:
      if ($instructors->update($id)) {
        echo json_encode(
            array(
                'message' => 'Instructor updated! + ' . $result_dlt_img
            )
        );
      } else {
        // Problem with update in DB:
        echo json_encode(
            array('message' => 'Problem: instructor **NOT** updated, maybe wrong id or same content (no changes made). + ' . $result_dlt_img)
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

