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

# Remove files on folder
# This function removes a list of files or folder that may exist on a giving path
# This function receives two argument:
#   $1 -> Path of the folder that contains the files and/or folder to be deleted
#   $2 -> List of folder and/or file names to be deleted.
#   $3 -> Name of the function that is currently calling rm_files_on_folder function
rm_files_on_folder() {
  if [ -z $1 ] ; then
    echo "Please pass a folder path to rm_files_on_folder that contains files to be deleted as \$1" 1>&2
    exit 1
  elif [ -z $2 ] ; then
    echo "Please pass a list of files or folder to rm_files_on_folder that are going to be removed as \$2" 1>&2
    exit 1
  elif [ -z $3 ]; then
    echo "Please pass the name of the function that is calling rm_files_on_folder as \$3" 1>&2
    exit 1
  fi

  path=$1

  list_of_files_in_folder=$(ls -a $1)

  list_to_delete=$2

  for VAR in $list_of_files_in_folder
    do
        if [ "$VAR" == "." ] || [ "$VAR" == ".." ]; then
            continue
        elif [[ " ${list_to_delete[@]} " =~ " $VAR " ]]; then
            if [ -d "$path/$VAR" ] || [ -f "$path/$VAR" ]; then
              echo "Removing: $path/$VAR"
              sudo rm -rf $path/$VAR
            else
              echo "\e[33mWARNING:\e[0m '$path/$VAR' does not exist, please check $3 function and fix the value in the list"
            fi
        fi
    done
}

# Zip Folder Function
# This function receives three argument:
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

    list=(
    "settings.php"
    "services.yml"
    "files"
    )

    rm_files_on_folder $default_dir "${list[*]}" "rm_site"

    echo "Successfully cleaned up '${list[*]}' in '$default_dir'."
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

    list=(
    '.circleci'
    'CODE_OF_CONDUCT.md'
    '.editorconfig'
    '.git'
    '.github'
    '.gitignore'
    'installer.sh'
    'tests'
    '.travis.yml'
    'mkdocs.yml'
    'phpunit.xml'
    )

    profile_contenta=$1/profiles/contrib/contenta_jsonapi

    rm_files_on_folder $profile_contenta "${list[*]}" "contentacms_profile_cleanup"

    echo "Successfully cleaned up '${list[*]}' in '$profile_contenta'."
}

$@