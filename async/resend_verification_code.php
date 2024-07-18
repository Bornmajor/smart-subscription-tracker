<?php
include('functions.php');

use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;


if(!isset($_POST['mail'])){
return false;
}

$mail = escapeString($_POST['mail']);

$code_token = generateVerificationCode(6);
$message = "Here is your new verification code :".$code_token." the code will be available for the next 15 minutes";

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
    ->setSubject('New account creation')
    ->setHtml($html_content)
    ->setText('This is the text content')
    ->setReplyTo('MS_zr64aS@trial-0r83ql3ojxplzw1j.mlsender.net')
    ->setReplyToName('reply to name');

    

$mailersend->email->send($emailParams);

$query = "UPDATE smart_park_users SET code_token = '$code_token' WHERE mail = '$mail'";
$update_token = mysqli_query($connection,$query);

successMsg('Verification code sent it will be active for the next 15 minutes');
?>