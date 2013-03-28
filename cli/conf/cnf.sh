#!/bin/bash

HOME_DIR="/public_html/visual-mysql-sync/cli"
MASTER_CONF="$HOME_DIR/conf/master.my.cnf"
SLAVE_CONF="$HOME_DIR/conf/slave.my.cnf"

MASTER_IP="192.168.57.15"
MASTER_DSN="h=192.168.57.15,P=3316"
SLAVE_IP="192.168.57.15"
SLAVE_DSN="h=192.168.57.15,P=3326"

SRC_DB="main_db"
DIFF_DB="percona"
TARGET_PRE="$DIFF_DB.diff_"
