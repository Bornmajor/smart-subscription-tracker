<?php
include('functions.php');


use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;

$mail = escapeString($_POST['mail']);
$pwd = escapeString($_POST['pwd']);

//check if email exist 

$query = "SELECT * FROM smart_park_users WHERE mail = '$mail'";
$select_user_data = mysqli_query($connection,$query);
checkQuery($select_user_data);

if(mysqli_num_rows($select_user_data) == 0){
    //email does not exist
    failMsg('Email unavailable try another one');
    echo "<script type='text/javascript'>
    window.setTimeout(function() {
    window.location = '?page=login';
    }, 2000);
    </script>";
    return false;
}

while($row = mysqli_fetch_assoc($select_user_data)){
$db_pwd = $row['pwd'];
$usr_id = $row['usr_id'];
}

//check pwd is correct?
if(!password_verify($pwd,$db_pwd)){
    //if pwd is not correct
    failMsg('Check your credentials!!Try again');
    //reload the page
    echo "<script type='text/javascript'>
    window.setTimeout(function() {
    window.location = '?page=login';
    }, 2000);
    </script>";
    return false;
}

  //store login cookie
if(isset($_POST['store_user'])){

  createLoginCookies($mail);  
  }

//check if user has verified account
$query = "SELECT code_token FROM smart_park_users WHERE mail = '$mail'";
$check_code_token = mysqli_query($connection,$query);
checkQuery($check_code_token);
while($row = mysqli_fetch_assoc($check_code_token)){
$code_token = $row['code_token'];

}
if(empty($code_token)){



//send email
$code_token = generateVerificationCode(6);
$message = "Here is your verification code :".$code_token."";

$query = "UPDATE smart_park_users SET code_token = '$code_token' WHERE mail = '$mail'";
$update_token = mysqli_query($connection,$query);

//send an email verification via email
//send mail code using mailersend api

$html_content = templateBody($message,$mail);
$mailersend = new MailerSend(['api_key' => 'mlsn.da27b7ca1cb097d19bc943995dcc00a3c4c7f6d03f9ffc2b54f275006b23bcfd']);

$recipients = [
    //it sending to
    new Recipient($mail, 'Your Client'),
];

$emailParams = (new EmailParams())
    ->setFrom('MS_zr64aS@trial-0r83ql3ojxplzw1j.mlsender.net')
    ->setFromName('Smart parking E-services')
    ->setRecipients($recipients)
    ->setSubject('Verify your account')
    ->setHtml($html_content)
    ->setText('This is the text content')
    ->setReplyTo('MS_zr64aS@trial-0r83ql3ojxplzw1j.mlsender.net')
    ->setReplyToName('reply to name');

    

$mailersend->email->send($emailParams);


$auth_type = 'login'; 
//if code toke is unavailable then means user has never verify is account
include('../components/verify_mail_form.php');   

}else{
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



