#!/bin/bash

# Include the base files
cd "$(dirname "$0")"
source ../conf/cnf.sh

DEFAULTS_FILE="$MASTER_CONF"

# Get a list of all the existing drop_% tables in the percona schema for cleanup
mysql --defaults-file=$DEFAULTS_FILE -r -s -N -e "TRUNCATE TABLE checksums" $DIFF_DB

CMD="$HOME_DIR/bin/pt-table-checksum --defaults-file=$MASTER_CONF --set-vars=\"time_zone='+00:00'\" --databases=$SRC_DB $MASTER_DSN"
echo "[`date '+%F %H:%M:%S'`] Running: $CMD"
eval $CMD
