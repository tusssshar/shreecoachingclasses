USE smsdb;
ALTER TABLE student ADD COLUMN first_name LONGTEXT AFTER student_id;
ALTER TABLE student ADD COLUMN middle_name LONGTEXT AFTER first_name;
ALTER TABLE student ADD COLUMN last_name LONGTEXT AFTER middle_name;
ALTER TABLE student ADD COLUMN fmobile LONGTEXT AFTER phone;
ALTER TABLE student ADD COLUMN standard LONGTEXT AFTER class_id;
ALTER TABLE student ADD COLUMN medium LONGTEXT AFTER standard;
ALTER TABLE student ADD COLUMN board LONGTEXT AFTER medium;