<?php include('async/functions.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/park-logo.png" type="image/x-icon">
    <title>Smark parking | <?php
     if(isset($page)){
     echo $page;
     }else{
        echo '';
     }
    ?></title>

     <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    
    <link rel="stylesheet" href="assets/css/bootstrap.min.css"> 
    <link href="assets/css/sb-admin-2.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css"> 
    <link rel="stylesheet" href="assets/css/css-circular-prog-bar.css">


    <script src="assets/js/jquery-3.6.3.min.js"></script>
    <!-- #add client id here-->



</head>
