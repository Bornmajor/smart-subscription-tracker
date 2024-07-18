<?php
include('functions.php');

$usr_id = escapeString($_SESSION['usr_id']);

$query = "SELECT * FROM vehicles WHERE usr_id = '$usr_id'";
$select_vehicles = mysqli_query($connection,$query);
checkQuery($select_vehicles);

if(mysqli_num_rows($select_vehicles)!== 0){
?>
<h4>Saved vehicles</h4>
<div class="container-cards">


<?php
while($row = mysqli_fetch_assoc($select_vehicles)){
$plate_no = $row['plate_no'];
$vehicle_id = $row['vehicle_id'];
?>

<div class="vehicle-saved-card card" style="">
<div class="card-body">
<h5 class="card-title"><?php echo $plate_no ?></h5>
<a vehicle-id="<?php echo $vehicle_id; ?>" class="text-danger remove-vehicle">Remove</a>
</div>
</div>

<?php
}
?>
</div>
<?php
}else{
?>
<div class="no_items">
   <img src="assets/images/placeholder-car.png" alt="No assets" >

  <h5 class="my-2">No saved asset</h5>
</div>

<?php
}

?>
<script>
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
        
    $(".remove-vehicle").click(function(){
        
        let vehicle_id = $(this).attr("vehicle-id");

        $.post("async/remove_saved_vehicle.php",{vehicle_id:vehicle_id},function(data){
            loadSavedVehicles();
        })
    })
</script>