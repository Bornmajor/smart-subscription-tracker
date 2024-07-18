// Use the current date formatted as a string
    $todayDate = (new DateTime())->format('Y-m-d H:i:s');
    $dueDate = calculateDueDate($todayDate,$package_interval);
    $string_due_date = $dueDate->format('Y-m-d H:i:s');
    echo formatDateWithOrdinalSuffix($string_due_date);