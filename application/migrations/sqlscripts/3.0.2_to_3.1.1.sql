-- alter quantity fields to be all decimal

ALTER TABLE `qc_items`
  ADD COLUMN `stock_type` TINYINT(2) NOT NULL DEFAULT 0,
  ADD COLUMN `item_type` TINYINT(2) NOT NULL DEFAULT 0;

ALTER TABLE `qc_item_kits`
  ADD COLUMN `item_id` INT(10) NOT NULL DEFAULT 0,
  ADD COLUMN `kit_discount_percent` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  ADD COLUMN `price_option` TINYINT(2) NOT NULL DEFAULT 0,
  ADD COLUMN `print_option` TINYINT(2) NOT NULL DEFAULT 0;

ALTER TABLE `qc_item_kit_items`
  ADD COLUMN `kit_sequence` INT(3) NOT NULL DEFAULT 0;

ALTER TABLE `qc_sales_items`
  ADD COLUMN `print_option` TINYINT(2) NOT NULL DEFAULT 0;

ALTER TABLE `qc_sales_suspended`
  ADD COLUMN `quote_number` varchar(32) DEFAULT NULL AFTER `invoice_number`;

ALTER TABLE `qc_sales_suspended_items`
  ADD COLUMN `print_option` TINYINT(2) NOT NULL DEFAULT 0;

-- alter pic_id field, to rather contain a file name

ALTER TABLE `qc_items` CHANGE `pic_id` `pic_filename` VARCHAR(255);

--
-- Table structure for table `qc_dinner_tables`
--

