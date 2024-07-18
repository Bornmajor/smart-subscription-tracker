<?php
include('functions.php');

use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;

$mail = escapeString($_POST['mail']);
$reset_token = bin2hex(random_bytes(32));

//check if mail is valid
if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
//not valid
failMsg('Email provided is invalid');
return false;
} 

$query = "SELECT * FROM smart_park_users WHERE mail = '$mail'";
$select_mail = mysqli_query($connection,$query);
checkQuery($select_mail);
//check if email exist in database
if(mysqli_num_rows($select_mail) == 0){
//if it does not exist
successMsg("We'll email you a reset link to recover your account");
return false;
}
//update reset_token
$query = "UPDATE smart_park_users SET reset_token = '$reset_token' WHERE mail = '$mail'";
$update_reset_token = mysqli_query($connection,$query);
checkQuery($update_reset_token);
if($update_reset_token){
successMsg("We'll email you a reset link to recover your account");
}

$message = "Here is a link to recover your account http://localhost/apps/smart-parking/?page=reset_password&reset_token=".$reset_token."&email=".$mail." this link will be available for the next 15 minutes";

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
    ->setSubject('Reset link')
    ->setHtml($html_content)
    ->setText('This is the text content')
    ->setReplyTo('MS_zr64aS@trial-0r83ql3ojxplzw1j.mlsender.net')
    ->setReplyToName('reply to name');

    

$mailersend->email->send($emailParams);


?>