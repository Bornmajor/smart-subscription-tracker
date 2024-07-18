<?php
// //correcting time
// date_default_timezone_set('UTC');
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

try {
   // Use the current date formatted as a string
   $startDate = (new DateTime())->format('Y-m-d H:i:s');
    $interval = 'daily';
    $dueDate = calculateDueDate($startDate, $interval);
    echo "The due date is: " . $dueDate->format('Y-m-d H:i:s'); // Output: The due date is: 2024-06-17 00:00:00
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}


function convertKshToUsd($amountInKsh, $exchangeRate) {
    // Calculate the amount in USD
    $amountInUsd = $amountInKsh * $exchangeRate;
  
    return $amountInUsd;
  }

  echo "<br>";






//   function calculatePeriodPercentage($startDate, $endDate) {
//       // Convert the dates to DateTime objects
//       $start = new DateTime($startDate);
//       $end = new DateTime($endDate);
//       $current = new DateTime(); // Get the current date and time
  
//       // Ensure the current date is within the start and end dates
//       if ($current < $start) {
//           return 0;
//       } elseif ($current > $end) {
//           return 100;
//       }
  
//       // Calculate the total period in seconds
//       $totalPeriod = $end->getTimestamp() - $start->getTimestamp();
  
//       // Calculate the period covered until the current date in seconds
//       $coveredPeriod = $current->getTimestamp() - $start->getTimestamp();
  
//       // Calculate the percentage of the period covered
//       $percentageCovered = ($coveredPeriod / $totalPeriod) * 100;
  
//       // Round the percentage to the nearest whole number
//       return round($percentageCovered);
//   }
  
//   // Example usage with dates differing only in time
//   $startDate = "2024-06-11 12:43:50";
//   $endDate = "2024-06-12 12:43:50";
  
//   echo calculatePeriodPercentage($startDate, $endDate) . "%";
  echo "<br>";

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

  // Example usage
$dateStr = '2024-06-12 12:43:50';
if (hasDateElapsed($dateStr)) {
    echo "The date $dateStr has passed.";
} else {
    echo "The date $dateStr has not yet passed.";
}
echo "<br>";
// $todayDate = (new DateTime())->format('Y-m-d H:i:s');
// $dueDate = calculateDueDate($todayDate, 'monthly');
// $string_due_date = $dueDate->format('Y-m-d H:i:s');

echo bin2hex(random_bytes(32));
// echo $string_due_date ;
  
       // Use the current date formatted as a string

  
