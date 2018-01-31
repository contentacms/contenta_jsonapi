# Contenta

Contenta is a content API and CMS based on Drupal 8. It provides a standard, jsonapi-based platform for building decoupled applications and websites.

## Quick Install

- Install [composer](https://getcomposer.org/)
- Make sure you have the sqlite extension for PHP (if you're using the default install on a mac, this should already be there)
`sudo apt-get install php-sqlite3`

```bash
php -r "readfile('https://raw.githubusercontent.com/contentacms/contenta_jsonapi/8.x-1.x/installer.sh');" > contentacms.sh
chmod a+x contentacms.sh
./contentacms.sh
```

- In your console will be a one-time login link to access your site.

Check the full installation instructions below for the commands to restart the web server and regenerate the login link. You will need to install Drush.

## Installation for Building Your Own Site

- Install [composer](https://getcomposer.org/)

```bash
composer create-project contentacms/contenta-jsonapi-project <DESTINATION> --stability dev --no-interaction
cd <DESTINATION>/web 
```
- Install the site with  either of these databases:
  - SQLite `../bin/drush si contenta_jsonapi --db-url=sqlite://sites/default/files/.ht.sqlite -y` or
  - MySQL `../bin/drush si contenta_jsonapi --db-url=mysql://root:pass@localhost:3306/dbname -y`
  - PostgreSQL `../bin/drush si contenta_jsonapi --db-url=pgsql://root:pass@localhost:5432/dbname -y`
- Start the web server with `../bin/drush runserver`. This defaults to `127.0.0.1:8888`, you can change this by appending a new host and port, e.g. `../bin/drush runserver local.contentacms.io:8000`
- Generate a one-time login link `../bin/drush user-login --uri="http://127.0.0.1:8888"`

### CURL

Once your site is running locally, you might want to use Curl to examine the pre-installed content:

```bash
curl --header 'Accept: application/vnd.api+json' http://127.0.0.1:8888/api/recipes
```

The result will be a list of recipes. Note that:

- The Drupal implementation of the jsonapi uses entity uuids to identify individual content resources. Append a `/{{uuid}}` to the URL above to fetch a single recipe. 
- Contenta uses the [JSON API Extras](https://www.drupal.org/project/jsonapi_extras) module to customize the URL to resources: `/api/recipes` instead of `/jsonapi/node/recipes`, for example. Contenta also configures JSON API Extras to customize the output of the request to eliminate unnecessary fields.

### CORS

When you actually build a front-end you will likely have [CORS (Cross-Origin Resource Sharing)](https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS)
issues.

In order to allow browsers to request the contenta back-end you need to:

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
* Run drush: ```cd <DESTINATION>/web && ../bin/drush cr```

## Development

Join the discussion in the [#contenta Slack channel](https://drupal.slack.com/messages/C5A70F7D1).

For documention on the development on contenta_jsonapi itself, see [docs/development](https://github.com/contentacms/contenta_jsonapi/blob/8.x-1.x/docs/development.md).

[Code of conduct](https://https://github.com/contentacms/contenta_jsonapi/blob/8.x-1.x/CODE_OF_CONDUCT.md)

### Development Installation

- If you want a setup which allows you to contribute back to Contenta, follow the installation instructions above
- Replace the <DESTINATION>/web/profiles/contrib/contenta_jsonapi directory with a checkout of this repo

```bash
cd <DESTINATION>
rm -rf web/profiles/contrib/contenta_jsonapi
git clone git@github.com:contentacms/contenta_jsonapi.git web/profiles/contrib/contenta_jsonapi
```

### Testing

#### Nightwatch

[Nightwatch](http://nightwatchjs.org/) provides automated browser testing and can be found in the `tests/nightwatch` directory. To install and run locally, you will need [Yarn](https://yarnpkg.com/) and Chrome.

```
yarn install
yarn run nightwatch
```

## Front-ends

There are a bunch of example consumers, see http://www.contentacms.org/#example-consumers for a list of them.


## Credits

This work is based upon a couple of contrib modules.

On top of that the [thunder distrbution](http://www.thunder.org/) was used as sort of a base for this installation profile.

Contenta CMS is [built by humans](https://raw.githubusercontent.com/contentacms/contenta_jsonapi/8.x-1.x/humans.txt).
