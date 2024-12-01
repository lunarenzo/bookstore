<?php
require_once 'db.php';
require_once 'includes/OrderManager.php';
require_once 'config/paypal_config.php';

$orderManager = new OrderManager($conn);

// Read POST data
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();

foreach ($raw_post_array as $keyval) {
    $keyval = explode('=', $keyval);
    if (count($keyval) == 2) {
        $myPost[$keyval[0]] = urldecode($keyval[1]);
    }
}

// Verify IPN
$req = 'cmd=_notify-validate';
foreach ($myPost as $key => $value) {
    $value = urlencode($value);
    $req .= "&$key=$value";
}

$paypal_url = $config['sandbox_mode'] ? $config['sandbox_url'] : $config['live_url'];
$ch = curl_init($paypal_url);
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

$res = curl_exec($ch);

if (!$res) {
    $errno = curl_errno($ch);
    $errstr = curl_error($ch);
    curl_close($ch);
    exit;
}

curl_close($ch);

if (strcmp($res, "VERIFIED") == 0) {
    // Payment verified, update order status
    $payment_status = $_POST['payment_status'];
    $txn_id = $_POST['txn_id'];
    $custom = $_POST['custom']; // This should contain the order ID
    
    // Update order status in database
    $orderManager->updateOrderStatus($custom, $payment_status, $txn_id);
}
?>