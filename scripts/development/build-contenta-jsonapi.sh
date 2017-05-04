#!/usr/bin/env bash

BASE_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/../.."

DEST_DIR="$BASE_DIR/../test_contenta_jsonapi"

if [ $1 ] ; then
  DEST_DIR="$1"
fi

sudo rm -Rf $DEST_DIR
composer create-project drupal-http-apis/contenta-jsonapi-project ${DEST_DIR} --stability dev --no-interaction

cd ${DEST_DIR}
# ??
composer config repositories.contenta_jsonapi path ${BASE_DIR}

# ??
# composer require "drupal-http-apis/contenta-jsonapi:*" "phpunit/phpunit:~4.8" --no-progress
cd docroot
drush si contenta_jsonapi --db-url=sqlite://sites/default/files/.ht.sqlite --account-pass=test -y

../bin/drush rs & 
../bin/drush uli

# ??
# echo "<?php use Drupal\Robo\RoboFileBase; class RoboFile extends RoboFileBase {}" > RoboFile.php
