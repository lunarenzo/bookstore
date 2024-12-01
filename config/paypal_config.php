<?php
// PayPal Configuration
return [
    'sandbox_mode' => true, // Set to false for production
    'business_email' => 'sb-smdtu34491493@business.example.com', // Your PayPal business email
    'return_url' => 'http://localhost/bookstore/payment_success.php',
    'cancel_url' => 'http://localhost/bookstore/payment_cancel.php',
    'currency' => 'USD',
    'sandbox_url' => 'https://www.sandbox.paypal.com/cgi-bin/webscr',
    'live_url' => 'https://www.paypal.com/cgi-bin/webscr'
];
?>
