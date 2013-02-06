#!/bin/bash

# Include the base files
cd "$(dirname "$0")"
source ../conf/cnf.sh

CMD="$HOME_DIR/bin/pt-table-checksum --defaults-file=$MASTER_CONF --databases=$SRC_DB $MASTER_IP"
echo "[`date '+%F %H:%M:%S'`] Running: $CMD"
eval $CMD