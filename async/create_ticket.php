<?php
include('functions.php');

$message = escapeString($_POST['message']);
$subject = escapeString($_POST['subject']);
$ticket_id = generateOrderId();

$todayDate = (new DateTime())->format('Y-m-d H:i:s');
//GET USER DATA
$usr_id = escapeString($_SESSION['usr_id']);
$query = "SELECT * FROM smart_park_users WHERE usr_id = '$usr_id'";
$select_user_data = mysqli_query($connection,$query);
checkQuery($select_user_data);
while($row = mysqli_fetch_assoc($select_user_data)){
$mail =  $row['mail'];
}

//send email about receiving the issue


//record ticket
$query = "INSERT INTO smart_park_tickets(ticket_id,message,usr_id,ticket_status,ticket_subject,date_created)VALUES('$ticket_id','$message','$usr_id','received','$subject','$todayDate')";
$create_ticket = mysqli_query($connection,$query);
checkQuery($create_ticket);
if($create_ticket){
    successMsg("Issue reported");
}else{
    failMsg('Something went wrong');
}

?>