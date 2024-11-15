UPDATE `qc_suppliers`
  SET `tax_id` = ''
  WHERE `tax_id` IS NULL;

ALTER TABLE `qc_suppliers`
  CHANGE COLUMN `tax_id` `tax_id` varchar(32) NOT NULL DEFAULT '';
