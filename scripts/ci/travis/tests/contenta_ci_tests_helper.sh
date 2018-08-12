#!/usr/bin/env bash

COMPOSER_BIN_DIR="$(composer config bin-dir)"
DOCROOT="web"

# Setup Anonymous User Function
# Gives access to anonymous user to access protected resources.
# This function receives one argument:
#   $1 -> The Drupal Base Path
setup_anonymous_user() {
     if [ -z $1 ] ; then
        echo "Please pass the Contenta Project Base Path to the install_test_dependencies function " 1>&2
        exit 1
    fi

    # Setup local variables
    current_path=`pwd`
    DRUSH=$1/$COMPOSER_BIN_DIR/drush
    DRUPAL_BASE=$1/$DOCROOT

    cd $DRUPAL_BASE
    # Add Permission to anonymous user
    $DRUSH role-add-perm 'anonymous' 'access jsonapi resource list' -y
    $DRUSH updatedb -y
    $DRUSH cr -y

    cd $current_path
}

# Run Functional Tests Function
# Run Contenta CMS Functional Tests
# This function receives one argument:
#   $1 -> The Drupal Base Path
run_functional_tests() {
    if [ -z $1 ] ; then
        echo "Please pass the Contenta Project Base Path to the run_functional_tests function " 1>&2
        exit 1
    fi

    if [[ -z $WEB_HOST ]] || [[ -z $WEB_PORT ]] ; then
        echo "Please ensure that WEB_HOST and WEB_PORT environment variables are set." 1>&2
        exit 1
    fi

    current_path=`pwd`
    CONTENTA_PATH=$1/$DOCROOT/profiles/contrib/contenta_jsonapi/
    PHPUNIT=$1/$COMPOSER_BIN_DIR/phpunit

    cd $CONTENTA_PATH

    WEB_HOST=$WEB_HOST WEB_PORT=$WEB_PORT $PHPUNIT --testsuite ContentaFunctional
    exit $?
}

$@
