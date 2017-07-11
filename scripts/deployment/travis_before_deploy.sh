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

# ContentaCMS Files List Function
# This function receives no argument
# List of least minimum files needed for Contenta CMS to work
contentacms_files_list() {
     list=(
    'modules'
    'scripts'
    'themes'
    'config'
    'composer.json'
    'composer.lock'
    'vendor'
    'humans.txt'
    '.env'
    'CHANGELOG.md'
    'contenta_jsonapi.info.yml'
    'contenta_jsonapi.install'
    'contenta_jsonapi.profile'
    'README.md'
    'LICENSE.txt'
    )

    echo ${list[*]}
}

# Zip Folder Function
# This function receives two argument:
#   $1 -> Parent path of the folder that is going to be compressed
#   $2 -> Folder Name that is going to be compressed
#   $3 -> Zip Folder Name
zip_folder(){

    # Validate that 2 arguments were passed
    if [ -z $1 ] || [ -z $2 ] || [ -z $3 ]; then
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

    if [ -z $1 ] ; then
        echo "Please pass a Drupal Base Path to the rm_site function" 1>&2
        exit 1
    fi

    default_dir=$1/sites/default

    sudo rm -rf $default_dir/settings.php \
           $default_dir/services.yml \
           $default_dir/files
}

# ContentaCMS Profile Cleanup Function
# Cleanup every unnecessary file/folder inside the ContentaCMS Profile folder
# This function receives one argument:
#   $1 -> The Drupal Base Path
contentacms_profile_cleanup(){

     if [ -z $1 ] ; then
        echo "Please pass a Drupal Base Path to the contenta_profile_cleanup function" 1>&2
        exit 1
    fi

    list=$(contentacms_files_list)

    profile_contenta=$1 #/profiles/contrib/contenta_jsonapi

    list_of_files=$(ls -a $profile_contenta)

    for VAR in $list_of_files
    do
        if [ "$VAR" == "." ] || [ "$VAR" == ".." ]; then
            continue
        elif [[ ! " ${list[@]} " =~ " $VAR " ]]; then
            echo "Removing: $VAR"
            rm -rf $profile_contenta/$VAR
        fi

    done
}

$@