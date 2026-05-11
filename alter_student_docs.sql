USE smsdb;
ALTER TABLE student ADD COLUMN birth_certificate LONGTEXT AFTER board;
ALTER TABLE student ADD COLUMN marksheet LONGTEXT AFTER birth_certificate;
ALTER TABLE student ADD COLUMN aadhar_card LONGTEXT AFTER marksheet;