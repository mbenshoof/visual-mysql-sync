# Disable the FK Checks
SET foreign_key_checks = 0;

REPLACE INTO `percona`.`diff_bar`(`id`) VALUES ('3') /* was REPLACE... FROM `foo`.`bar` ... */ /*percona-toolkit src_db:foo src_tbl:bar src_dsn:F=/public_html/visual-mysql-sync/cli/conf/slave.my.cnf,P=3306,h=192.168.1.45 dst_db:foo dst_tbl:bar dst_dsn:F=/public_html/visual-mysql-sync/cli/conf/slave.my.cnf,h=192.168.1.46 lock:1 transaction:1 changing_src:percona.checksums replicate:percona.checksums bidirectional:0 pid:2037 user:root host:centos6-webdev*/;
REPLACE INTO `percona`.`diff_bar`(`id`) VALUES ('8') /* was REPLACE... FROM `foo`.`bar` ... */ /*percona-toolkit src_db:foo src_tbl:bar src_dsn:F=/public_html/visual-mysql-sync/cli/conf/slave.my.cnf,P=3306,h=192.168.1.45 dst_db:foo dst_tbl:bar dst_dsn:F=/public_html/visual-mysql-sync/cli/conf/slave.my.cnf,h=192.168.1.46 lock:1 transaction:1 changing_src:percona.checksums replicate:percona.checksums bidirectional:0 pid:2037 user:root host:centos6-webdev*/;
REPLACE INTO `percona`.`diff_bar`(`id`) VALUES ('12') /* was REPLACE... FROM `foo`.`bar` ... */ /*percona-toolkit src_db:foo src_tbl:bar src_dsn:F=/public_html/visual-mysql-sync/cli/conf/slave.my.cnf,P=3306,h=192.168.1.45 dst_db:foo dst_tbl:bar dst_dsn:F=/public_html/visual-mysql-sync/cli/conf/slave.my.cnf,h=192.168.1.46 lock:1 transaction:1 changing_src:percona.checksums replicate:percona.checksums bidirectional:0 pid:2037 user:root host:centos6-webdev*/;

# Re-enable the FK checks
SET foreign_key_checks = 1;

