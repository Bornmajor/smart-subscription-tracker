<?php
include('functions.php');

$plate_one = escapeString($_POST['plate_one']);
$plate_two = escapeString($_POST['plate_two']);

$plate_no = $plate_one.$plate_two;
$plate_no = strtoupper($plate_no);
//GET USER ID
$usr_id = escapeString($_SESSION['usr_id']);
$todayDate = (new DateTime())->format('Y-m-d H:i:s');

$query = "INSERT INTO vehicles(plate_no,date_registered,usr_id)VALUES('$plate_no','$todayDate','$usr_id')";
$add_vehicle = mysqli_query($connection,$query);
checkQuery($add_vehicle);

if($add_vehicle){
    successMsg("Asset saved");
}


?>