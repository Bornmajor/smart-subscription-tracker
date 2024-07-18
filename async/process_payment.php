<?php
header('Content-Type: application/json');
include('functions.php');

// Replace these values with your own PayPal credentials
$clientId = "AZTgoqj0EfGK89tr88fbxjbuxhfbjGNHVF9qD4jwoVeG8tPDEnMEi54rpT3ia_4J5hKiC-rD_pkh716k";
$clientSecret = "EEw5Rj6eAuvBuJR64DLUe21dplas74NWFTEYECpb4NBP0HFkN_3DoPyZD7tFaXnS8yCy0nMISSuUgkWM";

$paypalUrl = "https://api.sandbox.paypal.com"; // Use "https://api.paypal.com" for live

// Get the request body
$requestBody = file_get_contents('php://input');
$data = json_decode($requestBody, true);

// Function to get an access token from PayPal
function getAccessToken($paypalUrl, $clientId, $clientSecret) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$paypalUrl/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$clientSecret");
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

    $response = curl_exec($ch);
    curl_close($ch);

    if(empty($response)) {
        return null;
    } else {
        $jsonData = json_decode($response);
        return $jsonData->access_token;
    }
}

// Function to create a PayPal order
function createOrder($paypalUrl, $accessToken, $cart) {
    global $connection;
    $items = array_map(function($item) {
        return [
            "name" => $item["name"], // Name of the product
            "sku" => $item["sku"],
            "unit_amount" => [
                "currency_code" => "USD",
                "value" => $item["amount"] // Amount for each item
            ],
            "quantity" => $item["quantity"]
        ];
    }, $cart);

    $itemTotalValue = array_reduce($cart, function($sum, $item) {
        return $sum + ($item["amount"] * $item["quantity"]);
    }, 0);

    $orderData = [
        "intent" => "CAPTURE",
        "purchase_units" => [
            [
                "amount" => [
                    "currency_code" => "USD",
                    "value" => number_format($itemTotalValue, 2, '.', ''), // Total amount for the order
                    "breakdown" => [
                        "item_total" => [
                            "currency_code" => "USD",
                            "value" => number_format($itemTotalValue, 2, '.', '') // Total amount of items
                        ]
                    ]
                ],
                "items" => $items
            ]
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$paypalUrl/v2/checkout/orders");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $accessToken"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));

    $response = curl_exec($ch);
    curl_close($ch);

    // Check if the response is empty
    if(empty($response)) {
        return null;
    } else {
        // Parse the JSON response
        $jsonData = json_decode($response);
        // Return the order ID and additional data
        $order = [
            "id" => $jsonData->id,
            "name" => $cart[0]["name"], // Assuming the cart has only one item
            "amount" => $cart[0]["amount"],
            "quantity" => $cart[0]["quantity"]
        ];

        return $order;

        // $query = "INSERT INTO orders (order_id, product_name, quantity, amount) VALUES ('" . $order["id"] . "', '" . $order["name"] . "', '" . $order["quantity"] . "', '" . $order["amount"] . "')";
        // $add_order = mysqli_query($connection,$query);
        // checkQuery($add_order);
        

        
    }
}

// Get the access token
$accessToken = getAccessToken($paypalUrl, $clientId, $clientSecret);
if($accessToken) {
    // Create the PayPal order and capture the order ID and additional data
    $orderData = createOrder($paypalUrl, $accessToken, $data["cart"]);
    if($orderData) {
        // Return the order data in the JSON response
        echo json_encode($orderData);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Unable to create order"]);
    }
} else {
    http_response_code(500);
    echo json_encode(["error" => "Unable to get access token"]);
}

