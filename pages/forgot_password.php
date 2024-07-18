<?Php $page = 'Reset password'; ?>
<?php include('includes/header.php');?>


<div class="container">

<div class="form_container">
    <div class="form_cover_img" >
        <img src="assets/images/park-logo.png"  alt="">
        <h4>Smart parking</h4>
    </div>

    <div class="form_content">

    <div class="mb-3 form-header">Password recovery</div>

    <!-- <h6 class="mb-3 text-align-center">You will re</h6> -->

    <div class="auth-feedback">
    <form class="forget_pwd_form" method="post">


    <div class="forgot_pwd_form_feeds"></div>
    
    <div class="form-floating mb-3">
    <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com"  name="mail" required>
    <label for="floatingInput">Email address</label>
    </div>



  <div class="my-4 form-link-separator">
  <a href="?page=login" class="app-links">  Login instead</a>
    <!-- <a href="?page=forgot_password" class="forgot-pwd app-links"> <i class="fas fa-unlock"></i> Forgot password</a> -->
</div>

  <div class="mt-3">
    <button class="btn btn-primary form_submit_btn" type="submit">Recover account</button>
  </div>

  </form>

    </div>

  

    </div>
</div>

</div>

<?php include('includes/footer.php'); ?>