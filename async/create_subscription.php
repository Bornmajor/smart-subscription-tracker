<?php
include('functions.php');

$order_id = escapeString($_POST['order_id']);
$package_id = escapeString($_POST['package_id']);
$amount = escapeString($_POST['amount']);
$payer_name = escapeString($_POST['payer_name']);
$plate_no = escapeString($_POST['plate_no']);
$auto_renew = escapeString($_POST['auto_renew']);


$mail = $_SESSION['mail'];
$sub_id = generateSubId($mail);
$usr_id = $_SESSION['usr_id'];


//fetch other params like interval
$query = "SELECT * FROM packages WHERE package_id = $package_id";
$select_packages = mysqli_query($connection,$query);
checkQuery($select_packages);
while($row = mysqli_fetch_assoc($select_packages)){
$package_id = $row['package_id'];
$package_title = $row['package_title'];
$package_fee = $row['package_fee'];
$package_interval = $row['package_interval'];
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

$query = "INSERT INTO active_subscription(sub_id,package_id,usr_id,date_created,due_date,order_id,plate_no,time_status,auto_renew,amount)VALUES('$sub_id',$package_id,'$usr_id','$startDate','$due_date','$order_id','$plate_no','active','$auto_renew','$package_fee')";
$create_subscription = mysqli_query($connection,$query);
checkQuery($create_subscription);

if($create_subscription){
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