<?Php
include('functions.php');

$usr_id = escapeString($_SESSION['usr_id']);
$balance = escapeString($_POST['amount']);
$order_id = escapeString($_POST['order_id']);
$payer_name = escapeString($_POST['payer_name']);

//adding the amount
$query = "UPDATE smart_park_users SET balance = balance + $balance WHERE usr_id = '$usr_id'";
$update_balance = mysqli_query($connection,$query);
checkQuery($update_balance);

if($update_balance){
?>
<div class="modal-header">
   
   <h1 class="modal-title fs-5" id="exampleModalLabel"> <b>Payment confirmation</b></h1>
   <button type="button" class="btn-close"  data-bs-dismiss="modal" aria-label="Close"></button>
 </div>
 <div class="modal-body">

 <div class="payment_success_msg">
    <img src="assets/images/green_tick.png" alt="Green button">
    <p class="mt-3">This is a confirmation that you have deposit amount <b><?php echo $balance; ?></b> reference no. <b><?php echo $order_id; ?> </b>credited by <b><?Php echo $payer_name; ?></b>.</p>
</div>


 </div>

 <div class="modal-footer">

   <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
   <button type="button" class="btn btn-primary">Save changes</button> -->
 </div>

<?php
}
?>