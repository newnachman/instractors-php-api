<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');

ini_set('display_errors', true); 

include_once '../../config/DB.php';
include_once '../../models/Instructors.php';
include_once '../../models/Users.php';

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Instantiate DB $ connect:
    $database = new DB();
    $db = $database->connect();

    // Instantiate Instructor object:
    $instructor = new Instructors($db);

    // Get posted data:
    $data = json_decode(file_get_contents("php://input"));
    $authkey = $data->authKey;
    $id = $data->id;
    // Default is to retrieve only the already approved instructors: 
    $approved_only = true;

    // The default content of "$authkey" from the request is "null" exactly, as a string. So,
    // Check if the recieved authkey equal to "nul":    
    if ($authkey !== 'null') {
        $user = new Users($db);
        // If its not null, its either a real authkey, or fake one. So,
        // Check if its an authorized key:
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
    $result = $instructor->read_one($id, $approved_only);
    if ($result) {
        $instr_item = array(
            'instId'            =>  htmlspecialchars_decode($result['inst_id']),
            'instName'          =>  htmlspecialchars_decode($result['inst_name']),
            'instField'         =>  htmlspecialchars_decode($result['inst_field']),
            'instTitle'         =>  htmlspecialchars_decode($result['inst_title']),
            'instEmail'         =>  htmlspecialchars_decode($result['inst_email']),
            'instPhone'         =>  htmlspecialchars_decode($result['inst_phone']),
            'instLink'          =>  htmlspecialchars_decode($result['inst_link']),
            'instDiploma'       =>  htmlspecialchars_decode($result['inst_diploma']),
            'instStatus'        =>  htmlspecialchars_decode($result['inst_status']),
            'instDescription'   =>  htmlspecialchars_decode($result['inst_descr']),
            'instImage'         =>  htmlspecialchars_decode($result['inst_img'])
        );
        echo json_encode($instr_item);
    } else {
        echo json_encode(
            array('message' => 'Problem, maybe no such id or no authorization.')
        );
    }
    
// } else{ 
//     // (Not a POST request)
//       echo "<h1>SORRY,</h1>";
//       echo "<h2>No permission to enter this page :( </h2>";
//   }
  
  
 