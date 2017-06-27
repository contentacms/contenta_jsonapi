# Contenta

## Quick Install

- Install [composer](https://getcomposer.org/)
- Make sure you have the sqlite extension for PHP (if you're using the default install on a mac, this should already be there)
`sudo apt-get install php-sqlite3`

```bash
$ php -r "readfile('https://raw.githubusercontent.com/contentacms/contenta_jsonapi/8.x-1.x/installer.sh');" > contentacms.sh
$ chmod a+x contentacms.sh
$ ./contentacms.sh
```

- In your console will be a one-time login link to access your site.

Check the full installation instructions below for the commands to restart the web server and regenerate the login link. You will need to install Drush.

## Installation for Building Your Own Site

- Install [composer](https://getcomposer.org/)
- Install [drush](http://docs.drush.org/en/8.x/install/)

```bash
$ composer create-project contentacms/contenta-jsonapi-project <DESTINATION> --stability dev --no-interaction
$ cd <DESTINATION>
```

- Decide whether you want to install with either SQLite `drush si contenta_jsonapi --db-url=sqlite://sites/default/files/.ht.sqlite -y`
- or MySQL `drush si contenta_jsonapi --db-url=mysql://root:pass@localhost:port/dbname -y`
- or PostgreSQL `drush si contenta_jsonapi --db-url=pgsql://root:pass@localhost:port/dbname -y`
- Start the web server `drush runserver`. This defaults to `127.0.0.1:8888`, you can change this by appending a new host and port. `drush runserver local.contentacma.io:8000`
- Generate a one-time login link `drush user-login --uri="http://127.0.0.1:8888"`

### CORS

When you actually build a frontend you will likely have [CORS (Cross-Origin Resource Sharing)](https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS)
issues.

In order to allow browsers to request the contenta backend you need to:

* Copy sites/default/default.services.yml to sites/default/services.yml
* Allow your app to access it, by replacing the end of this configuration file.
```
  cors.config:
    enabled: true
    allowedHeaders:
      - '*'
    allowedMethods:
      - '*'
    allowedOrigins:
       # Note: you need to specify the host + port where your app will run.
      - localhost:8000
    exposedHeaders: false
    maxAge: false
    supportsCredentials: false
```
* Run drush: ```cd web && ../bin/drush cr```

## Development

Join the discussion in the [#contenta Slack channel](https://drupal.slack.com/messages/C5A70F7D1).

For documention on the development on contenta_jsonapi itself, see [docs/development](https://github.com/contentacms/contenta_jsonapi/blob/master/docs/development.md).

### Development Installation

- If you want a setup which allows you to contribute back to Contenta, follow the installation instructions above
- Replace the <DESTINATION>/web directory with a checkout of this repo

```bash
$ cd <DESTINATION>
$ ls
README.md     bin           composer.json composer.lock scripts       vendor        web
$ rm -rf web
$ git clone git@github.com:contentacms/contenta_jsonapi.git web
```

### Testing

#### Nightwatch

[Nightwatch](http://nightwatchjs.org/) provides automated browser testing and can be found in the `tests/nightwatch` directory. To install and run locally, you will need [Yarn](https://yarnpkg.com/) and Chrome.

```
$ yarn install
$ yarn run nightwatch
```

## Frontends

Please implement your own frontends and talk about it

Existing frontends (all in development):

* https://github.com/contentacms/contenta_angular
* https://github.com/contentacms/contenta_react
* https://github.com/contentacms/contenta_jsonapi__elm


## Credits

This work is based upon a couple of contrib modules.

On top of that the [thunder distrbution](http://www.thunder.org/) was used as sort of a base for this installation profile.

Contenta CMS is [built by humans](https://raw.githubusercontent.com/contentacms/contenta_jsonapi/8.x-1.x/humans.txt).
