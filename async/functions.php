<?php
include('connection.php');

require __DIR__ . '/../vendor/autoload.php';


function escapeString($string){
global $connection;

return $string = mysqli_real_escape_string($connection,$string);

}

function checkQuery($result){
global $connection;
if($result){

}else{
    die("Query failed".mysqli_error($connection));

}  
}

function warnMsg($msg){
    echo '
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
    '.$msg.'
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
  }
  
  function successMsg($msg){
    echo '
    <div class="alert alert-success alert-dismissible fade show" role="alert">
    '.$msg.'
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
  }
  
  //fail msg
  function failMsg($msg){
    echo '
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
    '.$msg.'
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
  }
  
  function generateUserId($mail){

    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';   // Shuffle the $str_result and returns substring
    // of specified length
    $localPart = getLocalPart($mail);
    $gen_usr_id = "U-".$localPart."-". substr(str_shuffle($str_result),
                        0, 15);
      
   return $gen_usr_id;  
  }

  function generateSubId($mail){

    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';   // Shuffle the $str_result and returns substring
    // of specified length
   
    $gen_usr_id = "S-". substr(str_shuffle($str_result).$mail,
                        0, 15);
      
   return $gen_usr_id;  
  }

  function getLocalPart($email) {
    // Validate the email address first
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email address.";
    }
    
    // Extract the part before the '@' symbol
    $localPart = substr($email, 0, strpos($email, '@'));
    return $localPart;
}





function setSession($mail){
    global $connection;
  
    $mail = escapeString($mail);
  
    $query = "SELECT * FROM smart_park_users WHERE mail = '$mail'";
    $select_users = mysqli_query($connection,$query);
    checkQuery($select_users);
  
    while($row = mysqli_fetch_assoc($select_users)){
    $_SESSION['usr_id'] = $row['usr_id'];
    $db_mail = $row['mail'];
  
    }
    //trim content after @
    $parts = explode('@',$db_mail);
    $_SESSION['mail'] = $parts[0];
     
    //assigning session variable
  
  
  }

        
  function calculateDueDate($startDate, $interval) {
    // Create a DateTime object from the start date
    $date = new DateTime($startDate);
    
    // Determine the interval to add
    switch (strtolower($interval)) {
        case 'daily':
            $date->modify('+1 day');
            break;
        case 'weekly':
            $date->modify('+1 week');
            break;
        case 'monthly':
            $date->modify('+1 month');
            break;
        case 'yearly':
            $date->modify('+1 year');
            break;
        default:
            throw new Exception("Invalid interval. Please use 'daily', 'weekly', 'monthly', or 'yearly'.");
    }
    
    // Return the DateTime object
    return $date;
}


function formatDateWithOrdinalSuffix($dateStr) {
  // Create a DateTime object from the given date string
  $date = new DateTime($dateStr);

  // Get the day, month, and year components
  $day = $date->format('j');
  $month = $date->format('M');
  $year = $date->format('Y');

  // Determine the ordinal suffix for the day
  if ($day % 10 == 1 && $day != 11) {
      $suffix = 'st';
  } elseif ($day % 10 == 2 && $day != 12) {
      $suffix = 'nd';
  } elseif ($day % 10 == 3 && $day != 13) {
      $suffix = 'rd';
  } else {
      $suffix = 'th';
  }

  // Format the date with the ordinal suffix
  $formattedDate = $day . $suffix . ' ' . $month . ' ' . $year;

  return $formattedDate;
}

function formatTimeWithOrdinalSuffix($dateStr) {
  // Create a DateTime object from the given date string
  $date = new DateTime($dateStr);

  // Get the hour, minute, second, and AM/PM components
  $hour = $date->format('g'); // 12-hour format without leading zeros
  $minute = $date->format('i');
  $second = $date->format('s');
  $ampm = $date->format('A'); // AM or PM

  // Format the time with AM/PM
  $formattedTime = $hour . ':' . $minute . ':' . $second . ' ' . $ampm;

  return $formattedTime;
}

function convertKshToUsd($amountInKsh, $exchangeRate) {
  // Calculate the amount in USD
  $amountInUsd = $amountInKsh * $exchangeRate;

   // Round the result to two decimal places
   $amountInUsd = round($amountInUsd, 2);

  return $amountInUsd;
}

function calculatePeriodPercentage($startDate, $endDate) {
  // Convert the dates to DateTime objects
  $start = new DateTime($startDate);
  $end = new DateTime($endDate);
  $current = new DateTime(); // Get the current date and time

  // Ensure the current date is within the start and end dates
  if ($current < $start) {
      return 0;
  } elseif ($current > $end) {
      return 100;
  }

  // Calculate the total period in seconds
  $totalPeriod = $end->getTimestamp() - $start->getTimestamp();

  // Calculate the period covered until the current date in seconds
  $coveredPeriod = $current->getTimestamp() - $start->getTimestamp();

  // Calculate the percentage of the period covered
  $percentageCovered = ($coveredPeriod / $totalPeriod) * 100;

  // Round the percentage to the nearest whole number
  return round($percentageCovered);
}

