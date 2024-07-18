<?php
include('functions.php');

$usr_id = escapeString($_SESSION['usr_id']);

$query = "SELECT * FROM active_subscription WHERE usr_id = '$usr_id'";
$select_user_subs = mysqli_query($connection,$query);
checkQuery($select_user_subs);

if(mysqli_num_rows($select_user_subs) !== 0){
//subscription for current user exist

while($row = mysqli_fetch_assoc($select_user_subs)){
$sub_id = $row['sub_id'];
$package_id  = $row['package_id'];
$date_created = $row['date_created'];
$due_date = $row['due_date'];
$time_status = $row['time_status'];


$query = "SELECT * FROM packages WHERE package_id = $package_id";
$select_packages = mysqli_query($connection,$query);
checkQuery($select_packages);
while($row = mysqli_fetch_assoc($select_packages)){
$package_id = $row['package_id'];
$package_title = $row['package_title'];
$package_fee = $row['package_fee'];
$package_interval = $row['package_interval'];
$cover_image = $row['cover_image'];

}

?>
<div class="subscription-card card" >
<img src="assets/images/<?php echo $cover_image; ?>" class="card-img-top" height="100px" alt="Cover image">
<div class="card-body">

<div class="card-body-details">
        <h6 class="card-title text-truncate" style="width:80px;"><?php echo $package_title ?></h6>
    <p class="card-subtitle mb-2 text-muted">Ksh <?Php echo $package_fee ?>/=</p>
   
    <a data-bs-toggle="modal" data-bs-target="#infoSubModal" sub-id="<?php echo $sub_id; ?>" class="info-sub-btn card-link">Info</a> 
</div>

<?php 
// echo $time_status;
//time elapsed
if($time_status == 'elapsed'){

//true
?>
<div class="progress-circle over50 p100">
    <span  style="color:red;">Expired</span>
    <div class="left-half-clipper">
        <div class="first50-bar" style="background-color:red;"></div>
        <div class="value-bar" style="border-color:red;"></div>
    </div>
</div>
<?php
}else{
//false
?>
<div class="progress-circle 
<?php
if(calculatePeriodPercentage($date_created, $due_date) > 50){
  echo 'over50 ';  
}
  echo 'p'.calculatePeriodPercentage($date_created, $due_date)
?>
">
    <span 
    style="<?php
    if(calculatePeriodPercentage($date_created, $due_date) > 70){
        echo "color:red;";
        } 
    ?>">
        <?php echo calculatePeriodPercentage($date_created, $due_date)?>%
    </span>
    <div class="left-half-clipper">
        <div class="first50-bar" 
        style="<?php 
        if(calculatePeriodPercentage($date_created, $due_date) > 70){
        echo "background-color:red;";
        } 
        ?>" ></div>
        <div class="value-bar" style="<?php 
        if(calculatePeriodPercentage($date_created, $due_date) > 70){
        echo "border-color:red;";
        } 
        ?>"></div>
    </div>
</div>
<?Php
}

?>





    <!-- <a href="#" class="card-link">Another link</a> -->
</div>
</div>
<?php

}

}else{
    echo "No subscriptions";
}

?>
<script>
    $(".info-sub-btn").click(function(e){
    let sub_id =  $(this).attr('sub-id');

    $.post("async/view_sub_info_modal.php",{sub_id:sub_id},function(data){
        $(".sub_info_data").html(data);
    })
    })
</script>
