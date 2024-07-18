<?php
include('functions.php');

$mail = escapeString($_POST['mail']);
$pwd = escapeString($_POST['pwd']);

//check if user has old password


//encrypt pwd
$pwd = password_hash($pwd,PASSWORD_BCRYPT,array('cost' => 12));

//set new token
$reset_token = bin2hex(random_bytes(32));

$query = "UPDATE smart_park_users SET pwd = '$pwd', reset_token = '$reset_token' WHERE mail = '$mail'";
$update_password = mysqli_query($connection,$query);
checkQuery($update_password);
if($update_password){
successMsg('Password reset successfully');
?>
<script type='text/javascript'>
window.setTimeout(function() {
window.location = '?page=login';
}, 3000);
</script>
<?php
}else{
    failMsg('Password reset failed');
}
?>