function isValidNumber($value) {
  return is_numeric($value) && $value >= 1;
}


function hasDateElapsed($dateStr) {
  // Create a DateTime object from the given date string
  $date = new DateTime($dateStr);
  // Get the current date and time
  $currentDate = new DateTime();

  // Check if the given date has passed
  if ($currentDate > $date) {
      return true;
  } else {
      return false;
  }
}
//function to update elapsed subbscription

function updateElapsedSubscriptions(){
  global $connection;

  if(isset($_SESSION['usr_id'])){
     $usr_id = escapeString($_SESSION['usr_id']);

  $query = "SELECT * FROM active_subscription WHERE usr_id = '$usr_id'";
  $select_subs = mysqli_query($connection,$query);
  checkQuery($select_subs);
  if(mysqli_num_rows($select_subs) !==0 ){

    while($row = mysqli_fetch_assoc($select_subs)){
    $due_date = $row['due_date'];
    $sub_id = $row['sub_id'];
   

 if(hasDateElapsed($due_date)){
    
      $query = "UPDATE active_subscription SET time_status = 'elapsed' WHERE sub_id = '$sub_id'";
      $update_time_status = mysqli_query($connection,$query);
      checkQuery($update_time_status);

    }
 }

  }
  }
 

}

updateElapsedSubscriptions();

function getUserBalance($usr_id){
  global $connection;

$query = "SELECT balance FROM smart_park_users WHERE usr_id = '$usr_id'";
$select_balance = mysqli_query($connection,$query);
while($row = mysqli_fetch_assoc($select_balance)){
$balance = $row['balance'];

}

if(empty($balance)){
    $balance = '0';
}

return $balance;

}

function getIntervalByPackageId($package_id){
  global $connection;

  $query = "SELECT package_interval FROM packages WHERE package_id = $package_id";
  $select_interval = mysqli_query($connection,$query);
  checkQuery($select_interval);
  while($row = mysqli_fetch_assoc($select_interval)){
  $package_interval = $row['package_interval'];
  }

  return $package_interval;

}
function autoRenewSubscriptions(){
  global $connection;

  if(isset($_SESSION['usr_id'])){
     $usr_id = escapeString($_SESSION['usr_id']);

     $query = "SELECT SUM(amount) FROM active_subscription WHERE usr_id = '$usr_id' AND time_status = 'elapsed' AND auto_renew = 'yes'";
     $select_autorenew_list = mysqli_query($connection,$query);
     checkQuery($select_autorenew_list);
     if(mysqli_num_rows($select_autorenew_list) !== 0){
      //check if balance is succifient
      while($row = mysqli_fetch_assoc($select_autorenew_list))
       $package_total_fees = $row['SUM(amount)'];
     }
     $usr_balance = getUserBalance($usr_id);
     if($usr_balance  > $package_total_fees){
      //user balance must be greater than total packages subscribed
       $remained_balance = $usr_balance - $package_total_fees;
       

       $query = "SELECT * FROM active_subscription WHERE  usr_id = '$usr_id' AND time_status = 'elapsed' AND auto_renew = 'yes'";
      $select_renew_sub = mysqli_query($connection,$query);
      checkQuery($select_renew_sub);

      while($row = mysqli_fetch_assoc($select_renew_sub)){
      $sub_id = $row['sub_id'];
      $package_id =  $row['package_id'];
   
        $package_interval = getIntervalByPackageId($package_id);


       // Use the current date formatted as a string
      $startDate = (new DateTime())->format('Y-m-d H:i:s');
      $due_date = calculateDueDate($startDate,$package_interval);
      $due_date = $due_date->format('Y-m-d H:i:s');

  
       //update all user elapse packages if balance is greater
      $query = "UPDATE active_subscription SET time_status = 'active',date_created = '$startDate',due_date = '$due_date' WHERE sub_id = '$sub_id'";
      $activate_subscriptions = mysqli_query($connection,$query);
      checkQuery($activate_subscriptions);

      }

   

      //update user balance
      $query = "UPDATE smart_park_users SET balance = '$remained_balance' WHERE usr_id = '$usr_id'";
      $update_user_balance = mysqli_query($connection,$query);
      checkQuery($update_user_balance);
       


     }else{
      failMsg("Cannot auto renew your subscriptions your balance too low");
     }
    
    }



}


function getOfficialNamesById($usr_id){
  global $connection;
  $query = "SELECT official_names FROM smart_park_users WHERE usr_id = '$usr_id'";
  $select_user = mysqli_query($connection,$query);
  while($row = mysqli_fetch_assoc($select_user)){
  $official_names = $row['official_names'];

  }
  return $official_names;

}


function generateOrderId(){
$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';   // Shuffle the $str_result and returns substring
// of specified length

$gen_order_id = substr(str_shuffle($str_result),
                    0, 15);
  
return $gen_order_id;  
}

