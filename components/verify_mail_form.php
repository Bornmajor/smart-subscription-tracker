<form action="" method="post" class="verify_token_form">

   <div class="my-3 alternative-link">
    Did not receive the code? <a  class="resend-code" >resend</a>
    </div>

    <div class="code_form_feeds"></div>

   
     <input type="hidden" name="auth_type" value="<?php echo $auth_type; ?>">
    <input type="hidden" name="mail" value="<?php echo $_POST['mail'] ?>">
    <div class="mb-3">You need to verify your account to proceed</div>
    <div class="form-floating mb-3">
      
    <input type="text" class="form-control" id="floatingInput" placeholder="123345" name="code_token" required>
    <label for="floatingInput">Enter verification code send to your mail (******)</label>
    </div>

    <div class="my-3">
    <button class="btn btn-primary w-100" type="submit">Proceed</button>     
    </div>



</form>

<script>
$('.verify_token_form').submit(function(e){
e.preventDefault(); 
let postData =  $(this).serialize();

$.post("async/verify_token.php",postData,function(data){
$('.code_form_feeds').html(data);
})

});

$(".resend-code").click(function(e){
  e.preventDefault();
let mail = "<?php echo $_POST['mail'];  ?>";
$.post("async/resend_verification_code.php",{mail:mail},function(data){
  if(!data.error){
  $(".code_form_feeds").html(data);
  }
})
})

</script>