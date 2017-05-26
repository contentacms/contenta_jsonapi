# Contenta

## Quick Install

```bash
php -r "readfile('https://raw.githubusercontent.com/contentacms/contenta_jsonapi/8.x-1.x/installer.sh');" > contentacms.sh
chmod a+x contentacms.sh
./contentacms.sh
```

For this to work you will need to have composer installed in your local machine. See [get composer](getcomposer.org) for more details.

## Installation

* [Get composer](getcomposer.org)
* Create a new project using a command like this. This will pull down the installation profile + core + modules, so maybe get a cup of tea:
```
composer create-project contentacms/contenta-jsonapi-project MYPROJECT --stability dev --no-interaction
```
* After that install Drupal normally.

## Development

Join the discussion in the [#contenta Slack channel](https://drupal.slack.com/messages/C5A70F7D1).

For documention on the development on contenta_jsonapi itself, see [docs/development](https://github.com/contentacms/contenta_jsonapi/blob/master/docs/development.md).


## Credits

This work is based upon a couple of contrib modules.

On top of that the [thunder distrbution](http://www.thunder.org/) was used as sort of a base for this installation profile.

Contenta CMS is [built by humans](https://raw.githubusercontent.com/contentacms/contenta_jsonapi/8.x-1.x/humans.txt).
