<?php

ini_set('display_errors', true); 

$dir = "images/";
$target_dir = "../../" . $dir;

function deleteImg($name, $target_dir){
  if ($name == "") {
    return "Image name is empty, no need to delete from folder.";
  }
  // In DB the name of image saved includs the url path, cut it:
  $shorten_name = substr($name, strpos($name, "---") );
  // Get the correct path for the image in the folder:
  $file_name = $target_dir . $shorten_name;
  // Delete:
  if (!file_exists($file_name)) {
    return "No such an image here! . image name: " . $shorten_name;
  } else {
      if(unlink($file_name)){
        return "Image delited. path was: " . $file_name;
      } else {
        return "Problem deliting image";
      }
  }
}

