#!/usr/bin/env bash

BASE_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/../.."

DEST_DIR="$BASE_DIR/../test_contenta_jsonapi"

if [ $1 ] ; then
  DEST_DIR="$1"
fi

echo -e "Drush adds some permissions that need extra powers to delete.\n\n  * You are about to delete $DEST_DIR"
echo "Clearing the destination directory. It requires your sudo password."
sudo rm -Rf $DEST_DIR
echo -e "Downloading Contenta CMS using composer.\n\t* Executing: composer create-project contentacms/contenta-jsonapi-project ${DEST_DIR} --stability dev --no-interaction"
composer create-project contentacms/contenta-jsonapi-project ${DEST_DIR} --stability dev --no-interaction

cd ${DEST_DIR}
# ??
composer config repositories.contenta_jsonapi path ${BASE_DIR}

# ??
# composer require "contentacms/contenta_jsonapi:*" "phpunit/phpunit:~4.8" --no-progress
cd web
echo "Installing Contenta CMS for local usage."
../bin/drush si contenta_jsonapi --db-url=sqlite://sites/default/files/.ht.sqlite --account-pass=test -y

echo "Initializing local PHP server."
../bin/drush rs &

../bin/drush uli

# ??
# echo "<?php use Drupal\Robo\RoboFileBase; class RoboFile extends RoboFileBase {}" > RoboFile.php
