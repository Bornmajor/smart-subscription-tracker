<?php
include('functions.php');

$official_names = escapeString($_POST['official_names']);


?>

<div class="account_completion_official_feeds">

<form action="" method="post" class="add_region_form">   

  <div class="modal-body">



<div class="form-floating">
  <select class="form-select" id="floatingSelect" name="region" aria-label="Floating label select example" required>
    <option selected value="">Region</option>
    <option value="Gatanga Sub-County"> Gatanga Sub-County</option>
    <option value="Kandara Sub-County">Kandara Sub-County</option>
    <option value="Kangema Sub-County">Kangema Sub-County</option>

    <option value="Kigumo Sub-County">Kigumo Sub-County</option>
    <option value="Mathioya Sub-County">Mathioya Sub-County</option>
    <option value="Kiharu Sub-County">Kiharu Sub-County</option>

    <option value="Murang'a South Sub-County">Murang'a South Sub-County</option>
    <option value="Kahuro Sub-County">Kahuro Sub-County</option>
   




  </select>
  <label for="floatingSelect">Select a subcounty</label>
</div>

<input type="hidden" name="official_names" value="<?php echo $official_names  ?>" required>






</div>
<div class="modal-footer">
  <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
  <button type="button" class="btn btn-primary">Save changes</button> -->

   <a type="button" href="" class="btn btn-secondary add-username-btn">Prev</a>
  <button type="submit" class="btn btn-primary ">Next</button>

</div>


</form> 

  </div>

  <script>
         $(".add_region_form").submit(function(e){
            e.preventDefault();

            let postData = $(this).serialize();

            $.post("async/add_region.php",postData,function(data){
               $(".account_completion_official_feeds").html(data);
            }) 

        })

  </script>