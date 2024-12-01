-- Modify the status column to use ENUM with the four stages
ALTER TABLE `orders` 
MODIFY COLUMN `status` ENUM('Pending', 'Processing', 'Shipped', 'Delivered') NOT NULL DEFAULT 'Pending';

-- Update existing 'completed' orders to 'Delivered'
UPDATE `orders` SET `status` = 'Delivered' WHERE `status` = 'completed';
