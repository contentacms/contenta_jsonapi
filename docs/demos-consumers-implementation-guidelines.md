
# Demos consumers implementation guidelines

## wireframes

Consumers MUST implement those [wireframes](https://www.drupal.org/node/2818741#comment-12114776)

## Hosting, CSS & html, Urls structure, Models, Architecture, Cache, Service workers etc ...

Consumers are free to choose tools which fits best their projects concerning those points; because there is a wide variety of consumers with sometimes very different needs.

For hosting, Github pages are totally free. If you need node.js support, you may consider [Heroku](https://dashboard.heroku.com/login) or [now](https://zeit.co/now) free (but limited) plans. 

## public API

Consumers may use public [JSON-API](https://dev-contentacms.pantheonsite.io/api/)

# Getting started with public JSON API

## JS Useful libraries to request JSON API : 

[waterwheel](https://github.com/acquia/waterwheel.js#json-api)

Angular and Ionic consumers may be interested in :
[angular2-jsonapi](https://github.com/ghidoz/angular2-jsonapi)

[jsonapi-parse](https://www.npmjs.com/package/jsonapi-parse) : resolve nicely included and relationships from JSON response object.

[Subrequests](https://www.npmjs.com/package/d8-subrequests) : experimental for now : allows to create several json api requests in one single http request. This will be soon implemented by waterwheel.

Minimalistic package that transforms an object to a json api query string, use this only if a more complete library is not a solution for you.
[d8-jsonapi-querystring](https://www.npmjs.com/package/d8-jsonapi-querystring)


## API endpoints usage examples :

Public API endpoint is : 
[https://dev-contentacms.pantheonsite.io/api](https://dev-contentacms.pantheonsite.io/api)

**links** key list all existing resources. For example :

Recipes:
[https://dev-contentacms.pantheonsite.io/api/recipes](https://dev-contentacms.pantheonsite.io/api/recipes)

Recipes categories : 
[https://dev-contentacms.pantheonsite.io/api/categories](https://dev-contentacms.pantheonsite.io/api/categories)

## Pages documentation

It follows some documentation describing the various pages of the wireframes, in case someone gets stuck

### Getting Recipes Images and Images

### Front page

The frontpage consists of several elements:

* A list of promoted articles and recipes: This should contain the 3 last "promoted" items. As of now
  there is no easy way to query that instead of querying for both the last 3 recipes and articles and merge them manually.
```javascript
  const data = [...recipes, ...articles].sort((item1, item2) => item1.createdAt > item2.createdAt).slice(0, 3)
```
* A list of links:
  * Dinners to impress: Filter by category 'Main Dessert': ```"&filter[category.name][value]=Main course"```
  * Learn to cook: ```"&filter[difficulty][value]=easy"```
  * Baked up:  ```"&filter[category.name][value]=Dessert"```
  * Quick and easy: ``` "&filter[totalTime][condition][path]=totalTime&filter[totalTime][condition][value]=20&filter[totalTime][condition][operator]=<```
* A grid of the 4 latest recipes, ordered by create time

### Footer

'Get in touch' is links to a Contact Form, 
'About Umami Theme' links to a page providing information about the demo theme itself

### Not yet implemented by PUBLIC API

Here are the actions and resources not yet avalaible on PUBLIC API : 
- Posting a contact form and sending mail (Posting to contact currently is broken in core : https://www.drupal.org/node/2843755 )
- Getting Recipes Image from imageFile property (https://www.drupal.org/node/2890762 . Maybe related to Json Api extras aliasing ? )




