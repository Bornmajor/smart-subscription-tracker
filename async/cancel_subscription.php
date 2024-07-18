<?Php
include('functions.php');

$sub_id = escapeString($_POST['sub_id']);

$query = "DELETE FROM active_subscription WHERE sub_id = '$sub_id'";
$delete_subscription = mysqli_query($connection,$query);
checkQuery($delete_subscription);
?>