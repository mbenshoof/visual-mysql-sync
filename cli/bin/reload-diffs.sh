#!/bin/bash

# Include the base files
cd "$(dirname "$0")"
source ../conf/cnf.sh

DIFF_DIR="$HOME_DIR/sql"
DEFAULTS_FILE="$SLAVE_CONF"

TABLE_LIST="$HOME_DIR/prep/tables-to-check.txt"
TABLES=`cat $TABLE_LIST`

echo "[`date '+%F %H:%M:%S'`] Starting table restore loop"

for TABLE in $TABLES; do

	echo "[`date '+%F %H:%M:%S'`] Starting load process for [${TABLE}]"

	RELOAD_TABLE="${TABLE}-reload.sql"
	RELOAD_SQL="$DIFF_DIR/$RELOAD_TABLE"

	mysql --defaults-file=$DEFAULTS_FILE percona < $RELOAD_SQL
	echo "[`date '+%F %H:%M:%S'`] SQL for [${TABLE}] loaded"

done

echo "[`date '+%F %H:%M:%S'`] Finished table restore loop"