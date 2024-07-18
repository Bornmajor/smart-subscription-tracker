<?php
include('functions.php');

$official_names = escapeString($_POST['official_names']);
$region = escapeString($_POST['region']);


?>
<div class="account_completion_feeds">

<form action="" method="post" class="add_national_id_form">
  <div class="modal-body">



<div class="form-floating mb-3">
        <input type="text" class="form-control" id="floatingInput" placeholder="ID" name="national_id" required>
        <label for="floatingInput">National ID</label>
</div>

<input type="hidden" name="official_names" value="<?Php echo $official_names  ?>" required>
<input type="hidden" name="region" value="<?Php echo $region  ?>" required>






</div>
<div class="modal-footer">
  <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
  <button type="button" class="btn btn-primary">Save changes</button> -->

   <a type="button" official-names="<?Php echo $official_names; ?>" class="btn btn-secondary add-username-btn">Prev</a>
  <button type="submit" class="btn btn-primary">Next</button>

</div>
  </form>

  </div>

  <script>
    $(".add_national_id_form").submit(function(e){
        e.preventDefault();

        let postData = $(this).serialize();

        $.post("async/process_account_setup.php",postData,function(data){
            $(".account_completion_feeds").html(data);
        })
    });

      $(".add-username-btn").click(function(){
        console.log('Clicked add');

        let official_names =  $(this).attr("official-names");

        $.post("async/add_official_names.php",{official_names:official_names},function(data){
            $(".account_completion_feeds").html(data);
        }) 

        })

    
  </script>