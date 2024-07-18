<?php
include('functions.php');

$vehicle_id = escapeString($_POST['vehicle_id']);

$query = "DELETE FROM vehicles WHERE vehicle_id = $vehicle_id";
$delete_asset = mysqli_query($connection,$query);
checkQuery($delete_asset);

?>