CREATE TABLE IF NOT EXISTS `qc_dinner_tables` (
  `dinner_table_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`dinner_table_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `qc_dinner_tables` (`dinner_table_id`, `name`, `status`, `deleted`) VALUES
(1, 'Delivery', 0, 0),
(2, 'Take Away', 0, 0);

-- alter qc_sales table

ALTER TABLE `qc_sales`
  ADD COLUMN `dinner_table_id` int(11) NULL AFTER `invoice_number`;

ALTER TABLE `qc_sales`
  ADD KEY `dinner_table_id` (`dinner_table_id`),
  ADD CONSTRAINT `qc_sales_ibfk_3` FOREIGN KEY (`dinner_table_id`) REFERENCES `qc_dinner_tables` (`dinner_table_id`);

-- alter qc_sales_suspended table

ALTER TABLE `qc_sales_suspended`
  ADD COLUMN `dinner_table_id` int(11) NULL AFTER `quote_number`;

ALTER TABLE `qc_sales_suspended`
  ADD KEY `dinner_table_id` (`dinner_table_id`),
  ADD CONSTRAINT `qc_sales_suspended_ibfk_3` FOREIGN KEY (`dinner_table_id`) REFERENCES `qc_dinner_tables` (`dinner_table_id`);

-- add enabled dinner tables key into config

INSERT INTO `qc_app_config` (`key`, `value`) VALUES
('date_or_time_format', ''),
('sales_quote_format', 'Q%y{QSEQ:6}'),
('default_register_mode', 'sale'),
('last_used_invoice_number', '0'),
('last_used_quote_number', '0'),
('line_sequence', '0'),
('dinner_table_enable', '0'),
('customer_sales_tax_support', '0');

--
-- Table structure for table `qc_customer_packages`
--

CREATE TABLE IF NOT EXISTS `qc_customers_packages` (
  `package_id` int(11) NOT NULL AUTO_INCREMENT,
  `package_name` varchar(255) DEFAULT NULL,
  `points_percent` float NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `qc_customers_packages` (`package_id`, `package_name`, `points_percent`, `deleted`) VALUES
(1, 'Default', 0, 0),
(2, 'Bronze', 10, 0),
(3, 'Silver', 20, 0),
(4, 'Gold', 30, 0),
(5, 'Premium', 50, 0);

--
-- Table structure for table `qc_customer_points`
--

CREATE TABLE IF NOT EXISTS `qc_customers_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `points_earned` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Table structure for table `qc_sales_reward_points`
--

CREATE TABLE IF NOT EXISTS `qc_sales_reward_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `earned` float NOT NULL,
  `used` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- alter qc_customers table

ALTER TABLE qc_customers
  ADD COLUMN `package_id` int(11) DEFAULT NULL AFTER `discount_percent`,
  ADD COLUMN `points` int(11) DEFAULT NULL AFTER `package_id`;

-- add enabled reward points key into config

INSERT INTO `qc_app_config` (`key`, `value`) VALUES
('customer_reward_enable', '0');

--
-- The following changes are in support of customer sales tax changes
--

CREATE TABLE IF NOT EXISTS `qc_tax_codes` (
  `tax_code` varchar(32) NOT NULL,
  `tax_code_name` varchar(255) NOT NULL DEFAULT '',
  `tax_code_type` tinyint(2) NOT NULL DEFAULT 0,
  `city` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`tax_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `qc_tax_code_rates` (
  `rate_tax_code` varchar(32) NOT NULL,
  `rate_tax_category_id` int(10) NOT NULL,
  `tax_rate` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `rounding_code` tinyint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`rate_tax_code`,`rate_tax_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `qc_sales_taxes` (
  `sale_id` int(10) NOT NULL,
  `tax_type` smallint(2) NOT NULL,
  `tax_group` varchar(32) NOT NULL,
  `sale_tax_basis` decimal(15,4) NOT NULL,
  `sale_tax_amount` decimal(15,4) NOT NULL,
  `print_sequence` tinyint(2) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `tax_rate` decimal(15,4) NOT NULL,
  `sales_tax_code` varchar(32) NOT NULL DEFAULT '',
  `rounding_code` tinyint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`sale_id`,`tax_type`,`tax_group`),
  KEY `print_sequence` (`sale_id`,`print_sequence`,`tax_type`,`tax_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `qc_tax_categories` (
  `tax_category_id` int(10) NOT NULL,
  `tax_category` varchar(32) NOT NULL,
  `tax_group_sequence` tinyint(2) NOT NULL,
  PRIMARY KEY (`tax_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `qc_items`
  ADD COLUMN `tax_category_id` int(10) NOT NULL DEFAULT 0;

ALTER TABLE `qc_sales`
  ADD COLUMN `quote_number` varchar(32) DEFAULT NULL,
  ADD COLUMN `sale_status` tinyint(2) NOT NULL DEFAULT 0;

ALTER TABLE `qc_sales_items_taxes`
  MODIFY COLUMN `percent` decimal(15,4) NOT NULL DEFAULT 0.0000,
  ADD COLUMN `tax_type` tinyint(2) NOT NULL DEFAULT 0,
  ADD COLUMN `rounding_code` tinyint(2) NOT NULL DEFAULT 0,
  ADD COLUMN `cascade_tax` tinyint(2) NOT NULL DEFAULT 0,
  ADD COLUMN `cascade_sequence` tinyint(2) NOT NULL DEFAULT 0,
  ADD COLUMN `item_tax_amount` decimal(15,4) NOT NULL DEFAULT 0;

ALTER TABLE `qc_customers`
  ADD COLUMN `sales_tax_code` varchar(32) NOT NULL DEFAULT '1';

INSERT IGNORE INTO `qc_app_config` (`key`, `value`) VALUES
('customer_sales_tax_support', '0'),
('default_origin_tax_code', ''),
('default_tax_category', 'Standard'),
('default_tax_1_name', ''),
('default_tax_1_rate', ''),
('default_tax_2_name', ''),
('default_tax_2_rate', '');

INSERT IGNORE INTO `qc_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES
('module_taxes', 'module_taxes_desc', 105, 'taxes');

INSERT IGNORE INTO `qc_permissions` (`permission_id`, `module_id`) VALUES
('taxes', 'taxes');

-- add support for cash rounding into config

INSERT INTO `qc_app_config` (`key`, `value`) VALUES
('cash_decimals', '2'),
('cash_rounding_code', '0');

-- alter people table (create email index)

ALTER TABLE `qc_people`
  ADD INDEX `email` (`email`);

-- add financial year start into config

INSERT INTO `qc_app_config` (`key`, `value`) VALUES
('financial_year', '1');

-- alter giftcard field number to be varchar

ALTER TABLE `qc_giftcards` CHANGE `giftcard_number` `giftcard_number` VARCHAR(255) NULL;

-- add support for select between gitcard number series or random

INSERT INTO `qc_app_config` (`key`, `value`) VALUES
('giftcard_number', 'series');

-- add option to print company name in receipt

INSERT INTO `qc_app_config` (`key`, `value`) VALUES
('receipt_show_company_name', '1');

-- add support for sales tax history migration

INSERT INTO `qc_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES
('module_migrate', 'module_migrate_desc', 120, 'migrate');

INSERT INTO `qc_permissions` (`permission_id`, `module_id`) VALUES
('migrate', 'migrate');

INSERT INTO `qc_grants` (`permission_id`, `person_id`) VALUES
('migrate', 1);

-- update to receivings

UPDATE qc_items SET receiving_quantity = 1 WHERE receiving_quantity = 0;

-- long alternate description

ALTER TABLE `qc_sales_items`
  MODIFY COLUMN `description` varchar(255) DEFAULT NULL;

-- fix tax category maintenance

DELETE FROM `qc_tax_categories` where tax_category_id in (0, 1, 2, 3);

ALTER TABLE `qc_tax_categories`
  MODIFY COLUMN `tax_category_id` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `qc_items`
  MODIFY COLUMN `tax_category_id` int(10) DEFAULT NULL;

-- If you have added any tax codes, the following will correct the rate_tax_category_id on the tax_code_rates table,
-- but you might need to add more UPDATE statements depending on how may tax codes and/or tax categories you've added

UPDATE `qc_tax_code_rates` SET rate_tax_category_id = 4 WHERE rate_tax_category_id = 3;
UPDATE `qc_tax_code_rates` SET rate_tax_category_id = 3 WHERE rate_tax_category_id = 2;
UPDATE `qc_tax_code_rates` SET rate_tax_category_id = 2 WHERE rate_tax_category_id = 1;
UPDATE `qc_tax_code_rates` SET rate_tax_category_id = 1 WHERE rate_tax_category_id = 0;

-- add receipt font size

INSERT INTO `qc_app_config` (`key`, `value`) VALUES
('receipt_font_size', '12');

--
-- Add rewards foreign keys
--

ALTER TABLE `qc_customers_points`
  ADD KEY `person_id` (`person_id`),
  ADD KEY `package_id` (`package_id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD CONSTRAINT `qc_customers_points_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `qc_customers` (`person_id`),
  ADD CONSTRAINT `qc_customers_points_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `qc_customers_packages` (`package_id`),
  ADD CONSTRAINT `qc_customers_points_ibfk_3` FOREIGN KEY (`sale_id`) REFERENCES `qc_sales` (`sale_id`);

ALTER TABLE `qc_sales_reward_points`
  ADD KEY `sale_id` (`sale_id`),
  ADD CONSTRAINT `qc_sales_reward_points_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `qc_sales` (`sale_id`);

ALTER TABLE `qc_customers`
  ADD KEY `package_id` (`package_id`),
  ADD CONSTRAINT `qc_customers_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `qc_customers_packages` (`package_id`);

-- add reCAPTCHA configuration

INSERT INTO `qc_app_config` (`key`, `value`) VALUES
('gcaptcha_enable', '0'),
('gcaptcha_secret_key', ''),
('gcaptcha_site_key', '');

-- add Barcode formats and other missing keys

INSERT INTO `qc_app_config` (`key`, `value`) VALUES
('barcode_formats', '[]');

-- replace old tokens in qc_app_config

UPDATE `qc_app_config` SET `value` = REPLACE(`value`, '$CO', '{CO}');
UPDATE `qc_app_config` SET `value` = REPLACE(`value`, '$CU', '{CU}');
UPDATE `qc_app_config` SET `value` = REPLACE(`value`, '$INV', '{ISEQ}');
UPDATE `qc_app_config` SET `value` = REPLACE(`value`, '$SCO', '{SCO}');

--
-- Copy suspended sales to sales table
--

INSERT INTO `qc_sales` (sale_time, customer_id, employee_id, comment, invoice_number, sale_status)
  SELECT sale_time, customer_id, employee_id, comment, invoice_number, 1 FROM `qc_sales_suspended`;
INSERT INTO `qc_sales_items` (sale_id, item_id, description, serialnumber, line, quantity_purchased, item_cost_price, item_unit_price,
  discount_percent, item_location) SELECT sale_id, item_id, description, serialnumber, line, quantity_purchased, item_cost_price, item_unit_price,
  discount_percent, item_location FROM qc_sales_suspended_items;
INSERT INTO `qc_sales_payments` (sale_id, payment_type, payment_amount) SELECT sale_id, payment_type, payment_amount FROM `qc_sales_suspended_payments`;
INSERT INTO `qc_sales_items_taxes` (sale_id, item_id, line, name, percent) SELECT sale_id, item_id, line, name, percent FROM `qc_sales_suspended_items_taxes`;

ALTER TABLE `qc_sales_suspended_payments` DROP FOREIGN KEY `qc_sales_suspended_payments_ibfk_1`;

ALTER TABLE `qc_sales_suspended_items_taxes` DROP FOREIGN KEY `qc_sales_suspended_items_taxes_ibfk_1`;
ALTER TABLE `qc_sales_suspended_items_taxes` DROP FOREIGN KEY `qc_sales_suspended_items_taxes_ibfk_2`;

ALTER TABLE `qc_sales_suspended_items` DROP FOREIGN KEY `qc_sales_suspended_items_ibfk_1`;
ALTER TABLE `qc_sales_suspended_items` DROP FOREIGN KEY `qc_sales_suspended_items_ibfk_2`;
ALTER TABLE `qc_sales_suspended_items` DROP FOREIGN KEY `qc_sales_suspended_items_ibfk_3`;

ALTER TABLE `qc_sales_suspended` DROP FOREIGN KEY `qc_sales_suspended_ibfk_1`;
ALTER TABLE `qc_sales_suspended` DROP FOREIGN KEY `qc_sales_suspended_ibfk_2`;
ALTER TABLE `qc_sales_suspended` DROP FOREIGN KEY `qc_sales_suspended_ibfk_3`;

DROP TABLE `qc_sales_suspended_payments`, `qc_sales_suspended_items_taxes`, `qc_sales_suspended_items`, `qc_sales_suspended`;

--
-- General fixing to realign upgraded database to clean database structure
--

DELETE FROM `qc_app_config` WHERE `key` = 'print_after_sale';

ALTER TABLE qc_giftcards MODIFY value decimal(15,2) NOT NULL;
ALTER TABLE qc_items MODIFY cost_price decimal(15,2) NOT NULL;
ALTER TABLE qc_items MODIFY unit_price decimal(15,2) NOT NULL;
ALTER TABLE qc_receivings_items MODIFY discount_percent decimal(15,2) NOT NULL DEFAULT '0.00';
ALTER TABLE qc_receivings_items MODIFY item_unit_price decimal(15,2) NOT NULL;
ALTER TABLE qc_sales_items MODIFY discount_percent decimal(15,2) NOT NULL DEFAULT '0.00';
ALTER TABLE qc_sales_items MODIFY item_unit_price decimal(15,2) NOT NULL;

-- Change collation on columns to be utf8_general_ci

ALTER TABLE qc_app_config CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_customers CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_customers_packages CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_customers_points CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_dinner_tables CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_employees CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_giftcards CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_grants CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_inventory CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_item_kit_items CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_item_kits CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_item_quantities CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_items CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_items_taxes CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_modules CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_people CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_permissions CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_receivings CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_receivings_items CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_sales CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_sales_items CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_sales_items_taxes CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_sales_payments CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_sales_reward_points CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_sales_taxes CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_sessions CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_stock_locations CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_suppliers CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_tax_categories CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_tax_code_rates CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE qc_tax_codes CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

--
-- Increase acceptable length of custom fields and allow null values
--

ALTER TABLE qc_items
  MODIFY custom1 VARCHAR(255) DEFAULT NULL,
  MODIFY custom2 VARCHAR(255) DEFAULT NULL,
  MODIFY custom3 VARCHAR(255) DEFAULT NULL,
  MODIFY custom4 VARCHAR(255) DEFAULT NULL,
  MODIFY custom5 VARCHAR(255) DEFAULT NULL,
  MODIFY custom6 VARCHAR(255) DEFAULT NULL,
  MODIFY custom7 VARCHAR(255) DEFAULT NULL,
  MODIFY custom8 VARCHAR(255) DEFAULT NULL,
  MODIFY custom9 VARCHAR(255) DEFAULT NULL,
  MODIFY custom10 VARCHAR(255) DEFAULT NULL;

-- Change language code en to be en-US

UPDATE `qc_app_config` SET `value` = 'en-US' WHERE `key` = 'language_code' AND `value` = 'en';

