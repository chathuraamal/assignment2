<?php 
// Include the configuration file to index.php
include_once 'config.php'; 
 
$status = $status_msg = ''; 

$fileUrl = NULL;
if(!empty($_SESSION['status_response'])){ 
    $status_response = $_SESSION['status_response']; 
    $status = $status_response['status']; 
    $status_msg = $status_response['status_msg']; 
     
    unset($_SESSION['status_response']); 
    
} 

if(isset( $_SESSION['pathmy'] )){
        $fileUrl =  $_SESSION['pathmy'] ;
         unset($_SESSION['pathmy']);
}else{
    $fileUrl = NULL;
    
}


?>

<!-- Status message -->
<?php 
if($fileUrl == NULL){
    echo "Please select a file to upload"; 
}
?>


<div class="col-md-12">
    <form method="post" action="upload.php" class="form" enctype="multipart/form-data">
        <div class="form-group">
            <label>File</label>
            <input type="file" name="file" class="form-control">
        </div>
        <div class="form-group">
            <input type="submit" class="form-control btn-primary" name="submit" value="Upload"/>
        </div>
        <?php if( $fileUrl ) { ?>
         <div class="form-group">
             
             <br>  
             
           Uploaded File =  <?php  echo $fileUrl    ?>
        </div>
        <?php } ?>
    </form>
</div>

