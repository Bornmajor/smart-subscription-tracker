<?php
include("functions.php");

$amount  = escapeString($_POST['amount']);

if(!isValidNumber($amount)){
failMsg("Invalid amount entry");
return false;

}

//convert package fee to dollar for paypal
$converted_amount = convertKshToUsd($amount,0.0076);
?>
<div class="success_feedback">



  <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Deposit to your account</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

      <div class="feedback_error"></div>

      

        <div class="mb-3">
        <p>Amount depositing : <?php echo $amount ?></p>    
        </div>

<div id="paypal-button-container"></div>     
<button class="btn btn-primary w-100 mt-4" > <img src="assets/images/mpesa-mobile-logo.png" width="15px" alt="">  Lipa na MPESA</button>
   


 
      </div>

  

      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
        <!-- <button type="submit" class="btn btn-primary">Proceed</button> -->
      </div>

      </div>



<script>
  //load paypal embed btn 

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
  paypal.Buttons({
    createOrder: function(data, actions) {


        // Replace with your actual product details
        let cart = [
            {
                name: "Deposit", // Replace with actual product name
                sku: "1",
                quantity: 1, // Ensure quantity is a number
                amount:"<?php echo $converted_amount ; ?>" // Ensure amount is a number
            }
        ];


        return fetch("async/process_deposit.php", {
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
         
           let order_id = details.id;
           let amount = <?php echo $amount; ?>;
           let payer_name = `${details.payer.name.given_name} ${details.payer.name.surname}`;
           let payment_method = 'Paypal';
           let payment_type = "deposit";
      
          


            $.post("async/deposit_payment.php",{order_id:order_id,amount:amount,payer_name:payer_name,payment_method:payment_method},function(data){
                $(".success_feedback").html(data);
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