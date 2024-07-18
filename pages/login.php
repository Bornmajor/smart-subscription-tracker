<?Php $page = 'Login'; ?>
<?php include('includes/header.php');?>

<?php
if(isset($_COOKIE['smartpark'])){

  $optvideo_cookie = $_COOKIE['smartpark'];
  
  if(isset($_COOKIE[$optvideo_cookie])){
  //get actual cookie_token_name
  $cookie_token_name = $_COOKIE[$optvideo_cookie];
  $cookie_token_value = $cookie_token_name;
  
  //extract mail  and set session
  //check for value its enough
  $query = "SELECT mail FROM smart_park_users WHERE cookie_token_value = '$cookie_token_value'";
  $select_mail = mysqli_query($connection,$query);
  checkQuery($select_mail);
  while($row = mysqli_fetch_assoc($select_mail)){
  $mail = $row['mail'];
  }
  
  setSession($mail);
  
  
  if(isset($_SESSION['usr_id'])){
  header("Location: ?page=dashboard");
  exit;
  }    
  
  }
  
  
  }
?>
<div class="container">

<div class="form_container">
    <div class="form_cover_img" >
        <img src="assets/images/park-logo.png"  alt="">
        <h4>Smart parking</h4>
    </div>

    <div class="form_content">

    <div class="mb-3 form-header">Welcome, back</div>

    <div class="auth-feedback">
    <form class="login_form" method="post">

    <?Php
     if(isset($_GET['pwd_reset'])){
       $pwd_reset = $_GET['pwd_reset'];
       if($pwd_reset == 'failed'){
        failMsg("Password reset failed");
       }
     }
    ?>


    
    <div class="form-floating mb-3">
    <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com"  name="mail" required>
    <label for="floatingInput">Email address</label>
    </div>

    <div class="form-floating">
    <input type="password" class="form-control" name="pwd"  id="pwd" placeholder="Password" required>
    <label for="floatingPassword">Password</label>
    <span class="inline-eye slash-visible" style="display:none;"><i class="fas fa-lg fa-eye-slash"></i></span>
    <span class="inline-eye slash-invisible" ><i class="fas fa-lg fa-eye"></i></span>
    </div>


    <div class="my-3 form-check">
    <input type="checkbox" class="form-check-input" id="exampleCheck1" name="store_user" value="store_user">
    <label class="form-check-label" for="exampleCheck1">Keep me signed in</label>
    </div>

  <div class="my-4 form-link-separator">
  <a href="?page=registration" class="app-links"> <i class="fas fa-user-plus"></i> Create an account</a>
    <a href="?page=forgot_password" class="forgot-pwd app-links"> <i class="fas fa-unlock"></i> Forgot password</a>
</div>

  <div class="mt-3">
    <button class="btn btn-primary form_submit_btn" type="submit">Login</button>
  </div>

  </form>

    </div>

  

    </div>
</div>

</div>

<?php include('includes/footer.php'); ?>