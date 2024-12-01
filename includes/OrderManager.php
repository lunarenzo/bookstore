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
            $query = "INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (?, ?, 'pending', NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("id", $userId, $total);
            $stmt->execute();
            $orderId = $this->conn->insert_id;

            // Add order items
            $query = "INSERT INTO order_items (order_id, book_id, quantity, price_per_unit) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);

            foreach ($cartItems as $item) {
                $stmt->bind_param("iiid", $orderId, $item['id'], $item['quantity'], $item['price']);
                $stmt->execute();
            }

            $this->conn->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function updateOrderStatus($orderId, $status) {
        $query = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $orderId);
        return $stmt->execute();
    }

    public function getOrder($orderId) {
        $query = "SELECT * FROM orders WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getOrderItems($orderId) {
        $query = "SELECT oi.*, b.title, b.author, b.book_cover 
                 FROM order_items oi 
                 JOIN bookstore b ON oi.book_id = b.id 
                 WHERE oi.order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>