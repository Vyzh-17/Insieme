<?php
require 'vendor/autoload.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_POST['amount'])) {
    echo json_encode(['error' => 'Amount not provided']);
    exit;
}

\Stripe\Stripe::setApiKey('sk_test_51RnjP3R1bqqQjQ0vo9pgcyWNvp4h5ukrrZspnyjqGSObOAUFCpfZpFBcwsi3Uni6wBQsJQ84jMtj4728vJEDHkgN00a3hzNaMs');

$amount = intval($_POST['amount']);

// ✅ Store the created session in $session
$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'inr',
            'product_data' => [
                'name' => 'Donation',
            ],
            'unit_amount' => $amount * 100, // in paise
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => 'http://localhost/insieme/fundraiser/success.php?amount=' . $amount,
    'cancel_url' => 'http://localhost/insieme/fundraiser/cancel.php',
]);

// ✅ Now $session->id exists
echo json_encode(['id' => $session->id]);
?>
