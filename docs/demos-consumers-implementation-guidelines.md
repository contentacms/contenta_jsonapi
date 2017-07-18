
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

# Pages documentation

It follows some documentation describing the various pages of the wireframes, in case someone gets stuck.
Informations are comming from this drupal issue : https://www.drupal.org/node/2818741

## Front page
wireframe : https://www.drupal.org/files/issues/1%20-%20Umami%20Front%20wirefame%20v4.png

The frontpage consists of several elements:

* A list of promoted articles and recipes: This should contain the 3 last "promoted" items. As of now
  there is no easy way to query that instead of querying for both the last 3 recipes and articles and merge them manually.
```javascript
  const data = [...recipes, ...articles].sort((item1, item2) => item1.createdAt > item2.createdAt).slice(0, 3)
```
* Month Editions : The magazine promotion block in the center links to the "Magazine" page
* A list of links:
  * Dinners to impress: Filter by category 'Main Dessert': ```"&filter[category.name][value]=Main course"```
  * Learn to cook: ```"&filter[difficulty][value]=easy"```
  * Baked up:  ```"&filter[category.name][value]=Dessert"```
  * Quick and easy: ``` "&filter[totalTime][condition][path]=totalTime&filter[totalTime][condition][value]=20&filter[totalTime][condition][operator]=<```
* A grid of the 4 latest recipes, ordered by create time

## Features listing page
wireframe : https://www.drupal.org/files/issues/2%20-%20Umami%20features%20wirefame%20v4.png
API endpoint : https://dev-contentacms.pantheonsite.io/api/articles

Features is a list of articles, with a promoted article at the top

## Features detail page

wireframe : https://www.drupal.org/files/issues/3%20-%20Umami%20feature%20wirefame%20v4.png

Main body will be a long text field with full html with the possibility to have images inside. We haven’t faced the idea of having responsive images inside WYSIWYG yet. Just to point out that the main image is a separate field.

## Recipes 
wireframe : https://www.drupal.org/files/issues/4%20-%20Umami%20recipes%20wirefame%20v4.png

- The idea of having a list of tags has been bought back into the wireframes and placed in the pre-footer and these can link through to list the tagged recipes - plans for this View will likely be discussed in our next call

## Recipe detail page
wireframe : https://www.drupal.org/files/issues/5%20-%20Umami%20recipe%20wirefame%20v4.png

The "more recipes" is **recipes of the same category**  (we had the choice here, this still may be discussed : https://www.drupal.org/node/2818741#comment-12122853)

## Magazine detail page
wireframe : https://www.drupal.org/files/issues/6%20-%20Umami%20page%20wirefame%20v4.png

- The magazine page will use the page content type with an aside containing a block that promotes the contact form. The idea is to simply have this content type work in a consistent way for any further pages that may be added to the theme. The contents of the aside can be reviewed as the requirements come together
- The contents illustrated for the page can all be provided via the ckeditor, keeping the design for the page simple but with plenty of opportunity to have some great looking sample content

## Get in touch page / Contact page :

https://www.drupal.org/files/issues/7%20-%20Umami%20contact%20wirefame%20v4.png

## Searh field

Expanded/expandable search field that send you to a list of results.

## Footer

- 'Get in touch' is links to a Contact Form
-'About Umami Theme' links to a page providing information about the demo theme itself

## Not yet implemented by PUBLIC API

Here are the actions and resources not yet avalaible on PUBLIC API : 
- [ ] Posting a contact form and sending mail (Posting to contact currently is broken in core : https://www.drupal.org/node/2843755 )
- [ ] There is not articles content for now : https://dev-contentacms.pantheonsite.io/api/articles
- [ ] Getting Recipes Image from imageFile property (https://www.drupal.org/node/2890762 . Maybe related to Json Api extras aliasing ? )





