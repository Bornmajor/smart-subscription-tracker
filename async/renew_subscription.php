<?Php
include('functions.php');

$sub_id = escapeString($_POST['sub_id']);
$order_id = escapeString($_POST['order_id']);
$payer_name = $_POST['payer_name'];
$usr_id = escapeString($_SESSION['usr_id']);

$query = "SELECT * FROM active_subscription WHERE sub_id = '$sub_id'";
$select_renew_sub = mysqli_query($connection,$query);
checkQuery($select_renew_sub);

while($row = mysqli_fetch_assoc($select_renew_sub)){
$package_id =  $row['package_id'];
$date_created = $row['date_created'];
$due_date = $row['due_date'];
}

$query = "SELECT * FROM packages WHERE package_id = $package_id";
$select_packages = mysqli_query($connection,$query);
checkQuery($select_packages);
while($row = mysqli_fetch_assoc($select_packages)){
$package_title = $row['package_title'];
$package_interval = $row['package_interval'];
$package_fee = $row['package_fee'];

}

// Use the current date formatted as a string
$startDate = (new DateTime())->format('Y-m-d H:i:s');
$due_date = calculateDueDate($startDate,$package_interval);
$due_date = $due_date->format('Y-m-d H:i:s');

if(isset($_POST['redeem_balance'])){
   //deduct user amount from balance
   $usr_balance = getUserBalance($usr_id);
   $remained_balance = $usr_balance - $package_fee;

     //update user balance
     $query = "UPDATE smart_park_users SET balance = '$remained_balance' WHERE usr_id = '$usr_id'";
     $update_user_balance = mysqli_query($connection,$query);
     checkQuery($update_user_balance);
      
}

$query = "UPDATE active_subscription SET date_created = '$startDate', due_date = '$due_date', time_status = 'active',order_id = '$order_id' WHERE sub_id = '$sub_id'";
$update_subscription = mysqli_query($connection,$query);
if($update_subscription){
?>
<div class="modal-header">
   
   <h1 class="modal-title fs-5" id="exampleModalLabel"> <b>Payment confirmation</b></h1>
   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
 </div>
 <div class="modal-body">

 <div class="payment_success_msg">
    <img src="assets/images/green_tick.png" alt="Green button">
    <p class="mt-3">This is a confirmation that your payment for <b><?php echo $package_title; ?></b> reference no. <b><?php echo $order_id; ?> </b>credited by <b><?Php echo $payer_name; ?></b>  has been received.</p>
</div>


 </div>

 <div class="modal-footer">


 </div>

<?php
}