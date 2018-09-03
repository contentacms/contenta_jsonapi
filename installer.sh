#!/usr/bin/env bash

original_wd=$(pwd)
read -n 1 -p "Do you wish to install Contenta CMS (y/n)? " answer
if echo "$answer" | grep -iq "^y" ;then
    echo -e "\nGreat!"
else
    echo -e "\nBye!"
    exit
fi
read -e -p "Where do you want to install it? (Ex: /var/www/contentacms) " install_path
if echo "$install_path" ;then
    # Clean up the user input.
	install_path=$(echo $install_path|sed -e "s:~:$HOME:g")

	# Download Contenta CMS using composer create-project.
    php -r "readfile('https://raw.githubusercontent.com/contentacms/contenta_jsonapi_project/8.x-1.x/scripts/download.sh');" > download-contentacms.sh
    chmod a+x download-contentacms.sh
    ./download-contentacms.sh $install_path

    # Install Contenta CMS using sqlite by default.
    echo "The Quick Install uses SQLite to install Contenta CMS. This is not suited for production sites."
    # Set the .env data.
    ACCOUNT_PASS="$(LC_ALL=C tr -dc 'A-Za-z0-9!"#$%&'\''()*+,-./:;<=>?@[\]^_`{|}~' </dev/urandom | head -c 13)"
    echo -e "SQLITE_PATH=tmp\nSQLITE_DATABASE=site.sqlite\nSITE_MAIL=admin@localhost\nACCOUNT_MAIL=admin@localhost\nSITE_NAME='Contenta CMS Demo'\nACCOUNT_NAME=admin\nACCOUNT_PASS=$ACCOUNT_PASS" >> $install_path/.env
    echo -e "ACCOUNT_PASS=$ACCOUNT_PASS" >> $install_path/.env.local
    # Install using SQLite.
    composer run-script install:with-sqlite $install_path --timeout=0 --working-dir $install_path

    # Start the built-in PHP server.
    cd $install_path/web user-login
    drush user-login --no-browser --uri="http://127.0.0.1:8888"
    drush runserver 127.0.0.1:8888
	cd $original_wd
else
    echo -e "\nBye!"
    exit
fi
