<?php 
// Including Google drive api handling classes  
include_once 'GDriveApi.class.php'; 
     
//Including database conneciton configuration
require_once 'dbConfig.php'; 
 
$statusMsg = ''; 
$status = 'danger'; 
//initialize calling google API
if(isset($_GET['code'])){ 
     $GoogleDriveApi = new GoogleDriveApi(); 
     
    // check to see if there is previous file upload 
    // retrive reference ID of file from global session variable
    $file_id = $_SESSION['last_file_id']; 
    
    //proceed if empty
    if(!empty($file_id)){ 
         
        // Fetch file details from the database 
        $sqlquery = "SELECT * FROM drive_files WHERE id = ?"; 
        $statement = $dbconn->prepare($sqlquery);  
        $statement->bind_param("i", $db_file_id); 
        $db_file_id = $file_id; 
        $statement->execute(); 
        $result = $statement->get_result(); 
        $fileData = $result->fetch_assoc(); 
         
        if(!empty($fileData)){ 
            $file_name = $fileData['file_name']; 
            $target_file = 'uploads/'.$file_name; 
            $file_content = file_get_contents($target_file); 
            $mime_type = mime_content_type($target_file); 
             
            // retrive the access token from google 
            if(!empty($_SESSION['google_access_token'])){ 
                $access_token = $_SESSION['google_access_token']; 
            }else{ 
                $data = $GoogleDriveApi->GetAccessToken(GOOGLE_CLIENT_ID, REDIRECT_URI, GOOGLE_CLIENT_SECRET, $_GET['code']); 
                $access_token = $data['access_token']; 
                $_SESSION['google_access_token'] = $access_token; 
            } 
            
            //upload if the access token retrieval was successful
            if(!empty($access_token)){ 
                 
                try { 
                    // Upload file to Google drive 
                    $drive_file_id = $GoogleDriveApi->UploadFileToDrive($access_token, $file_content, $mime_type); 
                    
                    
                    
                    if($drive_file_id){ 
                        $file_meta = array( 
                            'name' => basename($file_name) 
                        ); 
                        
                        
                      
                         
                        // Updating the metadata of the file in Google drive 
                        $gdrive_metadata = $GoogleDriveApi->UpdateFileMeta($access_token, $drive_file_id, $file_meta); 
                         
                        if($gdrive_metadata){ 
                            // Update google drive file reference in the database 
                            $sqlquery = "UPDATE drive_files SET google_drive_file_id=? WHERE id=?"; 
                            $statement = $dbconn->prepare($sqlquery); 
                            $statement->bind_param("si", $db_gdrive_file_id, $db_file_id); 
                            $db_gdrive_file_id = $drive_file_id; 
                            $db_file_id = $file_id; 
                            $update = $statement->execute(); 
                             
                            unset($_SESSION['last_file_id']); 
                            unset($_SESSION['google_access_token']); 
                            
                           
                             
                            $status = 'success'; 
                           
                            $statusMsg .= '<p><a href="https://drive.google.com/open?id='.$gdrive_metadata['id'].'" target="_blank">'.$gdrive_metadata['name'].'</a>'; 
                            
                             $_SESSION['pathmy'] = $statusMsg;
                            
                            
                        } 
                    } 
                } catch(Exception $e) { 
                    $statusMsg = $e->getMessage(); 
                } 
            }else{ 
                $statusMsg = 'Failed to fetch access token!'; 
            } 
        }else{ 
            $statusMsg = 'File data not found!'; 
        } 
    }else{ 
        $statusMsg = 'File reference not found!'; 
    } 
     
    $_SESSION['status_response'] = array('status' => $status, 'status_msg' => $statusMsg); 
     
    header("Location: index.php"); 
    exit(); 
} 
?>