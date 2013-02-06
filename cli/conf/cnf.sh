#!/bin/bash

HOME_DIR="/public_html/visual-mysql-sync/cli"
MASTER_CONF="$HOME_DIR/conf/master.my.cnf"
SLAVE_CONF="$HOME_DIR/conf/slave.my.cnf"

MASTER_IP="192.168.1.45"
SLAVE_IP="192.168.1.46"

SRC_DB="foo"
DIFF_DB="percona"
TARGET_PRE="$DIFF_DB.diff_"