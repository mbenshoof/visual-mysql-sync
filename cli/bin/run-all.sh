#!/bin/bash

# Include the base files
cd "$(dirname "$0")"

./generate-checksums.sh
./generate-diff-tables.sh
./find-diffs.sh
./reload-diffs.sh