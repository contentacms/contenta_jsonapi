#!/usr/bin/env bash

BASE_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/../.."

DEST_DIR="$BASE_DIR/../api_first"

if [ $1 ] ; then
  DEST_DIR="$1"
fi

composer create-project drupal/api-first-project ${DEST_DIR} --stability dev --no-interaction --no-install

cd ${DEST_DIR}
composer config repositories.api_first path ${BASE_DIR}

composer require "drupal/api-first:*" "phpunit/phpunit:~4.8" --no-progress

echo "<?php use Drupal\Robo\RoboFileBase; class RoboFile extends RoboFileBase {}" > RoboFile.php
