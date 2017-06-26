#!/usr/bin/env bash

original_wd=$(pwd)
mytmpdir=$(mktemp -d 2>/dev/null || mktemp -d -t 'mytmpdir')
git clone --depth=1 https://github.com/contentacms/contenta_jsonapi.git $mytmpdir
cd $mytmpdir
read -n 1 -p "Do you wish to install Contenta CMS (y/n)? " answer
if echo "$answer" | grep -iq "^y" ;then
    echo -e "\nGreat!"
else
    echo -e "\nBye!"
    exit
fi
read -e -p "Where do you want to install it? (Ex: /var/www/contentacms) " install_path
if echo "$install_path" ;then
	install_path=$(echo $install_path|sed -e "s:~:$HOME:g")
	composer run-script install-contenta $install_path --timeout=0
	composer run-script start-contenta $install_path --timeout=0
	cd $original_wd
else
    echo -e "\nBye!"
    exit
fi
rm -Rf $mytmpdir
