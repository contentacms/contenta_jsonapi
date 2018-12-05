#!/usr/bin/env bash

if [ -e .env ]; then
    export $(cat .env | xargs)
fi

if [ -e .env.local ]; then
    export $(cat .env.local | xargs)
fi

BASE_DIR="$(dirname $(dirname $(cd ${0%/*} && pwd)))"
COMPOSER_BIN_DIR="$(composer config bin-dir)"
DOCROOT="web"

# Define the color scheme.
FG_C='\033[1;37m'
BG_C='\033[42m'
WBG_C='\033[43m'
EBG_C='\033[41m'
NO_C='\033[0m'

if [ $1 ] ; then
  DEST_DIR="$1"
else
  DEST_DIR="$( dirname $BASE_DIR )/test_contenta_jsonapi"
fi

cd "$DEST_DIR/$DOCROOT"

echo -e "\n"
find $DEST_DIR -name drush
DRUSH="$DEST_DIR/$COMPOSER_BIN_DIR/drush"


echo "-------------------------------------"
echo " Initializing local PHP server "
echo "-------------------------------------"
echo -e "${FG_C}${WBG_C} INFO ${NO_C} Server started. Use Ctrl+C to stop it."
$DRUSH runserver --no-browser $SIMPLETEST_BASE_URL

echo "---------------------------------"
echo " One time admin login link "
echo "---------------------------------"
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} $DRUSH user-login --no-browser --uri=\"$SIMPLETEST_BASE_URL\""
echo -e "${FG_C}${WBG_C} INFO ${NO_C} Use this link to login as an administrator in your new site:"
$DRUSH user-login --no-browser --uri="$SIMPLETEST_BASE_URL"
