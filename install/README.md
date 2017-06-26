# Contenta {json:api} - Demo

This repository is an example of how easily you can start a contenta project using [Circle CI](https://circleci.com) and [Pantheon](https://pantheon.io).

The goal of this repository is to make an example of how easily you can have a modern workflow to install a Drupal 8 back-end ready to serve your decoupled sites.

## About Contenta
Contenta is the community effort of an API-First distribution for Drupal. _Contenta_ means happy in [Catalan](https://en.wikipedia.org/wiki/Catalan_language) and indicates the goal of this project, _Contenta is about Content, it makes your content happy_. The Contenta project aims to have several distributions that can share goals and resources with all the thriving decoupled solutions in Drupal 8. This particular instance focuses on [JSON API](http://jsonapi.org).

By installing the contenta distribution you will get an empty drupal installation ready for you to start creating content types. However if you are only testing the distribution, you can install the `recipes_magazin` contrib module to get a rich content model with example content to play with.

In order to provide a uniform experience the _Contenta_ project aligns with the [Out of The Box experience initiative](https://www.drupal.org/node/2847582). We are under active collaboration to provide the same content types and the same default content so we can offer an out of the box experience with decoupling structured content.

## About JSON API
[JSON API](http://jsonapi.org) is an opinionated modern specification that allows building REST based digital experiences without the typical REST pains. _Contenta {json:api}_ is based on the [jsonapi](https://www.drupal.org/project/jsonapi) module to allow interacting with your content following the specification.

There are several documentation resources of interest:

  - [The official specification](http://jsonapi.org).
  - [The official module documentation in Drupal.org](https://www.drupal.org/docs/8/modules/json-api/json-api).
  - [The video series about the Drupal module](https://www.youtube.com/playlist?list=PLZOQ_ZMpYrZsyO-3IstImK1okrpfAjuMZ).

## Known consumers
This demo repository will only expose an API to build digital experiences out of it. You will find examples on how to build feature rich consumers for Contenta in the list below:

```
We have no examples at the moment. We are working hard on it.
```

## Generate stuff

Generator usage:

```
cd cli
node generator-form.js node
```

This folder provides a couple of generators which lets you get started with
building a decoupled Drupal site:

* generator-form: This generates a working form component from a given entity type, bundle and form mode
* generator-view: This generates a working view component from a given entity type, bundle and view mode

It leverages some mapping between formatters/widgets and react components placed defined in ```form.mapping.json```
and ```view.mapping.json```.

---

## Usage
Fork or clone this repository and add your Cirlcle CI and Pantheon.io information.

### Circle CI integration
TODO

### Pantheon integration
TODO

### Adding custom code
Using a Composer based workflow (LINK TO A BLOG POST NEEDED) you don't have to include Drupal core or contrib modules in your repository. Circle CI will download all the dependencies for you before deploying the assembled site to Pantheon. However, you will probably need to add custom code to meet your project requirements.

TODO: Explain the recommended workflow to add custom code.
