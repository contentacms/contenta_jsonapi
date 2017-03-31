#!/usr/bin/env bash

# Download thunder from drupal.org with drush
drush_download_thunder() {
    DOWNLOAD_PATH=$1

    mkdir -p $DOWNLOAD_PATH
    cd $DOWNLOAD_PATH
    drush dl thunder --drupal-project-rename="docroot" -y
    composer install --working-dir=${DOWNLOAD_PATH}/docroot
}

# update composer
composer self-update

# download + install Selenium2
if [ ! -d "$SELENIUM_PATH" ]; then
  mkdir -p $SELENIUM_PATH;
fi

if [ ! -f "$SELENIUM_PATH/selenium-server-standalone-$SELENIUM_VERSION.jar" ]; then
  SELENIUM_MINOR_VERSION=$(echo $SELENIUM_VERSION | cut -d. -f1-2)

  wget "http://selenium-release.storage.googleapis.com/$SELENIUM_MINOR_VERSION/selenium-server-standalone-$SELENIUM_VERSION.jar" -O "$SELENIUM_PATH/selenium-server-standalone-$SELENIUM_VERSION.jar"
fi

# download required Selenium Chrome Driver
if [ ! -f "$SELENIUM_PATH/chromedriver-$CHROME_DRIVER_VERSION" ]; then
  wget "https://chromedriver.storage.googleapis.com/$CHROME_DRIVER_VERSION/chromedriver_linux64.zip" -O "$SELENIUM_PATH/chromedriver.zip"

  unzip -o $SELENIUM_PATH/chromedriver.zip -d $SELENIUM_PATH
  mv $SELENIUM_PATH/chromedriver $SELENIUM_PATH/chromedriver-$CHROME_DRIVER_VERSION
fi

# remove xdebug to make php execute faster
phpenv config-rm xdebug.ini

# Install Drush and drupalorg_drush module
composer global require drush/drush:^8.1 drupal/coder
phpenv rehash
drush dl drupalorg_drush-7.x

# verify, that makefile is accepted by drupal.org, otherwise we do not need to go any further
drush verify-makefile

# install image magick
yes '' | pecl install imagick

# Install the PECL YAML extension for strict parsing. yes is used to
# acknowledge all prompts.
if [[ $TRAVIS_PHP_VERSION = '5.6' ]] ; then
  yes '' | pecl install yaml;
elif [[ $TRAVIS_PHP_VERSION = '7.1' ]] ; then
 yes '' | pecl install yaml-2.0.0;
fi;

# Set MySQL Options
mysql -e "SET GLOBAL wait_timeout = 5400;"
mysql -e "SHOW VARIABLES LIKE 'wait_timeout';"

# Prepare MySQL user and database
mysql -e "CREATE DATABASE drupal;"
mysql -e "CREATE USER 'thunder'@'localhost' IDENTIFIED BY 'thunder';"
mysql -e "GRANT ALL ON drupal.* TO 'thunder'@'localhost';"

# PHP conf tweaks
echo 'max_execution_time = 120' >> drupal.php.ini;
echo 'sendmail_path = /bin/true' >> drupal.php.ini;
phpenv config-add drupal.php.ini
phpenv rehash

# Prepare test directory
mkdir -p ${TEST_DIR}

# Clear drush release history cache, to pick up new releases.
rm -f ~/.drush/cache/download/*---updates.drupal.org-release-history-*

# If we test update, we also need the previous version of thunder downloaded
if [[ ${TEST_UPDATE} == "true" ]]; then
    # Download latest release from drupal.org
    drush_download_thunder $UPDATE_BASE_PATH
fi
