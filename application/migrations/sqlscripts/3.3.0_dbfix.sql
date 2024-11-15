ALTER TABLE `qc_sales_payments` ADD INDEX `employee_id` (`employee_id`);

ALTER TABLE `qc_sales_payments`
  ADD CONSTRAINT `qc_sales_payments_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `qc_employees` (`person_id`);

ALTER TABLE `qc_customers` ADD INDEX `sales_tax_code_id` (`sales_tax_code_id`);

ALTER TABLE `qc_customers`
  ADD CONSTRAINT `qc_customers_ibfk_3` FOREIGN KEY (`sales_tax_code_id`) REFERENCES `qc_tax_codes` (`tax_code_id`);

ALTER TABLE `qc_tax_rates` ADD INDEX `rate_tax_category_id` (`rate_tax_category_id`);

ALTER TABLE `qc_tax_rates`
  ADD CONSTRAINT `qc_tax_rates_ibfk_1` FOREIGN KEY (`rate_tax_category_id`) REFERENCES `qc_tax_categories` (`tax_category_id`);

ALTER TABLE `qc_tax_rates` ADD INDEX `rate_tax_code_id` (`rate_tax_code_id`);

ALTER TABLE `qc_tax_rates`
  ADD CONSTRAINT `qc_tax_rates_ibfk_2` FOREIGN KEY (`rate_tax_code_id`) REFERENCES `qc_tax_codes` (`tax_code_id`);

ALTER TABLE `qc_tax_rates` ADD INDEX `rate_jurisdiction_id` (`rate_jurisdiction_id`);

ALTER TABLE `qc_tax_rates`
  ADD CONSTRAINT `qc_tax_rates_ibfk_3` FOREIGN KEY (`rate_jurisdiction_id`) REFERENCES `qc_tax_jurisdictions` (`jurisdiction_id`);

ALTER TABLE `qc_receivings` ADD INDEX `receiving_time` (`receiving_time`);

ALTER TABLE `qc_sales_payments` ADD INDEX `payment_time` (`payment_time`);

ALTER TABLE `qc_inventory` ADD INDEX `trans_date` (`trans_date`);

ALTER TABLE `qc_expenses` ADD INDEX `date` (`date`);
