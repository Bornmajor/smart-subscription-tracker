<?php
include('functions.php');

$usr_id = $_SESSION['usr_id'];
$order_id = $_POST['order_id'];
$amount = escapeString($_POST['amount']);
$payment_type = escapeString($_POST['payment_type']);
$payment_method = escapeString($_POST['payment_method']);
$payer_name = escapeString($_POST['payer_name']);

$query = "INSERT INTO payment_history(order_id,usr_id,amount,payment_type,payment_method,payer_name)VALUES('$order_id','$usr_id','$amount','$payment_type','$payment_method','$payer_name')";
$create_payment_history = mysqli_query($connection,$query);
checkQuery($create_payment_history);
if(!$create_payment_history){
    failMsg('Payment history failed');
}
