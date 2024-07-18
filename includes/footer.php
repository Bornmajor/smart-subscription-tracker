 <!-- Scroll to Top Button-->
 <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a> 
    
 <script>
    $(document).ready(function(){

        $(".registration_form").submit(function(e){
            e.preventDefault();

            let postData = $(this).serialize();
            $('.submit_btn').html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            $('.submit_btn').attr("disabled",true);

            $.post("async/create_user.php",postData,function(data){
                $(".auth-feedback").html(data);
                $('.submit_btn').html('Create account');
                $('.submit_btn').attr("disabled",false);
            })

        });

        $(".login_form").submit(function(e){
            e.preventDefault();

            let postData = $(this).serialize();
            $('.submit_btn').html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            $('.submit_btn').attr("disabled",true);

            $.post("async/login_user.php",postData,function(data){
                $(".auth-feedback").html(data);
                $('.submit_btn').html('Login');
                $('.submit_btn').attr("disabled",false);
            })

        });

        //display entry of adding registration number

        $(".subscribe-service").click(function(){
         let package_id =   $(this).attr('package-id');

         $.post("async/view_subscription_modal.php",{package_id:package_id},function(data){
            $(".subscription_modal").html(data)
         })
        });

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
    loadActiveSubscriptions();


        $(".add_username_form").submit(function(e){
            e.preventDefault();

            let postData = $(this).serialize();

            $.post("async/add_official_names.php",postData,function(data){
               $(".account_completion_feeds").html(data);
            }) 

        })

       
        $('.skip-account-completion').click(function() {
                window.location.href = '?page=dashboard';

                
        });

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
        loadUserBalance();

        $(".submit_deposit_amount_form").submit(function(e){
            e.preventDefault();

            let postData = $(this).serialize();

            $.post("async/deposit_modal_btn.php",postData,function(data){
                $(".form_deposit_feeds").html(data);
            })
        })

        $(".plate_entry_one").keyup(function(){
            let inputValue = $(this).val();
        let isFormatABC = checkFormatABC(inputValue);
        let isFormatNNNL = checkFormatNNNL(inputValue);
        
        if (isFormatABC) {
            // $(".plate_validation").text("The input is valid (Format: ABC).").css("color", "green");
            $(".plate_validation").slideUp();
            $('.save-vehicle-asset').attr("disabled",false);

        }else {
            $(".plate_validation").slideDown();
            $(".plate_validation").text("The input is invalid. Please follow the format LLL NNNL.").css("color", "red");
            $('.save-vehicle-asset').attr("disabled",true);
        }
      });

      
      $(".plate_entry_two").keyup(function(){
            let inputValue = $(this).val();
        let isFormatABC = checkFormatABC(inputValue);
        let isFormatNNNL = checkFormatNNNL(inputValue);
        
        if (isFormatNNNL) {
            // $(".plate_validation").text("The input is valid (Format: ABC).").css("color", "green");
            $(".plate_validation").slideUp();
            $('.save-vehicle-asset').attr("disabled",false);

        } else {
            $(".plate_validation").slideDown();
            $(".plate_validation").text("The input is invalid. Please follow the format LLL NNNL.").css("color", "red");
            $('.save-vehicle-asset').attr("disabled",true);
        }
      });
    
      function checkFormatABC(value) {
        // Regular expression to match the format ABC (three letters)
        let regex = /^[A-Za-z]{3}$/;
        return regex.test(value);
    }

    function checkFormatNNNL(value) {
        // Regular expression to match the format NNNL (three numbers followed by one letter)
        let regex = /^[0-9]{3}[A-Za-z]$/;
        return regex.test(value);
    }

        $(".save_vehicle_form").submit(function(e){
            e.preventDefault();

            let postData = $(this).serialize();

            $.post("async/save_vehicle.php",postData,function(data){
                $(".plate_no_feeds").html(data);
                loadSavedVehicles();

            })
            //reset form fields
            $(".save_vehicle_form")[0].reset();

        })
        function loadSavedVehicles(){
            $.ajax({
                url: "async/view_saved_vehicles.php",
                type: "POST",
                success:function(data){
                    if(!data.error){
                    $('.view_saved_vehicles').html(data);
                    }

                }
                })

        }

        loadSavedVehicles();

        $(".report_issue_form").submit(function(e){
            e.preventDefault();
            let postData = $(this).serialize();

            $.post("async/create_ticket.php",postData,function(data){
                $(".report_issue_feeds").html(data);
                loadUserTickets();
            })

              //reset form fields
              $(".report_issue_form")[0].reset();
        });

        function loadUserTickets(){
            $.ajax({
                url: "async/view_user_ticket.php",
                type: "POST",
                success:function(data){
                    if(!data.error){
                    $('.load_tickets').html(data);
                    }

                }
                })

        }
    
        loadUserTickets();


$(".inline-eye").click(function(e){
    const passwordField = $("#pwd");
        const passwordFieldType = passwordField.attr('type');
        const icon = $(this);

    

        if (passwordFieldType === 'password') {
            passwordField.attr('type', 'text');
            $(".slash-invisible").fadeOut();
            $(".slash-visible").fadeIn();

          
        } else {
            passwordField.attr('type', 'password');
            $(".slash-invisible").fadeIn();
            $(".slash-visible").fadeOut();
           
        }   
})

        $(".forget_pwd_form").submit(function(e){
        
            e.preventDefault();
            let postData = $(this).serialize();

            $.post("async/send_pwd_reset_link.php",postData,function(data){
                $(".forgot_pwd_form_feeds").html(data);
            });
              //reset form fields
              $(".forget_pwd_form")[0].reset();

        });

        $(".reset_pwd_form").submit(function(e){
            e.preventDefault();

            let postData = $(this).serialize();

            $.post("async/process_pasword_reset.php",postData,function(data){
             $(".reset_pwd_feeds").html(data);
            })

             //reset form fields
             $(".reset_pwd_form")[0].reset();
        })


    })
 </script>

<script src="assets/vendor/jquery/jquery.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>  
<script src="assets/js/sb-admin-2.min.js"></script>  

<script src="assets/js/all.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/myscript.js"></script>

</body>
</html>