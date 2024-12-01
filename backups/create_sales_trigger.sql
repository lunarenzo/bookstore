DELIMITER //

CREATE TRIGGER after_order_insert 
AFTER INSERT ON orders
FOR EACH ROW
BEGIN
    INSERT INTO sales_history (order_id, total_amount, transaction_date)
    VALUES (NEW.id, NEW.total_amount, NEW.created_at);
END//

DELIMITER ;
