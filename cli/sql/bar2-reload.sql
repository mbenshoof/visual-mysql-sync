# Disable the FK Checks
SET foreign_key_checks = 0;

REPLACE INTO `percona`.`diff_bar2`(`id`) VALUES ('8') /* was REPLACE... FROM `foo`.`bar2` ... */ /*percona-toolkit src_db:foo src_tbl:bar2 src_dsn:F=/public_html/visual-mysql-sync/cli/conf/slave.my.cnf,P=3306,h=192.168.1.45 dst_db:foo dst_tbl:bar2 dst_dsn:F=/public_html/visual-mysql-sync/cli/conf/slave.my.cnf,h=192.168.1.46 lock:1 transaction:1 changing_src:percona.checksums replicate:percona.checksums bidirectional:0 pid:2046 user:root host:centos6-webdev*/;
REPLACE INTO `percona`.`diff_bar2`(`id`) VALUES ('12') /* was REPLACE... FROM `foo`.`bar2` ... */ /*percona-toolkit src_db:foo src_tbl:bar2 src_dsn:F=/public_html/visual-mysql-sync/cli/conf/slave.my.cnf,P=3306,h=192.168.1.45 dst_db:foo dst_tbl:bar2 dst_dsn:F=/public_html/visual-mysql-sync/cli/conf/slave.my.cnf,h=192.168.1.46 lock:1 transaction:1 changing_src:percona.checksums replicate:percona.checksums bidirectional:0 pid:2046 user:root host:centos6-webdev*/;

# Re-enable the FK checks
SET foreign_key_checks = 1;

