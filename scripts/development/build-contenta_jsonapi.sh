#!/usr/bin/env bash
# Move up 3 levels since we are in contenta_jsonapi/script/development.
BASE_DIR="$(dirname $(dirname $(cd ${0%/*} && pwd)))"

COMPOSER="$(which composer)"
DOCROOT="web"

# Define the color scheme.
FG_C='\033[1;37m'
BG_C='\033[42m'
WBG_C='\033[43m'
EBG_C='\033[41m'
NO_C='\033[0m'

echo -e "\n"
if [ $1 ] ; then
  DEST_DIR="$1"
  echo $1
else
  DEST_DIR="$( dirname $BASE_DIR )/test_contenta_jsonapi"
  echo -e "${FG_C}${WBG_C} WARNING ${NO_C} No installation path provided.\nContenta will be installed in $DEST_DIR."
  echo -e "${FG_C}${BG_C} USAGE ${NO_C} ${0} [install_path] # to install in a different directory."
fi
DRUSH="$DEST_DIR/bin/drush"

echo -e "\n\n\n"
echo -e "\t********************************"
echo -e "\t*   Installing Contenta CMS    *"
echo -e "\t********************************"
echo -e "\n\n\n"
echo -e "Installing to: $DEST_DIR\n"

if [ -d "$DEST_DIR" ]; then
  echo -e "${FG_C}${WBG_C} WARNING ${NO_C} You are about to delete $DEST_DIR to install Contenta CMS in that location."
  rm -Rf $DEST_DIR
  if [ $? -ne 0 ]; then
    echo -e "${FG_C}${EBG_C} ERROR ${NO_C} Sometimes drush adds some files with permissions that are not deletable by the current user."
    echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} sudo rm -Rf $DEST_DIR"
    sudo rm -Rf $DEST_DIR
  fi
fi
echo "-----------------------------------------------"
echo " Downloading Contenta CMS using composer "
echo "-----------------------------------------------"
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} $COMPOSER create-project contentacms/contenta-jsonapi-project ${DEST_DIR} --stability dev --no-interaction\n\n"
$COMPOSER create-project contentacms/contenta-jsonapi-project ${DEST_DIR} --stability dev --no-interaction --no-install
if [ $? -ne 0 ]; then
  echo -e "${FG_C}${EBG_C} ERROR ${NO_C} There was a problem setting up Contenta CMS using composer."
  echo "Please check your composer configuration and try again."
  exit 2
fi

cd ${DEST_DIR}

$COMPOSER config repositories.contenta_jsonapi path ${BASE_DIR}

$COMPOSER require "contentacms/contenta_jsonapi:*" "phpunit/phpunit:~5.7" --no-progress

cd $DOCROOT
echo "-----------------------------------------------"
echo " Installing Contenta CMS for local usage "
echo "-----------------------------------------------"
echo -e "${FG_C}${BG_C} EXECUTING ${NO_C} $DRUSH si contenta_jsonapi --db-url=sqlite://sites/default/files/.ht.sqlite --account-pass=test -y\n\n"
$DRUSH si contenta_jsonapi --db-url=sqlite://sites/default/files/.ht.sqlite --account-pass=test -y
if [ $? -ne 0 ]; then
  echo -e "${FG_C}${EBG_C} ERROR ${NO_C} The Drupal installer failed to install Contenta CMS."
  exit 3
fi

echo -e "\n\n\n"
echo -e "\t********************************"
echo -e "\t*    Installation finished     *"
echo -e "\t********************************"
echo -e "\n\n\n"
