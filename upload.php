<?php     
// Include database configuration file 
require_once 'dbConfig.php'; 
 
$status_msg = $valueErr = ''; 
$status = 'danger'; 
 
// If the form is submitted 
if(isset($_POST['submit'])){ 
     
    // Form input field validation
    if(empty($_FILES["file"]["name"])){ 
        $valueErr .= 'Please select a file to upload to google drive.<br/>'; 
    } 
     
    // generate error if input is empty 
    if(empty($valueErr)){ 
        $trgt_dir = "uploads/"; 
        $file_name = basename($_FILES["file"]["name"]); 
        $targetFilePath = $trgt_dir . $file_name; 
         
        // storing file to be uploaded 
        if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)){ 
             
            // storing file ID and info to the database
            //for reference
            $sqlquery = "INSERT INTO drive_files (file_name,created) VALUES (?,NOW())"; 
            $statement = $dbconn->prepare($sqlquery); 
            $statement->bind_param("s", $db_file_name); 
            $db_file_name = $file_name; 
            $insert = $statement->execute(); 
             
            if($insert){ 
                $file_id = $statement->insert_id; 
                 
                // Store DB reference ID of file in SESSION 
                $_SESSION['last_file_id'] = $file_id; 
                 
                header("Location: $OauthURL"); 
                exit(); 
            }else{ 
                $status_msg = 'connecitivty was not initialized successfully.'; 
            } 
        }else{ 
            $status_msg = 'file upload failed. please contact system administrator'; 
        } 
    }else{ 
        $status_msg = '<p>A file must be selected to be uploaded:</p>'.trim($valueErr, '<br/>'); 
    } 
}else{ 
    $status_msg = 'upload failed due an error!'; 
} 
 
$_SESSION['status_response'] = array('status' => $status, 'status_msg' => $status_msg); 
 
header("Location: index.php"); 
exit(); 
?>