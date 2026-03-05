CREATE DATABASE IF NOT EXISTS `pure_view` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `pure_view`;

SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` int DEFAULT NULL,
  `cash_balance` int DEFAULT '0',
  `status` int DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `pass_reset` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  `deletedAt` datetime DEFAULT NULL,
  `country_id` int DEFAULT NULL,
  `linking_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `games` (
  `id` int NOT NULL AUTO_INCREMENT,
  `winner_id` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bet_amount` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `game_play` json DEFAULT NULL,
  `status` int DEFAULT '1',
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  `deletedAt` datetime DEFAULT NULL,
  `table_game` int DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `game_emojis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `status` int DEFAULT '1',
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  `deletedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `game_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `game_id` int DEFAULT NULL,
  `bet_amount` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `game_play` json DEFAULT NULL,
  `peer_id` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` int DEFAULT '1',
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  `deletedAt` datetime DEFAULT NULL,
  `player_no` int DEFAULT NULL,
  `withdraw_amount` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sitting_out` int DEFAULT '0',
  `missed_smallblind` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `game_id` (`game_id`),
  CONSTRAINT `game_users_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `game_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `game_id` int DEFAULT NULL,
  `winner_id` int DEFAULT NULL,
  `game_data` json DEFAULT NULL,
  `status` int DEFAULT '1',
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  `deletedAt` datetime DEFAULT NULL,
  `win_amount` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `game_chats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `game_id` int DEFAULT NULL,
  `from_user_id` int DEFAULT NULL,
  `from_id` int DEFAULT NULL,
  `from_player_no` int DEFAULT NULL,
  `from_type` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `message` text,
  `emoji_id` int DEFAULT NULL,
  `status` int DEFAULT '1',
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  `deletedAt` datetime DEFAULT NULL,
  `game_user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `game_id` (`game_id`),
  KEY `from_id` (`from_id`),
  KEY `emoji_id` (`emoji_id`),
  KEY `game_user_id` (`game_user_id`),
  CONSTRAINT `game_chats_ibfk_69` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `game_chats_ibfk_70` FOREIGN KEY (`from_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `game_chats_ibfk_71` FOREIGN KEY (`emoji_id`) REFERENCES `game_emojis` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `game_chats_ibfk_72` FOREIGN KEY (`game_user_id`) REFERENCES `game_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS=1;
