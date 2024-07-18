<?php
include('functions.php');

$package_id = escapeString($_POST['package_id']);

$query = "SELECT * FROM packages WHERE package_id = $package_id";
$select_packages = mysqli_query($connection,$query);
checkQuery($select_packages);
while($row = mysqli_fetch_assoc($select_packages)){
$package_id = $row['package_id'];
$package_title = $row['package_title'];
$package_fee = $row['package_fee'];
$package_interval = $row['package_interval'];
}
?> 
<form action="" method="post" class="add_plate_number_form">

    <div class="modal-header">
   
        <h1 class="modal-title fs-5" id="exampleModalLabel">Subscribing to <?php echo $package_title; ?></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

       <!-- <div class="load_vehicles_toggle_btns my-3"></div> -->




       
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="floatingInput" name="plate_number" placeholder="KCA 808E (LLL NNNL)" required>
        <label for="floatingInput">Vehicle registration Number</label>
        </div>

        <div class="my-3 mx-1 form-check">
        <input type="checkbox" class="form-check-input" id="exampleCheck1" name="auto_renew" value="yes">
        <label class="form-check-label" for="exampleCheck1">Auto renewal</label>
        </div>

        <input type="hidden" name="package_id" value="<?php echo $package_id; ?>">

    
      </div>
      <div class="modal-footer">

      <button type="submit" class="btn btn-primary">Proceed</button>


        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button> -->
      </div> 
    </form>
    <script src="https://www.paypal.com/sdk/js?client-id=AZTgoqj0EfGK89tr88fbxjbuxhfbjGNHVF9qD4jwoVeG8tPDEnMEi54rpT3ia_4J5hKiC-rD_pkh716k&components=buttons"></script>


    <script>
      
      function loadVehicleToggleBtns(){
            $.ajax({
                url: "async/load_vehicle_toggle_btns.php",
                type: "POST",
                success:function(data){
                    if(!data.error){
                    $('.load_vehicles_toggle_btns').html(data);
                    }

                }
                })   
        }

        loadVehicleToggleBtns();
            $(".add_plate_number_form").submit(function(e){
            e.preventDefault();

            let postData = $(this).serialize();

            $.post("async/make_payment.php",postData,function(data){
                
                $(".add_plate_number_form").html(data);
            })
        });

       
    </script>
 
  