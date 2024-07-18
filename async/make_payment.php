<?Php
include('functions.php');
$package_id = escapeString($_POST['package_id']);
$plate_number = escapeString($_POST['plate_number']);

if(isset($_POST['auto_renew'])){
$auto_renew = escapeString($_POST['auto_renew']);
}else{
$auto_renew = 'no';
}


$query = "SELECT * FROM packages WHERE package_id = $package_id";
$select_packages = mysqli_query($connection,$query);
checkQuery($select_packages);
while($row = mysqli_fetch_assoc($select_packages)){
$package_id = $row['package_id'];
$package_title = $row['package_title'];
$package_fee = $row['package_fee'];
$package_interval = $row['package_interval'];
}

//convert package fee to dollar for paypal
$package_fee_usd = convertKshToUsd($package_fee,0.0076);
?>

<div class="feedback">


<div class="modal-header">
   
   <h1 class="modal-title fs-5" id="exampleModalLabel"> <b><?php echo $package_title; ?> package</b></h1>
   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
 </div>
 <div class="modal-body">

 <div class="feedback_error"></div>
  <!-- #display package details-->
   
  <div class="d-flex justify-content-between">
      <div class="package-car-details">
     <img src="assets/images/placeholder-car.png" alt="" width="150px">   
     <span style="font-size:18px;"><?php echo $plate_number ?></span>
    </div>
   
    <div class="package-details">
       <div> Valid till: <b><?php
         // Use the current date formatted as a string
         $todayDate = (new DateTime())->format('Y-m-d H:i:s');
         $due_date = calculateDueDate($todayDate,$package_interval);
         $string_due_date = $due_date->format('Y-m-d H:i:s');
         echo formatDateWithOrdinalSuffix($string_due_date);
         ?>
         </b>
        </div>
       <div>Amount to pay: <b><?php echo $package_fee?>/=</b></div>
       <div><button class="btn btn-primary mt-3" style="font-size:15px;"><?php echo ucfirst($package_interval); ?></button></div>
    </div>
  </div>
  
  <form action="" method="post" class="redeem_balance_form my-4">
    
 <input type="hidden" name="package_id" value="<?php echo $package_id; ?>" required>
  <input type="hidden" name="amount" value="<?php echo $package_fee; ?>" required>
  <input type="hidden" name="payer_name" value="<?php echo  getOfficialNamesById($_SESSION['usr_id']) ?>" required>
  <input type="hidden" name="plate_no" value="<?php echo $plate_number; ?>" required>
  <input type="hidden" name="order_id" value="<?php echo generateOrderId();?>" required>
  <input type="hidden" name="auto_renew" value="<?php echo $auto_renew ?>" required>
  <input type="hidden" name="redeem_balance" value="redeem_balance">

    <?php
    
    $usr_balance = getUserBalance($_SESSION['usr_id']);
    if($usr_balance > $package_fee){
    //if package fee > balance redeem balance unavailable
    ?>
   <button type="submit" class="btn btn-warning w-100 mt-3 py-2" >Redeem balance</button>
    <?php
    }else{
    echo '<p class="text-danger">Redeem balance option unavailable balance too low </p>';
    }
    ?>


  </form>

    <div id="paypal-button-container"></div>     
    <button class="btn btn-primary w-100 mt-4" > <img src="assets/images/mpesa-mobile-logo.png" width="15px" alt="">  Lipa na MPESA</button>
   

 </div>

 <div class="modal-footer">

   <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
   <button type="button" class="btn btn-primary">Save changes</button> -->
 </div>

    
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

    $(".redeem_balance_form").submit(function(e){
        e.preventDefault();

        if (confirm("Are you sure you want to redeem your balance?")) {
           let postData = $(this).serialize();

        $.post("async/create_subscription.php",postData,function(data){
                $(".feedback").html(data);
                loadActiveSubscriptions();
                loadUserBalance();
         })   
        }
      
    })
 </script>

 <script>

        paypal.Buttons({
    createOrder: function(data, actions) {
        // Replace with your actual product details
        let cart = [
            {
                name: "<?php echo $package_title ?>", // Replace with actual product name
                sku: "1",
                quantity: 1, // Ensure quantity is a number
                amount: <?php echo $package_fee_usd; ?> // Ensure amount is a number
            }
        ];

        return fetch("async/process_payment.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ cart })
        })
        .then((response) => response.json())
        .then((order) => order.id);
    },
    onApprove: function(data, actions) {
        // Capture the payment when the payer approves
        return actions.order.capture().then(function(details) {
            // Order captured successfully, handle completion
                 // Check if the transaction was successful
            if (details.status === "COMPLETED") {

            console.log("success");
            // Perform any actions like updating UI, showing success message, etc.

            //get data
           let package_id = "<?php echo $package_id; ?>";
           let order_id = details.id;
           let amount = details.purchase_units[0].amount.value;
           let payer_name = `${details.payer.name.given_name} ${details.payer.name.surname}`;
           let plate_no = "<?php echo $plate_number; ?>";
           let auto_renew = "<?php echo $auto_renew; ?>";
           let payment_method = "Paypal";
           let payment_type = "subscription";
          
          


            $.post("async/create_subscription.php",{order_id:order_id,package_id:package_id,amount:amount,payer_name:payer_name,plate_no:plate_no,auto_renew:auto_renew},function(data){
                $(".feedback").html(data);
                loadActiveSubscriptions();
                loadUserBalance();
            });

            $.post("async/record_payment_history.php",{order_id:order_id,amount:amount,payment_method:payment_method,payer_name:payer_name,payment_type:payment_type},function(data){
              $(".success_feedback").html(data);
            })

            //   let payer_email = details.payer.email_address;
            // let order_id = details.id;
            // let amount = details.purchase_units[0].amount.value;
            // let currency = details.purchase_units[0].amount.currency_code;
            // let payer_name = `${details.payer.name.given_name} ${details.payer.name.surname}`;
            // let status = details.status; 

            // $.post("async/create_usr_.php",{status:status,order_id:order_id,amount:amount,payer_email:payer_email,payer_name:payer_name,currency:currency},function(data){
            //     $(".feedback").html(data);
            // })

            } else {
                console.log("Transaction not completed. Status:", details.status);
                $(".feedback_error").html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>Transaction not completed</div>');
                // Handle non-completed transaction scenarios
            }
            console.log("Payment details:", details);

            // Perform any actions like updating UI, showing success message, etc.
        }).catch(function(error) {
            // Handle capture error
            console.error("Capture error:", error);
            $(".feedback_error").html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>Sorry something went wrong!!Try again</div>');
            // Perform actions like showing error message, retrying capture, etc.
        });
    },
    onCancel: function(data) {
        // Handle payment cancellation
        console.log('Order cancelled');
        $(".feedback_error").html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>Transaction canceled</div>');
        // You can show a cancel page, return to cart, or perform other actions here
    },
    style: {
        layout: 'vertical',
        color: 'blue',
        shape: 'rect',
        label: 'paypal'
    },
}).render('#paypal-button-container');
    </script>