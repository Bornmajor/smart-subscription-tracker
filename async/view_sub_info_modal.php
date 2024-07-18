<?php
include('functions.php');
$sub_id = escapeString($_POST['sub_id']);

$query = "SELECT * FROM active_subscription WHERE sub_id = '$sub_id'";
$select_sub = mysqli_query($connection,$query);
checkQuery($select_sub);
while($row = mysqli_fetch_assoc($select_sub)){
$package_id =  $row['package_id'];
$date_created = $row['date_created'];
$due_date = $row['due_date'];
$plate_no = $row['plate_no'];
$time_status = $row['time_status'];
}

$query = "SELECT * FROM packages WHERE package_id = $package_id";
$select_packages = mysqli_query($connection,$query);
checkQuery($select_packages);
while($row = mysqli_fetch_assoc($select_packages)){
$package_id = $row['package_id'];
$package_title = $row['package_title'];
$package_fee = $row['package_fee'];
$package_interval = $row['package_interval'];
$placeholder_img = $row['placeholder_img'];

}

?>
  <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel"><b><?php echo $package_title ?></b></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
       
      <div class="d-flex justify-content-between align-items-center">

      <div class="package-car-details">
        <img src="assets/images/<?php echo $placeholder_img ?>" alt="Placeholder img" width="150px">   
        <span style="font-size:18px;"><?php echo $plate_no ?></span>
        </div>

        <div class="progress-area d-flex  flex-column justify-content-center align-items-center ">
         <h6>Usage</h6>

        <?Php
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

        </div>
       

      </div>

       

   <div class="package-details">
       <div> Valid till: <b><?php
         // Use the current date formatted as a string
        // $due_date;
      
         echo formatDateWithOrdinalSuffix($due_date);
         ?>,
         <?php echo formatTimeWithOrdinalSuffix($due_date); ?>
         </b>
        </div>
       <div>Paid: <b><?php echo $package_fee?>/=</b></div>
       <div class="mt-3" style="font-weight:600;">
        <?php
         if($time_status == 'elapsed'){
          //if elapsed
          ?>
         <p class="text-danger">
            Expired(Action required)
          </p> 
      
          <?php
         }else{
          ?>
           <p  class="text-success">
           Active
          </p>
          
          <?php

          }
          ?>
      
      </div>
    </div>
        
       
       

      </div>
      <div class="modal-footer">
        <!-- <a href="#" class="btn btn-primary"></a> -->
         <button type="button" sub-id="<?Php echo $sub_id  ?>" class="btn btn-secondary cancel-subscription-btn">Cancel subscription</button>
        <button class="btn btn-primary renew-modal-btn" sub-id="<?Php echo $sub_id  ?>" data-bs-target="#renewModal" data-bs-toggle="modal">Renew</button>
        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
        
      </div>

      <script>
          function loadUserBalance(){
            $.ajax({
                url: "async/user_balance.php",
                type: "POST",
                success:function(data){
                    if(!data.error){
                    $('.load_user_balance').html(data);
                    }

                }
                })

        }

        function loadActiveSubscriptions(){
        $.ajax({
        url: "async/users_subscriptions.php",
        type: "POST",
        success:function(data){
            if(!data.error){
            $('.view_active_subscription').html(data);
            }

        }
        })

        }

        $(".renew-modal-btn").click(function(){
          let sub_id = $(this).attr("sub-id");

          $.post("async/view_renew_modal.php",{sub_id:sub_id},function(data){
            $(".load-renew-modal").html(data);
          })
        });

        $(".cancel-subscription-btn").click(function(){
          let sub_id = $(this).attr("sub-id");
          if (confirm("Are you sure you want to cancel your subscription?")) {
              $.post("async/cancel_subscription.php",{sub_id:sub_id},function(data){
            loadActiveSubscriptions();
            loadUserBalance();
          })  
          }
      

        })
      </script>