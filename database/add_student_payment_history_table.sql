-- Dedicated student payment history table for the SMS application.
-- Run this file once to add the table to an existing database.

CREATE TABLE IF NOT EXISTS `student_payment_history` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `title` longtext COLLATE utf8_unicode_ci NOT NULL,
  `payment_type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `method` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
