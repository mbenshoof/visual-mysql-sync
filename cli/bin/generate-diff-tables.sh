#!/bin/bash

# Include the base files
cd "$(dirname "$0")"
source ../conf/cnf.sh

DEFAULTS_FILE="$SLAVE_CONF"

TABLE_LIST="$HOME_DIR/prep/tables-to-check.txt"
OLD_TABLE_LIST="$HOME_DIR/prep/old-diff-tables.txt"

CREATE_SQL="$HOME_DIR/prep/diff-tables.sql"
DROP_SQL="$HOME_DIR/prep/drop-diff-tables.sql"

# Clean up all the old files
cat /dev/null > $CREATE_SQL
cat /dev/null > $DROP_SQL


# Get a list of all the existing drop_% tables in the percona schema for cleanup
mysql --defaults-file=$DEFAULTS_FILE -r -s -N -e "SHOW TABLES LIKE 'diff%'" $DIFF_DB > $OLD_TABLE_LIST

OLD_TABLES=`cat $OLD_TABLE_LIST`
for TABLE in $OLD_TABLES; do
	echo "DROP TABLE percona.${TABLE};" >> $DROP_SQL
done

echo "[`date '+%F %H:%M:%S'`] Determined old tables to clean up"

mysql --defaults-file=$DEFAULTS_FILE -r -s -N -e"SELECT DISTINCT(tbl) FROM percona.checksums WHERE master_crc <> this_crc OR master_cnt <> this_cnt;" > $TABLE_LIST
echo "[`date '+%F %H:%M:%S'`] Determined tables with rows out of sync"

# Loop through the diff tables to generate the list of new tables
TABLES=`cat $TABLE_LIST`
for TABLE in $TABLES; do	
	echo "CREATE TABLE ${TARGET_PRE}${TABLE} LIKE ${SRC_DB}.${TABLE};" >> $CREATE_SQL

	COL_LIST=`mysql --defaults-file=$DEFAULTS_FILE -r -s -N -e"SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$SRC_DB' AND TABLE_NAME = '${TABLE}' AND COLUMN_KEY <> 'PRI'"`
	BASE_DROP="ALTER TABLE ${TARGET_PRE}${TABLE}"
	I=

	for COL in $COL_LIST; do
		if [ ! "$I" ]
		then 
			BASE_DROP="$BASE_DROP DROP COLUMN \`${COL}\`"
			I=1
		else
			BASE_DROP="$BASE_DROP, DROP COLUMN \`${COL}\`"
		fi
	done
	echo "$BASE_DROP;" >> $CREATE_SQL
done

echo "[`date '+%F %H:%M:%S'`] Completed SQL to drop non-PK columns"

mysql --defaults-file=$DEFAULTS_FILE percona < $DROP_SQL
mysql --defaults-file=$DEFAULTS_FILE percona < $CREATE_SQL