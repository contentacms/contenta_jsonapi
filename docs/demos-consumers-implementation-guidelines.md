
# Demos consumers implementation guidelines

## wireframes

Consumers MUST implement those [wireframes](https://www.drupal.org/node/2818741#comment-12114776)

## designs

The official design from the out of the box initiative can be found on [https://www.drupal.org/node/2900720](https://www.drupal.org/node/2900720)

## Hosting, CSS & html, Urls structure, Models, Architecture, Cache, Service workers etc ...

Consumers are free to choose tools which fits best their projects concerning those points; because there is a wide variety of consumers with sometimes very different needs.

For hosting, [Github pages](https://pages.github.com/) are totally free. If you need node.js support, you may consider [Heroku](https://dashboard.heroku.com/login) or [now](https://zeit.co/now) free (but limited) plans. 

## public API

Consumers may use public [JSON-API](https://dev-contentacms.pantheonsite.io/api/)

# Getting started with public JSON API

## JS Useful libraries to request JSON API : 

[waterwheel](https://github.com/acquia/waterwheel.js#json-api)

Angular and Ionic consumers may be interested in :
[angular2-jsonapi](https://github.com/ghidoz/angular2-jsonapi)

[jsonapi-parse](https://www.npmjs.com/package/jsonapi-parse) : resolve nicely included and relationships from JSON response object.

[Subrequests](https://www.npmjs.com/package/d8-subrequests) : experimental for now : allows to create several json api requests in one single http request. This will be soon implemented by [waterwheel](https://github.com/acquia/waterwheel.js).

Minimalistic package that transforms an object to a json api query string, use this only if a more complete library is not a solution for you.
[d8-jsonapi-querystring](https://www.npmjs.com/package/d8-jsonapi-querystring)


## API endpoints usage examples :

The public API endpoint is : [https://live-contentacms.pantheonsite.io/api](https://live-contentacms.pantheonsite.io/api)
there is also a dev version (more unstable) : [https://dev-contentacms.pantheonsite.io/api](https://dev-contentacms.pantheonsite.io/api)

A list of important resources:

Recipes: [https://live-contentacms.pantheonsite.io/api/recipes](https://live-contentacms.pantheonsite.io/api/recipes)

Recipes categories : [https://live-contentacms.pantheonsite.io/api/categories](https://live-contentacms.pantheonsite.io/api/categories)

# Pages documentation

Content model and wireframes have been provided by the [Outside of the box initiative](https://www.drupal.org/node/2818741).
Frontend consumers should try to follow that. 

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

## Recipes 
wireframe : https://www.drupal.org/files/issues/4%20-%20Umami%20recipes%20wirefame%20v4.png

- The idea of having a list of tags has been bought back into the wireframes and placed in the pre-footer and these can link through to list the tagged recipes - plans for this View will likely be discussed in our next call

## Recipe detail page
wireframe : https://www.drupal.org/files/issues/5%20-%20Umami%20recipe%20wirefame%20v4.png

The "more recipes" is **recipes of the same category**  (we had the choice here, this still may be discussed : https://www.drupal.org/node/2818741#comment-12122853)

## Features listing page
wireframe : https://www.drupal.org/files/issues/2%20-%20Umami%20features%20wirefame%20v4.png
API endpoint : http://live-contentacms.pantheonsite.io/api/articles
Features is a list of articles, with a promoted article at the top
There is not yet articles content for now in public API

## Features detail page

wireframe : https://www.drupal.org/files/issues/3%20-%20Umami%20feature%20wirefame%20v4.png

Main body will be a long text field with full html with the possibility to have images inside. We haven’t faced the idea of having responsive images inside WYSIWYG yet. Just to point out that the main image is a separate field.


## Magazine detail page
wireframe : https://www.drupal.org/files/issues/6%20-%20Umami%20page%20wirefame%20v4.png

- The magazine page will use the page content type with an aside containing a block that promotes the contact form. The idea is to simply have this content type work in a consistent way for any further pages that may be added to the theme. The contents of the aside can be reviewed as the requirements come together
- The contents illustrated for the page can all be provided via the ckeditor, keeping the design for the page simple but with plenty of opportunity to have some great looking sample content

## Get in touch page / Contact page :

https://www.drupal.org/files/issues/7%20-%20Umami%20contact%20wirefame%20v4.png

## Search field

Expanded/expandable search field that send you to a list of results.

## Footer

- 'Get in touch' is links to a Contact Form
- 'About Umami Theme' links to a page providing information about the demo theme itself


## Known limitations of current public API

### API

- We can't use imagestyles to scale and crop imported images
- Posting a contact form and sending mail is not yet possible (see https://www.drupal.org/node/2843755 )
- Getting Recipes Image from imageFile property is not yet possible due to a bug, use "image_field" or "thumbnail" proprety as a workaround for now (https://www.drupal.org/node/2890762 . Maybe related to Json Api extras aliasing ? )
- There is not yet something like a "slug" field to create front-end SEO-friendly paths ( but snail module should allow to fix this https://github.com/contentacms/snail )

### Content contribution

- there are not articles for now to create "features" pages : http://live-contentacms.pantheonsite.io/api/articles
- Month Edition block : how to implement this ?
- unique path for content : when snail will be ready, we may want to fill "path" field for nodes
