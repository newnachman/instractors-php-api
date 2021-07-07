<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');

ini_set('display_errors', true); 

$new_img_name = "";
$errorMsg = "";
$dir = "images/";
$target_dir = "../../" . $dir;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if ther is a file sent by request:
    if($_FILES['image']){

        // Check if folder exist, if not - create it:
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Handle image:
        $target_file = $target_dir . basename($_FILES['image']["name"]);
        $uploadOk = true;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES['image']["tmp_name"]);
        if($check !== false) {
            $uploadOk = true;
        } else {
            $errorMsg .=  " + Problem: File is not an image.";
            $uploadOk = false;
        }

        // Check file size
        if ($_FILES['image']["size"] > 600000) {
            $errorMsg .= " + Problem: Image is too large (max size: 500 kb).";
            $uploadOk = false;
        }

        // Allow certain file formats
        if(
            $imageFileType != "jpg"  && 
            $imageFileType != "png"  && 
            $imageFileType != "jpeg" && 
            $imageFileType != "gif" 
            ) {
                $errorMsg .= " + Problem: only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = false;
            }

        if ($uploadOk == false) {
            $errorMsg .= " + Sorry, your file was not uploaded.";
        } else {
            // If everything is ok, try to save the file:
            $new_img_name = createName($_FILES['image']["name"]);
            $new_target_file =  $target_dir . $new_img_name;
            if (move_uploaded_file($_FILES['image']["tmp_name"], $new_target_file)) {
                $uploadOk = true;
            } else {
                $uploadOk = false;
                $errorMsg .= " + Sorry, there was an error *saving* your image.";
            }
        }

        // Handle response:
        if ($uploadOk) {   
            $new_img_path = getCurrentUrl() . "/" . $dir . $new_img_name;      
            $response = array(
                "status" => "success",
                "img-name" => $new_img_path
            );
        }else {
            $response = array(
                "status" => "error",
                "message" => $errorMsg
            );
        }
    }else{
        // No file 'image' has been sent at all:
        $response = array(
            "status" => "error",
            "message" => "No file was sent!"
        );
    } 
    echo json_encode($response);

} else{
   // (Not a POST request)
   echo "<h1>SORRY,</h1>";
   echo "<h2>No permission to enter this page :( </h2>";
}

function createName($name){
    // Create random name:
    $random_number = intval( "0" . rand(1,9) . rand(0,9) . rand(0,9) ); 
    $random_string = chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90)); 
    $new_name = "---" . $random_number . $random_string . "-" . $name;
    
    return $new_name;
}

function getCurrentUrl(){
    // Get the current path:
    $server = sprintf(
      "%s://%s",
      isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
      $_SERVER['SERVER_NAME']
    );
    $api_app = dirname($_SERVER['REQUEST_URI'], 3);

    return $server . $api_app;
}

