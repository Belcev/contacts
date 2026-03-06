#!/bin/bash
set -e

if [ ! -d "vendor" ]; then
    echo "Setup..."
    composer run-script setup
fi

exec "$@"
