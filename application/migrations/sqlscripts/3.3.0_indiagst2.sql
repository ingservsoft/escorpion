-- This is to cleanup any orphaned tax migration tables

DROP TABLE IF EXISTS `qc_tax_codes_backup`;
DROP TABLE IF EXISTS `qc_sales_taxes_backup`;
DROP TABLE IF EXISTS `qc_tax_code_rates_backup`;