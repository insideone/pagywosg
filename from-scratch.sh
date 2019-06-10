#!/usr/bin/env bash

set -e

hr () {
  HR='======================================'
  if [[ "$#" -gt 0 ]]; then
    echo "/$HR\\";
    echo "   $@"
    echo "\\$HR/";
  else
    echo "$HR";
  fi
}

hr
echo "WARNING! These actions will purge all user data. Also, this command will block your console"
echo "Use Ctrl+Z to exit (also it will stop all the containers if they're Up)"
echo "Press <Enter> to continue"
hr
read CONTINUE

if [[ -f .env.local ]]; then
    hr
    echo "We are going to generate .env.local file, but it's already exists"
    echo "Press <Enter> to rewrite or <N><Enter> to keep"
    hr
    read REWRITE
else
    cp ./.env ./.env.local
fi

REWRITE=${REWRITE^^}
REWRITE=${REWRITE:-Y}

# stop docker if it runs
docker-compose rm --stop --force -v
docker-compose build

# remove all generated stuff what can exists
rm -rf ./vendor ./node_modules ./public/build ./var

if [[ "$REWRITE" =  "Y" ]]; then
    # .env.local template
    cat ./.env | grep --regex '=$' > ./.env.local

    hr
    echo "What is your time zone?"
    echo "Type value from https://www.w3schools.com/php/php_ref_timezones.asp or ENTER to use Europe/London"
    hr
    read TZ
    TZ=${TZ:-Europe/London}
    sed -i "s|TZ=|TZ=$TZ|g" .env.local

    hr "We will need a Steam API key to proceed: "
    read STEAM_API_KEY
    sed -i "s|STEAM_API_KEY=|STEAM_API_KEY=$STEAM_API_KEY|g" .env.local
fi


HTTPS_CERTIFICATE_DIRECTORY=./env/https
HTTPS_CERTIFICATE_KEY_FILE="$HTTPS_CERTIFICATE_DIRECTORY/key.pem"
HTTPS_CERTIFICATE_CERT_FILE="$HTTPS_CERTIFICATE_DIRECTORY/cert.pem"
if [[ -f "$HTTPS_CERTIFICATE_KEY_FILE" ]]; then
    hr
    echo "Should we rewrite existed https certificate?"
    echo "Press <Enter> to keep or <Y><Enter> to rewrite"
    hr
    read WRITE_CERTIFICATE
else
    WRITE_CERTIFICATE=Y
fi

WRITE_CERTIFICATE=${WRITE_CERTIFICATE:-N}

if [[ ! -d "$HTTPS_CERTIFICATE_DIRECTORY" ]]; then
    mkdir "$HTTPS_CERTIFICATE_DIRECTORY"
fi

if [[ "$WRITE_CERTIFICATE" = "Y" ]]; then
    hr "Generating https certificate"
    openssl req -subj '/CN=localhost' -x509 -newkey rsa:4096 -nodes -keyout "$HTTPS_CERTIFICATE_KEY_FILE" -out "$HTTPS_CERTIFICATE_CERT_FILE" -days 365
fi

hr "docker-compose finally..."

FROM_SCRATCH=Y ./up
