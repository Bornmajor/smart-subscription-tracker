<?php
include('functions.php');

$usr_id = escapeString($_SESSION['usr_id']);
$query = "SELECT balance FROM smart_park_users WHERE usr_id = '$usr_id'";
$select_balance = mysqli_query($connection,$query);
while($row = mysqli_fetch_assoc($select_balance)){
$balance = $row['balance'];

}

if(empty($balance)){
    $balance = '0';
}

echo $balance;