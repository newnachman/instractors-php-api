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
    // Default is to retrieve only the already approved instructors: 
    $approved_only = true;

    // Check if the recieved authkey exist & have the correct value:
    if ($authkey !== 'null') {
        $user = new Users($db);
        if ($user->check_authkey($authkey)) {
            // Change the call to the DB so it retrieves also not-yet approved:
            $approved_only = false;
        } else {
            echo json_encode(
                array('message' => '{-- Security problem, access denied --}')
            );
            die();
        }
    }
    $result = $instructors->read($approved_only);
    $num = $result->rowCount();

    // Check if any instructors:
    if($num > 0) {
        // print_r($result->fetch(PDO::FETCH_ASSOC));
        $instr_array = array();
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $instr_item = array(
                'instId'        => htmlspecialchars_decode($inst_id),
                'instName'      => htmlspecialchars_decode($inst_name),
                'instField'     => htmlspecialchars_decode($inst_field),
                'instTitle'     => htmlspecialchars_decode($inst_title),
                'instEmail'     => htmlspecialchars_decode($inst_email),
                'instStatus'    => htmlspecialchars_decode($inst_status),
                'instImg'       => htmlspecialchars_decode($inst_img)
            );
            // Push to "data":
            array_push($instr_array, $instr_item);
        }
        // Turn to JSON & output:
        echo json_encode($instr_array);
    }else {
        echo json_encode(
            array('message' => 'No Instructors Found')
        );
    } 
} else{ 
    // (Not a POST request)
      echo "<h1>SORRY,</h1>";
      echo "<h2>No permission to enter this page :( </h2>";
  }
  
  