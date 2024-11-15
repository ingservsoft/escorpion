UPDATE `qc_sales_payments`
  JOIN `qc_sales` ON `qc_sales`.`sale_id`=`qc_sales_payments`.`sale_id`
  SET `qc_sales_payments`.`payment_time`=`qc_sales`.`sale_time`;
