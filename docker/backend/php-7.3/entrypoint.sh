#!/usr/bin/env bash

set -e

if [[ "$FROM_SCRATCH" = "Y" ]]; then
    composer install
    console database:healthcheck --retries 10
    composer from-scratch
fi

/usr/local/sbin/php-fpm
