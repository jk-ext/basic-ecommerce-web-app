-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `yoke-main` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `yoke-main`;

-- Create login table
CREATE TABLE IF NOT EXISTS `login` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create orders table with proper foreign key
CREATE TABLE IF NOT EXISTS `orders` (
    `order_id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(100) NOT NULL,
    `product_name` VARCHAR(100) NOT NULL,
    `product_price` DECIMAL(10,2) NOT NULL,
    `quantity` INT NOT NULL,
    `status` ENUM('pending', 'completed') DEFAULT 'pending',
    `order_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`email`) REFERENCES `login`(`email`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add sample products if needed
INSERT IGNORE INTO `orders` (`email`, `product_name`, `product_price`, `quantity`, `status`) VALUES
('test@example.com', 'Wireless Headphones', 99.99, 1, 'pending'),
('test@example.com', 'Smart Watch', 199.99, 2, 'pending');
