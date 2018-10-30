/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50724
Source Host           : localhost:3306
Source Database       : iris-test

Target Server Type    : MYSQL
Target Server Version : 50724
File Encoding         : 65001

Date: 2018-10-30 06:19:02
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for merchants
-- ----------------------------
DROP TABLE IF EXISTS `merchants`;
CREATE TABLE `merchants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mid` bigint(18) unsigned NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `merchants_mid_unique` (`mid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=203 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for transaction_batches
-- ----------------------------
DROP TABLE IF EXISTS `transaction_batches`;
CREATE TABLE `transaction_batches` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  `reference_number` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_batches_date_reference_number_unique` (`date`,`reference_number`) USING BTREE,
  KEY `transaction_batches_merchant_id_foreign` (`merchant_id`),
  CONSTRAINT `transaction_batches_merchant_id_foreign` FOREIGN KEY (`merchant_id`) REFERENCES `merchants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=628 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for transaction_card_types
-- ----------------------------
DROP TABLE IF EXISTS `transaction_card_types`;
CREATE TABLE `transaction_card_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_card_types_name_index` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for transaction_types
-- ----------------------------
DROP TABLE IF EXISTS `transaction_types`;
CREATE TABLE `transaction_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_types_name_index` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for transactions
-- ----------------------------
DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_batch_id` int(10) unsigned NOT NULL,
  `transaction_type_id` int(10) unsigned NOT NULL,
  `transaction_card_type_id` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  `card_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `transactions_transaction_batch_id_index` (`transaction_batch_id`),
  KEY `transactions_transaction_type_id_index` (`transaction_type_id`),
  KEY `transactions_transaction_card_type_id_index` (`transaction_card_type_id`),
  KEY `transactions_date_index` (`date`) USING BTREE,
  CONSTRAINT `transactions_transaction_batch_id_index` FOREIGN KEY (`transaction_batch_id`) REFERENCES `transaction_batches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_transaction_card_type_id_index` FOREIGN KEY (`transaction_card_type_id`) REFERENCES `transaction_card_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_transaction_type_id_index` FOREIGN KEY (`transaction_type_id`) REFERENCES `transaction_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3261 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SET FOREIGN_KEY_CHECKS=1;
