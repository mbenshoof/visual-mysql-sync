#!/bin/bash

# Include the base files
cd "$(dirname "$0")"
source ../conf/cnf.sh

DIFF_DIR="$HOME_DIR/sql"
FK_OFF="$DIFF_DIR/_fk_off.sql"
FK_ON="$DIFF_DIR/_fk_on.sql"

LOAD_TABLE_BASE="\`percona\`.\`diff_"
ORIG_SCHEMA="$SRC_DB"

DEFAULTS_FILE="$SLAVE_CONF"
TABLE_LIST="$HOME_DIR/prep/tables-to-check.txt"
TABLES=`cat $TABLE_LIST`

echo "[`date '+%F %H:%M:%S'`] Starting table sync loop"

for TABLE in $TABLES; do

	echo "[`date '+%F %H:%M:%S'`] Starting sync process for [${TABLE}]"

	RAW_TABLE="${TABLE}-raw.sql"
	RELOAD_TABLE="${TABLE}-reload.sql"
	RAW_SQL="$DIFF_DIR/$RAW_TABLE"
	RELOAD_SQL="$DIFF_DIR/$RELOAD_TABLE"

	SYNC="$HOME_DIR/bin/pt-table-sync-replace --defaults-file=$DEFAULTS_FILE --print --replicate percona.checksums --tables=${TABLE} --sync-to-master $SLAVE_DSN > $RAW_SQL"

	COMBINE="cat $FK_OFF $RAW_SQL $FK_ON > $RELOAD_SQL"
	REPLACE="sed -i 's/REPLACE INTO \`$ORIG_SCHEMA\`.\`${TABLE}\`/REPLACE INTO $LOAD_TABLE_BASE${TABLE}\`/g' $RELOAD_SQL"

	echo "[`date '+%F %H:%M:%S'`] Running: $SYNC"
	eval $SYNC
	echo "[`date '+%F %H:%M:%S'`] Running: $COMBINE"
	eval $COMBINE
	echo "[`date '+%F %H:%M:%S'`] Running: $REPLACE"
	eval $REPLACE

	echo "[`date '+%F %H:%M:%S'`] SQL for [${TABLE}] ready in [$RELOAD_SQL]"
done

echo "[`date '+%F %H:%M:%S'`] Finished table sync loop"
