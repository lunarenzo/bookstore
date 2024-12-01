-- Create sales_history table
CREATE TABLE IF NOT EXISTS `sales_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copy existing orders data to sales_history
INSERT INTO sales_history (order_id, total_amount, transaction_date)
SELECT id, total_amount, created_at
FROM orders;

-- Create trigger for new orders
DELIMITER //

CREATE TRIGGER after_order_insert 
AFTER INSERT ON orders
FOR EACH ROW
BEGIN
    INSERT INTO sales_history (order_id, total_amount, transaction_date)
    VALUES (NEW.id, NEW.total_amount, NEW.created_at);
END//

DELIMITER ;
