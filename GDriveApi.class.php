<?php 

class GoogleDriveApi { 
    const OAUTH_TOKEN = 'https://oauth2.googleapis.com/token'; 
    const GDRIVE_UPLOAD_URI = 'https://www.googleapis.com/upload/drive/v3/files'; 
    const GDRIVE_FILE_META = 'https://www.googleapis.com/drive/v3/files/'; 
     
    function __construct($params = array()) { 
        if (count($params) > 0){ 
            $this->initialize($params);         
        } 
    } 
     
    function initialize($params = array()) { 
        if (count($params) > 0){ 
            foreach ($params as $key => $val){ 
                if (isset($this->$key)){ 
                    $this->$key = $val; 
                } 
            }         
        } 
    } 
     
    public function GetAccessToken($client_id, $redirect_uri, $client_secret, $code) { 
        $curlPost = 'client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&client_secret=' . $client_secret . '&code='. $code . '&grant_type=authorization_code'; 
        $ch = curl_init();         
        curl_setopt($ch, CURLOPT_URL, self::OAUTH_TOKEN);         
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);         
        curl_setopt($ch, CURLOPT_POST, 1);         
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);     
        $data = json_decode(curl_exec($ch), true); 
        $http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE); 
         
        if ($http_code != 200) { 
            $error_msg = 'Failed to receieve access token'; 
            if (curl_errno($ch)) { 
                $error_msg = curl_error($ch); 
            } 
            throw new Exception('Error '.$http_code.': '.$error_msg); 
        } 
             
        return $data; 
    } 
     
    public function UploadFileToDrive($access_token, $file_content, $mime_type) { 
        $apiURL = self::GDRIVE_UPLOAD_URI . '?uploadType=media'; 
         
        $ch = curl_init();         
        curl_setopt($ch, CURLOPT_URL, $apiURL);         
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);         
        curl_setopt($ch, CURLOPT_POST, 1);         
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: '.$mime_type, 'Authorization: Bearer '. $access_token)); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $file_content); 
        $data = json_decode(curl_exec($ch), true); 
        $http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);         
         
        if ($http_code != 200) { 
            $error_msg = 'Failed to upload file to Google Drive'; 
            if (curl_errno($ch)) { 
                $error_msg = curl_error($ch); 
            } 
            throw new Exception('Error '.$http_code.': '.$error_msg); 
        } 
 
        return $data['id']; 
    } 
    
        public function listFiles($access_token) { 
        $apiURL = self::GDRIVE_UPLOAD_URI; 
         
        $lst = curl_init();         
        curl_setopt($lst, CURLOPT_URL, $apiURL);         
        curl_setopt($lst, CURLOPT_RETURNTRANSFER, 1);         
        curl_setopt($lst, CURLOPT_POST, 1);         
        curl_setopt($lst, CURLOPT_SSL_VERIFYPEER, FALSE); 
        $data = json_decode(curl_exec($lst), true); 
        $http_code = curl_getinfo($lst,CURLINFO_HTTP_CODE);         
         
        if ($http_code != 200) { 
            $error_msg = 'Failed to upload file to Google Drive'; 
            if (curl_errno($lst)) { 
                $error_msg = curl_error($lst); 
            } 
            throw new Exception('Error '.$http_code.': '.$error_msg); 
        } 
 
        return $data; 
    } 

     
    public function UpdateFileMeta($access_token, $file_id, $file_meatadata) { 
        $googleURL = self::GDRIVE_FILE_META . $file_id; 
         
        $ch = curl_init();         
        curl_setopt($ch, CURLOPT_URL, $googleURL);         
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);         
        curl_setopt($ch, CURLOPT_POST, 1);         
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer '. $access_token)); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH'); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($file_meatadata)); 
        $data = json_decode(curl_exec($ch), true); 
        $http_return_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);         
         
        if ($http_return_code != 200) { 
            $error_msg = 'Failed to update file metadata'; 
            if (curl_errno($ch)) { 
                $error_msg = curl_error($ch); 
            } 
            throw new Exception('Error '.$http_return_code.': '.$error_msg); 
        } 
 
        return $data; 
    } 
} 
?>