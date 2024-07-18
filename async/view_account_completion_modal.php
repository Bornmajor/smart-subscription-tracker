<?php
include('functions.php');
?>
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
  <button type="submit" class="btn btn-primary add-username-btn">Next</button>

</div>
  </form>

  </div>

  <script>
        $(".add_username_form").submit(function(e){
      
            e.preventDefault();

            let postData = $(this).serialize();

            $.post("async/add_official_names.php",postData,function(data){
               $(".account_completion_feeds").html(data);
            }) 

        })
  </script>