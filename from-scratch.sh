#!/usr/bin/env bash

hr () {
  echo '--------------------------------------';
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

    hr && echo "We will need a Steam API key to proceed: " && hr
    read STEAM_API_KEY
    sed -i "s|STEAM_API_KEY=|STEAM_API_KEY=$STEAM_API_KEY|g" .env.local
fi

hr && echo "docker-compose finally..." && hr

FROM_SCRATCH=Y ./up
