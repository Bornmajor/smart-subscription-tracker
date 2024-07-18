<?Php $page = 'Saved vehicles'; ?>
<?php include('includes/header.php');?>

<?php include('components/dashboard_bar.php');?>


   <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800"></h1>
                     

                                
                    </div>

                    <!-- Content Row -->
                    <div class="container-video-flex">

                    <div class="dashboard_stats">
                      <?php autoRenewSubscriptions(); ?>

                        

                        <div class="view_saved_vehicles">

                    

                        </div>

                  
                        <!-- <button type="button" class="btn btn-primary mb-3" >
                        Launch demo modal
                        </button> -->



                    </div>

                  
                    <div class="load_users_video_assets">
                    </div>
                    




                    </div>
                    
              
                    <!-- Content Row -->


                   

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

<?php



?>
<!-- #region -->
<!-- Onboarding complete account setup modal -->
<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" style="display:none;" id="accountSetupButton" data-bs-toggle="modal" data-bs-target="#accountSetupModal">
Open account completion
</button>

<!-- Modal -->
<div class="modal fade" id="accountSetupModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Complete account setup</h1>
        <button type="button" class="btn-close skip-account-completion" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="account_completion_feeds">

  
      <div class="modal-body">

      <form action="" method="post" class="add_username_form">

         <div class="form-floating mb-3">
        <input type="text" class="form-control" id="floatingInput" placeholder="John Kamau" name="official_names" required>
        <label for="floatingInput">Official Names</label>
      </div>

    

      


      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button> -->

         <button type="button" disabled class="btn btn-secondary">Prev</button>
        <button type="submit" class="btn btn-primary">Next</button>

      </div>
        </form>

      </div>
    </div>
  </div>
</div>

<!--  Subscription Modal -->
<div class="modal fade" id="subscriptionModal" tabindex="-1" aria-labelledby="subscriptionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content subscription_modal">
  
    </div>
  </div>
</div>


<!-- #Subscription info modal-->


<!-- Modal -->
<div class="modal fade" id="infoSubModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content sub_info_data">
    
    </div>
  </div>
</div>

<!-- #account deposit modal-->
<!-- Modal -->
<div class="modal fade" id="depositModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content form_deposit_feeds">
 <form action="" method="post" class="submit_deposit_amount_form">

    <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Deposit to your account</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

 
     

   

      <div class="form-floating mb-3">
      <input type="number" class="form-control amount_to_deposit" id="floatingInput" name="amount" min="1" max="8000" placeholder="Enter the number">
      <label for="floatingInput">Amount to deposit</label>
      </div>

 
      </div>

  

      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
        <button type="submit" class="btn btn-primary">Proceed</button>
      </div>

      </form>

    </div>
  </div>
</div>


<div class="modal fade" id="renewModal" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content load-renew-modal">
    
    </div>
  </div>
</div>


<script src="https://www.paypal.com/sdk/js?client-id=AZTgoqj0EfGK89tr88fbxjbuxhfbjGNHVF9qD4jwoVeG8tPDEnMEi54rpT3ia_4J5hKiC-rD_pkh716k&components=buttons"></script>



<?php include('includes/footer.php');?>