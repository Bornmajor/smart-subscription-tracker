<?php
include('functions.php');

$usr_id = escapeString($_SESSION['usr_id']);
$query = "SELECT * FROM vehicles WHERE usr_id = '$usr_id'";
$select_user_vehicles = mysqli_query($connection,$query);
checkQuery($select_user_vehicles);
if(mysqli_num_rows($select_user_vehicles) !== 0){
while($row = mysqli_fetch_assoc($select_user_vehicles)){
$vehicle_id = $row['vehicle_id'];
$plate_no = $row['plate_no'];
?>
<div class="radio-vehicle-btn d-inline">
<input type="radio" class="btn-check "  name="plate_number" value="<?php echo $plate_no; ?>" id="success-outlined-<?php echo $vehicle_id ?>" autocomplete="off" >
<label class="btn btn-outline-secondary" for="success-outlined-<?php echo $vehicle_id ?>"><i class="fas fa-car"></i> <?php echo formatLicensePlate($plate_no) ?></label>

</div>


<?php
}  
}

?>

<script>
     $(".radio-vehicle-btn").click(function(){
          console.log('Clicked')
        })
</script>