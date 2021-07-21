# Installation

## Prerequisites

- [Composer] 1.7 or higher

## Quick Installation

The quick installation is intended for local development only. For this to work you will need to have composer installed in your local machine.

Mmake sure that the sqlite-extension is installed:

``` bash
# macOS
brew install sqlite

# debian/ubuntu
sudo apt-get install php-sqlite3
```

Then install by running:

``` bash
php -r "readfile('https://raw.githubusercontent.com/contentacms/contenta_jsonapi/8.x-2.x/installer.sh');" > contentacms-quick-installer.sh

chmod a+x contentacms-quick-installer.sh

./contentacms-quick-installer.sh
```

## Project Installation

Project installation is intended for creating new ContentaCMS projects.

1. Pull down the installation profile + core + modules from project template

    ``` bash
    php -r "readfile('https://raw.githubusercontent.com/contentacms/contenta_jsonapi_project/8.x-2.x/scripts/download.sh');" > download-contentacms.sh

    chmod a+x download-contentacms.sh

    ./download-contentacms.sh /path/to/new-dir-for-contenta

    cd /path/to/new-dir-for-contenta
    ```

2. Copy .env.example into .env

    ``` bash
    cp .env.example .env
    ```

3. Add the database connection details to `.env` file

    NOTE: it is highly recommended to use .env.local to store your credentials, since that file is ignored by git.

    ``` env
    # Example .env file.
    SITE_MAIL=admin@localhost
    ACCOUNT_MAIL=admin@localhost
    SITE_NAME='Contenta CMS Demo'
    ACCOUNT_NAME=admin
    MYSQL_DATABASE=contenta
    MYSQL_HOSTNAME=localhost
    MYSQL_PORT=3306
    MYSQL_USER=contenta
    ```

    ``` env
    # Example .env.local file.
    MYSQL_PASSWORD=contenta
    ACCOUNT_PASS=admin
    ```

4. Once you have added your database connection details you can:

    ``` bash
    composer run-script install:with-mysql
    ```

## Drush Installation

By default the installation scripts run [Drush] to install ContentaCMS. If you
are familiar with site installtion this way, or want to manipulate the site
installation with the CLI, here are some helpful examples:

### Standard Install Example

``` bash
drush site:install contenta_jsonapi \
--root=web \
--db-url=mysql://drupal8:drupal8@database/drupal8 \
--verbose \
--yes
```

### Configuration Override Examples

Drush allows for passing in options to override configuration defaults.
ContentaCMS's options can be overrided with the `form_state_values` key.
Possible options are:

- install_modules_recipes_magazin
- install_modules_contentajs

Install without Recipes Magazin and default content

``` bash
drush site:install contenta_jsonapi \
form_state_values.install_modules_recipes_magazin=0 \
--db-url=mysql://drupal8:drupal8@database/drupal8 \
--verbose \
--yes
```

Install without Recipes Magazin and add [ContentaJS]

``` bash
drush site:install contenta_jsonapi \
form_state_values.install_modules_recipes_magazin=0 \
form_state_values.install_modules_contentajs=1 \
--db-url=mysql://drupal8:drupal8@database/drupal8 \
--verbose \
--yes
```

[Composer]: https://getcomposer.org/
[Contenta JSON API Project]: https://github.com/contentacms/contenta_jsonapi_project
[Drush]: https://www.drush.org/
[ContentaJS]: https://github.com/contentacms/contentajs
