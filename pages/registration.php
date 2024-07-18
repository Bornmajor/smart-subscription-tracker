<?Php $page = 'Registration'; ?>
<?php include('includes/header.php');?>

<div class="container">

<div class="form_container">
    <div class="form_cover_img" >
        <img src="assets/images/park-logo.png"  alt="">
        <h4>Smart parking</h4>
    </div>

    <div class="form_content">

    <div class="mb-3 form-header">Setup an account</div>

    <div class="auth-feedback">

    <form class="registration_form"  method="post">

  

    <div class="form-floating mb-3">
    <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com"  name="mail" required>
    <label for="floatingInput">Email address</label>
    </div>

    <div class="pwd_validation_msg mb-2"></div>

   <div class="form-floating">
    <input type="password" class="form-control" name="pwd"  id="pwd" placeholder="Password" required>
    <label for="floatingPassword">Password</label>
    <span class="inline-eye slash-visible" style="display:none;"><i class="fas fa-lg fa-eye-slash"></i></span>
    <span class="inline-eye slash-invisible" ><i class="fas fa-lg fa-eye"></i></span>
    </div>


  <div class="my-4 form-link-separator">
  <a href="?page=login" class="app-links"> <i class="fas fa-key"></i> Login to account</a>
    <a href="?page=forgot_password" class="forgot-pwd app-links"> <i class="fas fa-unlock"></i> Forgot password</a>
</div>

  <div class="mt-3">
    <button class="btn btn-primary form_submit form_submit_btn" type="submit">Create account</button>
  </div>

 </form>
</div>
 
    </div>
</div>

</div>

<script>
          function validatePassword(password) {
    const minLength = 8;
    const hasUppercase = /[A-Z]/.test(password);
    const hasLowercase = /[a-z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    const hasSpecialChar = /[!@#\$%\^\&*\)\(+=._-]/.test(password);
    const isValidLength = password.length >= minLength;

    console.log("Password:", password);
    console.log("isValidLength:", isValidLength);
    console.log("hasUppercase:", hasUppercase);
    console.log("hasLowercase:", hasLowercase);
    console.log("hasNumber:", hasNumber);
    console.log("hasSpecialChar:", hasSpecialChar);



    if (isValidLength && hasUppercase && hasLowercase && hasNumber && hasSpecialChar) {
         // alert(message);
         $(".pwd_validation_msg").html("");
         $(".pwd_validation_msg").slideUp(1000);
         $('.form_submit_btn').attr("disabled",false);

        return true;

    } else {
        let message = "Password must be at least 8 characters long, contain an uppercase letter, a lowercase letter, a number, and a special character.";
        if (!isValidLength) message = "Password must be at least 8 characters long.";
        else if (!hasUppercase) message = "Password must contain at least one uppercase letter.";
        else if (!hasLowercase) message = "Password must contain at least one lowercase letter.";
        else if (!hasNumber) message = "Password must contain at least one number.";
        else if (!hasSpecialChar) message = "Password must contain at least one special character.";
        
        // alert(message);
        $(".pwd_validation_msg").slideDown(2000);
        $(".pwd_validation_msg").html(message);
        $('.form_submit_btn').attr("disabled",true);
        return false;
    }
}


$("#pwd").keyup(function(e){
    let pwd  = $(this).val();
    if(validatePassword(pwd)){
    //pwd valid //display submit button

    }else{
    
    }
}) 

</script>

<?php include('includes/footer.php'); ?>