<?php
include('functions.php');

use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;

$mail = escapeString($_POST['mail']);
$pwd = escapeString($_POST['pwd']);

//check if user exist

$query = "SELECT * FROM smart_park_users WHERE mail = '$mail'";
$select_user_data = mysqli_query($connection,$query);
checkQuery($select_user_data);

if(mysqli_num_rows($select_user_data) !== 0){
    failMsg('Email unavailable try another one');
       //reload the page
       echo "<script type='text/javascript'>
       window.setTimeout(function() {
       window.location = '?page=registration';
       }, 2000);
       </script>";
    return false;
}

//proceed mail does not exist

//check if email is valid

if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    failMsg('Email address unavailable try another one');
    return false;
}

// password encryption
$pwd = password_hash($pwd,PASSWORD_BCRYPT,array('cost' => 12));

//generate user ID
$usr_id = generateUserId($mail);

$code_token = generateVerificationCode(6);
$message = " ".$code_token."";

//send an email verification via email
//send mail code using mailersend api

$html_content = createAccountTemplateBody($message,$mail);
$mailersend = new MailerSend(['api_key' => 'mlsn.da27b7ca1cb097d19bc943995dcc00a3c4c7f6d03f9ffc2b54f275006b23bcfd']);

$recipients = [
    //it sending to
    new Recipient($mail, 'Your Client'),
];

$emailParams = (new EmailParams())
    ->setFrom('MS_zr64aS@trial-0r83ql3ojxplzw1j.mlsender.net')
    ->setFromName('Smart parking E-services')
    ->setRecipients($recipients)
    ->setSubject('New account creation')
    ->setHtml($html_content)
    ->setText('This is the text content')
    ->setReplyTo('MS_zr64aS@trial-0r83ql3ojxplzw1j.mlsender.net')
    ->setReplyToName('reply to name');

    

$mailersend->email->send($emailParams);

$auth_type = 'registration';

//create user data
$query = "INSERT INTO smart_park_users(usr_id,mail,pwd,code_token)VALUES('$usr_id','$mail','$pwd','$code_token')";
$create_user = mysqli_query($connection,$query);
checkQuery($create_user);
if($create_user){

include('../components/verify_mail_form.php');   

// successMsg("Creating an account");

// successMsg("Redirecting..login...");

// //reload the page
// echo "<script type='text/javascript'>
// window.setTimeout(function() {
// window.location = '?page=dashboard&registration_complete=false';
// }, 3000);
// </script>";

}


