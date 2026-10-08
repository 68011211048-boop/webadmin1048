CREATE DATABASE IF NOT EXISTS `pet_lab_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pet_lab_db`;

DROP TABLE IF EXISTS `pets`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `pet_name` VARCHAR(100) NOT NULL,
    `photo` VARCHAR(255) DEFAULT NULL,
    `owner_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`owner_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- รหัสผ่านของทุกบัญชีคือ: 1234
INSERT INTO `users` (`username`, `password_hash`, `full_name`, `role`) VALUES
('admin', '$2b$12$ZsoG7cpijOyR38n4FRAaTOseBAE.uraVXaG.ppy2oU0G9CYmfGMw2', 'ผู้ดูแลระบบ', 'admin'),
('kanya', '$2b$12$ZsoG7cpijOyR38n4FRAaTOseBAE.uraVXaG.ppy2oU0G9CYmfGMw2', 'กัญญา มีสุข', 'user'),
('chai', '$2b$12$ZsoG7cpijOyR38n4FRAaTOseBAE.uraVXaG.ppy2oU0G9CYmfGMw2', 'ชาญชัย ชัยชนะ', 'user');