function templateBody($message, $sender_mail) {
  $htmlContent = '
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiVideo</title>
    <style>
    .container{
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        
    }
    .content_card{
        border: 2px solid #f1f1f1;
        border-radius:20px;
        width: 100%;
        max-width: 400px;
    }
    .brand_logo{
        font-size: 20px;
        text-align: center;
        font-weight: 600;
    }
    .send_content{
    margin:20px 0;
    }
    .content_message{
        margin:20px 20px;
    }
    .footer_content{
        margin:30px 20px;
        font-weight: 600;
        text-align: end;
    }
    body{
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
    
    }
    </style>
  </head>
  <body>
  <div class="container">
  
  <div class="content_card">
  
  
  <div class="brand_logo">
  
      <img src="https://res.cloudinary.com/dx8t5kvns/image/upload/v1718277755/r56m2tilotrg83xxgozi.png" 
       width="150px"
      alt="">
      <div><span style="color:black;">Smart</span><span style="color:#f6b417;">parking</span></div>
      
  </div>
  
  <div class="content_message">

  <div class="other_content"><b>'.$message.'</b></div>
  
  
  <div class="footer_content">
    This message is sent to you by Smart parking best application in world
  </div>
  
  </div>
  
  
  </div>
  
  </div>
  
      
  </body>
  </html>';

  // Replace placeholders with dynamic content
 
  return $htmlContent;
}

function createAccountTemplateBody($message, $sender_mail) {
  $htmlContent = '
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiVideo</title>
    <style>
    .container{
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        
    }
    .content_card{
        border: 2px solid #f1f1f1;
        border-radius:20px;
        width: 100%;
        max-width: 400px;
    }
    .brand_logo{
        font-size: 20px;
        text-align: center;
        font-weight: 600;
    }
    .send_content{
    margin:20px 0;
    }
    .content_message{
        margin:20px 20px;
    }
    .footer_content{
        margin:30px 20px;
        font-weight: 600;
        text-align: end;
    }
    body{
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
    
    }
    </style>
  </head>
  <body>
  <div class="container">
  
  <div class="content_card">
  
  
  <div class="brand_logo">
  
      <img src="https://res.cloudinary.com/dx8t5kvns/image/upload/v1718277755/r56m2tilotrg83xxgozi.png" 
       width="150px"
      alt="">
      <div><span style="color:black;">Smart</span><span style="color:#f6b417;">parking</span></div>
      
  </div>
  
  <div class="content_message">
 
  <div class="other_content">Thanks for joining Smart parking! We are excited to have you on board.
  To complete your account setup and unlock all the features of  Smart parking, please verify your email address.</div>
  
  <div class="other_content">Here is your verification code<b>'.$message.'</b></div>
  
  
  <div class="footer_content">
      This message is sent to you by Smart parking best application in world
  </div>
  
  </div>
  
  
  </div>
  
  </div>
  
      
  </body>
  </html>';

  // Replace placeholders with dynamic content
 
  return $htmlContent;
}


function generateVerificationCode($numDigits){
  // Validate input
if ($numDigits <= 0) {
throw new InvalidArgumentException('Number of digits must be positive.');
}

// Initialize empty string for the random digits
$randomDigits = '';

// Loop to generate random digits
for ($i = 0; $i < $numDigits; $i++) {
// Generate a random digit between 0 and 9 (inclusive)
$randomDigit = rand(0, 9);
// Append the digit to the string
$randomDigits .= (string) $randomDigit;
}

return $randomDigits;
}

function createLoginCookies($mail){
  global $connection;
  //store login cookie on user computer
// Generate a random name and value for the cookie
$random_name = bin2hex(random_bytes(16));
$random_value = bin2hex(random_bytes(32));

// Calculate the expiration time (30 days from now)
$expiration_time = time() + (30 * 24 * 60 * 60); // 30 days * 24 hours * 60 minutes * 60 seconds

// Store the random name in a separate cookie
setcookie('smartpark', $random_name, time() + 3600, '/', 'localhost', true, true);

// Set cookie with secure, HttpOnly flags, 30-day expiry time, path, and domain (localhost)
setcookie($random_name, $random_value, $expiration_time, '/', 'localhost', true, true);

//store cookie to database
$query = "UPDATE smart_park_users SET cookie_token_name = '$random_name',cookie_token_value = '$random_value' WHERE mail = '$mail'";
$update_cookie = mysqli_query($connection,$query);
checkQuery($update_cookie);
}
function formatLicensePlate($licensePlate) {
  // Check if the length of the license plate is exactly 7 characters
  if (strlen($licensePlate) == 7) {
      // Insert a space after the first three characters
      $formattedPlate = substr($licensePlate, 0, 3) . ' ' . substr($licensePlate, 3);
      return $formattedPlate;
  } else {
      return "Invalid license plate format.";
  }
}