#!/usr/bin/env bash

# Validate Environment Variable Function
# Validate that each value pass has an environment variable define, else exit with error
validate_env_var() {
    for var in $@
    do
        if [ -z "${!var}" ] ; then
            echo "Variable $var is not set" 1>&2
            exit 1
        fi
    done
}

# Zip Folder Function
# This function receives two argument:
#   $1 -> Parent path of the folder that is going to be compressed
#   $2 -> Folder Name that is going to be compressed
#   $3 -> Zip Folder Name
zip_folder(){

    # Validate that 2 arguments were passed
    if [ -z "${!1}" ] || [ -z "${!2}" ] || [ -z "${!3}" ]; then
        echo "Please pass a parent folder path, the folder name and zip name to the zip_folder function" 1>&2
        exit 1
    fi

    validate_env_var TRAVIS_BUILD_DIR

    cd $1

    zip -r $TRAVIS_BUILD_DIR/$3.zip $2/*

    cd $TRAVIS_BUILD_DIR
}

# Remove Site Function
# This function receives one argument:
#   $1 -> The Drupal Base Path
rm_site(){
    if [ -z "${!1}" ] ; then
        echo "Please pass a Drupal Base Path to the rm_site function" 1>&2
        exit 1
    fi

    default_dir=$1/sites/default

    rm -rf $default_dir/settings.php \
           $default_dir/services.yml \
           $default_dir/files
}

$@