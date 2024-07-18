

<!-- Modal -->
<div class="modal fade" id="addVehicleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="" method="post" class="save_vehicle_form">

      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add vehicles</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">

      <label for="exampleInputEmail1" class="form-label">Format entry of (L -letter & N - number)</label>
      <br>
      <label for="exampleInputEmail1" class="form-label">LLL NNNL</label>

      <div class="plate_no_feeds"></div>
      <div class="plate_validation mb-2"></div>

    <div class="plate_no_entry">
 
    <input type="text" class="form-control plate_entry_one" maxlength="3" id="exampleInputEmail1" name="plate_one" placeholder="KCA" required>
    <input type="text" class="form-control plate_entry_two mx-2" maxlength="4" id="exampleInputEmail1" name="plate_two"  placeholder="808E" required>
    <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
    </div>

      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
        <button type="submit" class="btn btn-primary save-vehicle-asset">Save asset</button>
      </div>

      </form>

    </div>
  </div>
</div>