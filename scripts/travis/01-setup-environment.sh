#!/usr/bin/env bash

## Setup environment
# add composer's global bin directory to the path
# see: https://github.com/drush-ops/drush#install---composer
export PATH="$HOME/.composer/vendor/bin:$PATH"
export THUNDER_DIST_DIR=`echo $(pwd)`
export TEST_DIR=`echo ${THUNDER_DIST_DIR}"/../test-dir"`
export TEST_INSTALLER="false"

# For daily cron runs, current version from Drupal will be installed
# and after that update will be executed and tested
if [[ ${TRAVIS_EVENT_TYPE} == "cron" ]]; then
    TEST_UPDATE="true"
else
    TEST_UPDATE=""
fi

export TEST_UPDATE;

# base path for update tests
export UPDATE_BASE_PATH=${TEST_DIR}-update-base

# Setup Selenium parameters
export DISPLAY=:99.0
export DBUS_SESSION_BUS_ADDRESS=/dev/null

# Get latest ChromeDriver version
CHROME_DRIVER_VERSION=$(curl -s https://chromedriver.storage.googleapis.com/LATEST_RELEASE)
export CHROME_DRIVER_VERSION

# Selenium related environment variables
SELENIUM_PATH="$PWD/travis_selenium"
export SELENIUM_PATH

SELENIUM_VERSION="3.0.1"
export SELENIUM_VERSION

# Manual overrides of environment variables by commit messages. To override a variable add something like this to
# your commit message:
# git commit -m="Your commit message [TEST_UPDATE=true]"
#
# To override multiple variables us something like this:
# git commit -m="Your other commit message [TEST_UPDATE=true|INSTALL_METHOD=composer]"
if [[ ${TRAVIS_EVENT_TYPE} == "pull_request" ]]; then
    # These are the variables, that are allowed to be overridden
    ALLOWED_VARIABLES=("TEST_UPDATE" "INSTALL_METHOD" "TEST_INSTALLER" "SAUCE_LABS_ENABLED")
    COMMIT_MESSAGE=$(git log --no-merges -1 --pretty="%B")
    for VARIABLE_NAME in "${ALLOWED_VARIABLES[@]}"
    do
        VALUE=$(echo $COMMIT_MESSAGE | perl -lne "/[|\[]$VARIABLE_NAME=(.+?)[|\]]/ && print \$1")
        if [[ $VALUE ]]; then
            export $VARIABLE_NAME=$VALUE
        fi
    done
fi
# Do not place any code behind this line.
