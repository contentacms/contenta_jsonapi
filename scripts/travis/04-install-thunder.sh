#!/usr/bin/env bash


# Install thunder and enable Test module
# in provided folder
install_thunder() {
    cd $1

    /usr/bin/env PHP_OPTIONS="-d sendmail_path=`which true`" drush si thunder --db-url=mysql://thunder:thunder@127.0.0.1/drupal -y thunder_module_configure_form.install_modules_thunder_demo
    drush en simpletest -y
}

# Update thunder to current test version
update_thunder() {
    # Link sites folder from initial installation
    mv ${TEST_DIR}/web/sites ${TEST_DIR}/web/_sites
    ln -s ${UPDATE_BASE_PATH}/web/sites ${TEST_DIR}/web/sites

    cd ${TEST_DIR}/web

    # Execute all required updates
    drush updatedb -y
}

drush_make_thunder() {
    cd ${THUNDER_DIST_DIR}

    # Build drupal + thunder from makefile
    drush make --concurrency=5 drupal-org-core.make ${TEST_DIR}/web -y
    mkdir ${TEST_DIR}/web/profiles/thunder
    shopt -s extglob
    rsync -a . ${TEST_DIR}/web/profiles/thunder --exclude web

    drush make -y --no-core ${TEST_DIR}/web/profiles/thunder/drupal-org.make ${TEST_DIR}/web/profiles/thunder
    composer install --working-dir=${TEST_DIR}/web
}

composer_create_thunder() {
    cd ${THUNDER_DIST_DIR}
    composer create-project burdamagazinorg/thunder-project ${TEST_DIR} --stability dev --no-interaction --no-install

    cd ${TEST_DIR}
    composer config repositories.thunder path ${THUNDER_DIST_DIR}
    composer config repositories.thunder_admin git https://github.com/BurdaMagazinOrg/theme-thunder-admin.git
    composer require "burdamagazinorg/thunder:*" "phpunit/phpunit:~4.8" --no-progress
}

apply_patches() {
    cd ${TEST_DIR}/web

    #EXAMPLE:
    # apply cookie expire patch for javascript tests
    #wget https://www.drupal.org/files/issues/test-session-expire-2771547-64.patch
    #patch -p1 < test-session-expire-2771547-64.patch
}

create_testing_dump() {
    cd ${TEST_DIR}/web

    php ./core/scripts/db-tools.php dump-database-d8-mysql | gzip > thunder.php.gz
}
# Build current revision of thunder
if [[ ${INSTALL_METHOD} == "drush_make" ]]; then
    drush_make_thunder
elif [[ ${INSTALL_METHOD} == "composer" ]]; then
    composer_create_thunder
fi

# Install Thunder
if [[ ${TEST_UPDATE} == "true" ]]; then
    # Install last drupal org version and update to currently tested version
    install_thunder ${UPDATE_BASE_PATH}/web
    update_thunder
else
    install_thunder ${TEST_DIR}/web
fi

create_testing_dump

apply_patches
