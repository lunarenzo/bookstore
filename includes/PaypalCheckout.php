<?php
class PaypalCheckout {
    private $config;
    private $conn;

    public function __construct($db_connection) {
        $this->config = require_once 'config/paypal_config.php';
        $this->conn = $db_connection;
    }

    public function generatePaymentForm($cart_items, $total) {
        $paypal_url = $this->config['sandbox_mode'] ? $this->config['sandbox_url'] : $this->config['live_url'];
        
        $form = '<form action="' . $paypal_url . '" method="post" id="paypal_form">';
        $form .= '<input type="hidden" name="cmd" value="_cart">';
        $form .= '<input type="hidden" name="upload" value="1">';
        $form .= '<input type="hidden" name="business" value="' . $this->config['business_email'] . '">';
        $form .= '<input type="hidden" name="currency_code" value="' . $this->config['currency'] . '">';
        $form .= '<input type="hidden" name="return" value="' . $this->config['return_url'] . '">';
        $form .= '<input type="hidden" name="cancel_return" value="' . $this->config['cancel_url'] . '">';
        $form .= '<input type="hidden" name="notify_url" value="' . $this->config['notify_url'] . '">';

        // Add cart items
        $i = 1;
        foreach ($cart_items as $item) {
            $form .= '<input type="hidden" name="item_name_' . $i . '" value="' . htmlspecialchars($item['title']) . '">';
            $form .= '<input type="hidden" name="amount_' . $i . '" value="' . $item['price'] . '">';
            $form .= '<input type="hidden" name="quantity_' . $i . '" value="' . $item['quantity'] . '">';
            $i++;
        }

        $form .= '<button type="submit" class="paypal-button">Pay with PayPal</button>';
        $form .= '</form>';

        return $form;
    }

    public function createOrder($user_id, $total) {
        $query = "INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (?, ?, 'pending', NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("id", $user_id, $total);
        return $stmt->execute() ? $this->conn->insert_id : false;
    }
}
?>