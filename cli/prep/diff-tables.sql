CREATE TABLE percona.diff_bar LIKE foo.bar;
ALTER TABLE percona.diff_bar DROP COLUMN `test`, DROP COLUMN `data`, DROP COLUMN `date_created`;
CREATE TABLE percona.diff_bar2 LIKE foo.bar2;
ALTER TABLE percona.diff_bar2 DROP COLUMN `test`, DROP COLUMN `data`, DROP COLUMN `date_created`;
