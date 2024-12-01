<?php
class StockManager {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function reduceStock($order_id) {
        try {
            $this->conn->begin_transaction();

            // Get order items
            $query = "SELECT book_id, quantity FROM order_items WHERE order_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // Update stock for each item
            foreach ($items as $item) {
                $query = "UPDATE bookstore SET stock_available = stock_available - ? 
                         WHERE id = ? AND stock_available >= ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("iii", $item['quantity'], $item['book_id'], $item['quantity']);
                $result = $stmt->execute();

                if ($stmt->affected_rows === 0) {
                    throw new Exception("Insufficient stock for book ID: " . $item['book_id']);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function checkStockAvailability($cart_items) {
        $query = "SELECT id, stock_available FROM bookstore WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        foreach ($cart_items as $item) {
            $stmt->bind_param("i", $item['id']);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if (!$result || $result['stock_available'] < $item['quantity']) {
                return false;
            }
        }

        return true;
    }

    public function getStockLevel($book_id) {
        $query = "SELECT stock_available FROM bookstore WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['stock_available'] : 0;
    }
}
?>
