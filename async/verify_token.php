<?php
include('functions.php');

if(isset($_POST['code_token']) || isset( $_POST['mail'])){
    
  $code_token = escapeString($_POST['code_token']);
  $mail = escapeString($_POST['mail']);


   //fetch code token
   $query = "SELECT code_token FROM smart_park_users WHERE mail = '$mail'";
   $select_db_code = mysqli_query($connection,$query);
   checkQuery($select_db_code);
   while($row = mysqli_fetch_assoc($select_db_code)){
   $db_token = $row['code_token'];
   }

   if($select_db_code){

     if($db_token == $code_token){

    $auth_type = $_POST['auth_type'];
    if($auth_type == 'registration'){

     //assign session variable
    setSession($mail);

    successMsg("Creating an account");

    successMsg("Redirecting..login...");

    //reload the page
    echo "<script type='text/javascript'>
    window.setTimeout(function() {
    window.location = '?page=dashboard&registration_completion=false';
    }, 3000);
    </script>";

    
    
    }else{
        //auth is login
        //create session
setSession($mail);

successMsg("Verification successful");

successMsg("Redirecting...");

//code to check if user has completed setup account
$usr_id = escapeString($_SESSION['usr_id']);
$query = "SELECT * FROM smart_park_users WHERE usr_id = '$usr_id'";
$check_user_data = mysqli_query($connection,$query);
checkQuery($check_user_data);

while($row = mysqli_fetch_assoc($check_user_data )){
  $official_names = $row['official_names'];

}

if(empty($official_names)){
$registration_completion = 'false';
}else{
$registration_completion = 'true';   
}

//reload the page
echo "<script type='text/javascript'>
window.setTimeout(function() {
window.location = '?page=dashboard&registration_completion=".$registration_completion."';
}, 3000);
</script>";
    }
   

  }else{
    failMsg('Verification code incorrect');
  }
   }
 
 




  

}

?>