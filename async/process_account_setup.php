<?php
include('functions.php');

$national_id = escapeString($_POST['national_id']);
$region = escapeString($_POST['region']);
$official_names = escapeString($_POST['official_names']);

$usr_id = escapeString($_SESSION['usr_id']);

$query  = "UPDATE smart_park_users SET official_names = '$official_names', national_id = '$national_id',region = '$region'";
$update_user_data = mysqli_query($connection,$query);
checkQuery($update_user_data);
if($update_user_data){

?>
<div class="payment_success_msg">
    <img src="assets/images/green_tick.png" alt="Green button">
    <p class="mt-3">Completed account setup</p>
</div>
<?php
//reload the page
echo "<script type='text/javascript'>
window.setTimeout(function() {
window.location = '?page=dashboard&registration_completion=true';
}, 3000);
</script>";
}