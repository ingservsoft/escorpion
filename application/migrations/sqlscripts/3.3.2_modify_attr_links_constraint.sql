ALTER TABLE `qc_attribute_links`
DROP FOREIGN KEY `qc_attribute_links_ibfk_4`;

ALTER TABLE `qc_attribute_links`
ADD CONSTRAINT `qc_attribute_links_ibfk_4`
FOREIGN KEY (`receiving_id`) REFERENCES `qc_receivings`(`receiving_id`)
ON DELETE CASCADE
ON UPDATE RESTRICT;