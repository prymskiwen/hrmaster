<?php
define('KB', 1024);

define('MB', 1048576);

define('GB', 1073741824);

define('TB', 1099511627776);

$target_dir = "uploads/";

$target_file = $target_dir . basename($_FILES['file']['name']);

$uploadOk = 1;

$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

$fileName = basename($_FILES['file']['name'], '.'.$fileType) . "_" . time() . "." . $fileType;

$target_file = $target_dir . $fileName;

if(file_exists($target_file)){
    
    $uploadOk = 0;

    echo json_encode(array("status" => $uploadOk, "msg" => "Sorry, file already exists. Change name of file please."));
    exit;
}

if($_FILES['file']['size'] > 5*MB){

    $uploadOk = 0;

    echo json_encode(array("status" => $uploadOk, "msg" => "Sorry, your file size is larger than 5MB.. "));
    exit;

}

if($fileType != 'docx' && $fileType != 'doc' && $fileType != 'pdf' && $fileType != 'msg'){

    //echo "Sorry, only doc or pdf are allowed. Retry please.";

    $uploadOk = 0;

    echo json_encode(array("status" => $uploadOk, "msg" => "Sorry, only doc or pdf or outlook mail are allowed. Retry please."));
    exit;
}

if($uploadOk == 0){

    echo json_encode(array("status" => $uploadOk, "msg" => "Sorry, your file was not uploaded. Retry please."));
    exit;

}else{

    if(move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {

        $uploadOk = 1;

        echo json_encode(array("status" => $uploadOk, "msg" => "The file ". $fileName. " has been uploaded successfully.", "filename"=>$fileName));
        exit;

    }else{

        //echo "Sorry, there was an error uploading your file. Retry please.";

        $uploadOk = 1;

        echo json_encode(array("status" => $uploadOk, "msg" => "Sorry, there was an error uploading your file. Retry please."));
        exit;

    }
}
?>





