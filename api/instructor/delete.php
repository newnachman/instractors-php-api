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
    $authkey = $data->authKey;
    $id = $data->instId;

     // Check if $authkey is an authorized key:
    $user = new Users($db);
    if ($user->check_authkey($authkey)) {

        // Before deleting get the actual <image> name on DB:
        $instructor_result = $instructors->read_one($id, false);
        $image_name = $instructor_result['inst_img'];
        
        // Delete and check the result:
        if ($instructors->delete($id)) {
            // Instructor has been deleted, now delete image:
            $result_dlt_img = deleteImg($image_name, $target_dir);

            // Echo result:
            echo json_encode(
                array(
                    'message' => 'Instructor id: [ ' . $id . ' ] deleted.
                     + Delete image status: '
                     . $result_dlt_img
                )
            );
        } else {
            echo json_encode(
                array('message' => 'Problem deleting Instructor id: ' . $id)
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
    // (Not a POST request)
    echo "<h1>SORRY,</h1>";
    echo "<h2>No permission to enter this page :( </h2>";
}

