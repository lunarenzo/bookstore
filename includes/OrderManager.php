<?php
class OrderManager {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function createOrder($userId, $cartItems, $total) {
        try {
            $this->conn->begin_transaction();

            // Create the order
            $query = "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("id", $userId, $total);
            $stmt->execute();
            $orderId = $this->conn->insert_id;

            // Add order items and update stock
            foreach ($cartItems as $item) {
                // Insert order item
                $query = "INSERT INTO order_items (order_id, book_id, quantity, price_per_unit) VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("iiid", $orderId, $item['id'], $item['quantity'], $item['price']);
                $stmt->execute();

                // Update stock
                $query = "UPDATE bookstore SET stock_available = stock_available - ? WHERE id = ? AND stock_available >= ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("iii", $item['quantity'], $item['id'], $item['quantity']);
                $result = $stmt->execute();

                if ($stmt->affected_rows === 0) {
                    throw new Exception("Insufficient stock for book ID: " . $item['id']);
                }
            }

            $this->conn->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function updateOrderStatus($orderId, $status, $transactionId = null) {
        $query = "UPDATE orders SET status = ?, transaction_id = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssi", $status, $transactionId, $orderId);
        return $stmt->execute();
    }

    public function getOrderDetails($orderId) {
        $query = "SELECT o.*, oi.*, b.title, b.author 
                 FROM orders o 
                 JOIN order_items oi ON o.id = oi.order_id 
                 JOIN bookstore b ON oi.book_id = b.id 
                 WHERE o.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        return $stmt->get_result();
    }
}