# Contenta

## Requirements

- Install [composer](https://getcomposer.org/)
- Make sure you have the sqlite extension for PHP (if you're using the default install on a mac, this should already be there)
`sudo apt-get install php-sqlite3`

## Quick Install

```bash
php -r "readfile('https://raw.githubusercontent.com/contentacms/contenta_jsonapi/8.x-1.x/installer.sh');" > contentacms.sh
chmod a+x contentacms.sh
./contentacms.sh
```

- To restart the webserver, run `composer run-script start-contenta <install location>`
- Visit [http://127.0.0.1:8888/](http://127.0.0.1:8888/) and log into your site with `admin`/`test`
- The host and port can be overridden by copying `.env` to `.env.local`

## Installation

- Install [composer](https://getcomposer.org/)
Clone this repo and install Contenta from the `install` folder.

```
$ git clone git@github.com:contentacms/contenta_jsonapi.git`
$ cd contenta_jsonapi
$ composer run-script install-contenta /var/www/contenta --timeout=0
$ composer run-script start-contenta /var/www/contenta --timeout=0
```

- To restart the webserver, run `composer run-script start-contenta <install location>`
- Visit [http://127.0.0.1:8888/](http://127.0.0.1:8888/) and log into your site with `admin`/`test`
- The host and port can be overridden by copying `.env` to `.env.local`

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
