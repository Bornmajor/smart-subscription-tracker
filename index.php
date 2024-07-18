<?php
if(isset($_GET['page'])){
   $source = $_GET['page'];

}else{
    $source = '';
}

switch($source){
    case 'dashboard';
    include('pages/dashboard.php');
    break;
    case 'registration';
    include('pages/registration.php');
    break;
    case 'vehicles';
    include('pages/vehicles.php');
    break;
    case 'support';
    include('pages/support.php');
    break;
    case 'forgot_password';
    include('pages/forgot_password.php');
    break;
    case 'reset_password';
    include('pages/reset_password.php');
    break;
    case 'logout';
    include('pages/logout.php');
    break;
    default:
    include('pages/login.php');

}
    
 include('components/modals.php');