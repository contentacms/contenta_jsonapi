#!/usr/bin/env bash

BASE_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/../.."

DEST_DIR="$BASE_DIR/../test_api_first"

if [ $1 ] ; then
  DEST_DIR="$1"
fi

sudo rm -Rf $DEST_DIR
composer create-project drupal/api_first_project ${DEST_DIR} --stability dev --no-interaction

cd ${DEST_DIR}
# ??
# composer config repositories.api_first path ${BASE_DIR}

# ??
# composer require "drupal/api-first:*" "phpunit/phpunit:~4.8" --no-progress
cd docroot
rm profiles/contrib/api-first/config/install/core.extension.yml
drush si api_first --db-url=mysql://root@localhost/loc_apifirst --account-pass=test -y


# ??
# echo "<?php use Drupal\Robo\RoboFileBase; class RoboFile extends RoboFileBase {}" > RoboFile.php
