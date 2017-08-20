#!/usr/bin/env bash

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
    drush=$1/bin/drush
    drupal_base=$1/web

    cd $drupal_base
    # Add Permission to anonymous user
    $drush role-add-perm 'anonymous'  'access jsonapi resource list' -y
    $drush updatedb -y
    $drush cr -y

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
    CONTENTA_PATH=$1/web/profiles/contrib/contenta_jsonapi/
    PHPUNIT=$1/bin/phpunit

    cd $CONTENTA_PATH

    WEB_HOST=$WEB_HOST WEB_PORT=$WEB_PORT $PHPUNIT --testsuite ContentaFunctional
    exit $?
}

$@