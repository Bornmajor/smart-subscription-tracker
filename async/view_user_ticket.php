<?php
include('functions.php');

$usr_id = $_SESSION['usr_id'];

$query = "SELECT * FROM smart_park_tickets WHERE usr_id = '$usr_id'";
$select_tickets = mysqli_query($connection,$query);
checkQuery($select_tickets);
if(mysqli_num_rows($select_tickets) !== 0 ){
while($row = mysqli_fetch_assoc($select_tickets)){
$ticket_id = $row['ticket_id'];
$ticket_subject = $row['ticket_subject'];
$date_created = $row['date_created'];
$ticket_status = $row['ticket_status'];
$message = $row['message'];

//loop through tickets
?>
<div class="ticket-card card" style="">
<div class="card-body">
<h5 class="card-title text-truncate mb-4" style="width:20rem;"><?php echo $ticket_subject.": ".$message ?></h5>

<div class="ticket-dropdown">
<div class="dropdown">

<span class="" type="button" data-bs-toggle="dropdown" aria-expanded="false">
<i class="fas fa-ellipsis-v"></i>
</span>
<ul class="dropdown-menu">
<li><a class="dropdown-item delete-comment" ticket-id="<?php echo $ticket_id; ?>">Delete</a></li>

</ul>
</div>

</div>

<div class="d-flex justify-content-between align-items-end" style="font-size;:14px;">
<?php
if($ticket_status == 'received'){
?>
    <p class="card-text">Status: <span class="text-warning"><?php echo ucfirst($ticket_status); ?></span></p>
<?php
}else if($ticket_status == 'resolving'){
?>  
    <p class="card-text">Status: <span class="text-warning"><?php echo ucfirst($ticket_status); ?></span></p>
<?php
}else{
?>
<p class="card-text">Status: <span class="text-success"><?php echo ucfirst($ticket_status); ?></span></p> 
<?Php
    
}
?>

<p class="text-mute"><?php echo formatDateWithOrdinalSuffix($date_created);?></p>   
</div>


</div>
</div>
<?php

}

}else{
?>
<div class="no_items">
   <img src="assets/images/support.png" alt="No assets" >
  <h5 class="my-2">No tickets</h5>
</div>
<?php
}
?>

