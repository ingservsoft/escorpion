-- Improve payment tracking

RENAME TABLE qc_sales_payments TO qc_sales_payments_backup;

CREATE TABLE `qc_sales_payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(10) NOT NULL,
  `payment_type` varchar(40) NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  `payment_user` int(11) NOT NULL DEFAULT 0,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reference_code` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`payment_id`),
  KEY `payment_sale` (`sale_id`, `payment_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO qc_sales_payments (sale_id, payment_type, payment_amount, payment_user)
SELECT payments.sale_id, payments.payment_type, payments.payment_amount, sales.employee_id
FROM qc_sales_payments_backup AS payments
JOIN qc_sales AS sales ON payments.sale_id = sales.sale_id
ORDER BY payments.sale_id, payments.payment_type;

DROP TABLE IF EXISTS qc_sales_payments_backup;

ALTER TABLE `qc_sales_payments`
  ADD CONSTRAINT `qc_sales_payments_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `qc_sales` (`sale_id`);
