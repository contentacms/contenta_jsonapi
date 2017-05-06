# Material Admin
Material Design Inspired Admin Theme Utilizing http://materialcss.com Framework

![alt text][logo]

[logo]: https://github.com/briancwald/material_admin/blob/8.x-1.x/images/screenshot.png "Drupal Material Admin"

## Dev Requirments 
[Yarn package manager](https://yarnpkg.com)

## Dev Setup 
 - `Yarn install`
 - `Gulp libsrc` Gets libries
 - `Gulp rename` renames conflict with jqueryUI and MaterializeCSS autocomplete plugin
 - `Gulp copy` moves updated libraries over to js/lib folder
 - `Gulp sass` or `gulp` to watch sass changes

## To-Do
- [x] Gulp Setup
- [x] Add method to use materialize partials
- [x] Navigation / Local Tasks
- [x] Breadcrumbs (responsive)
- [x] Date and Time selector
- [x] Submit and action buttons
- [ ] Vertical Tabs support / Styling
- [x] Submit button loading UX
- [x] Admin landing page / group styling
- [x] Dropbutton replacement
- [ ] Throbber/Prgoress icons
- [x] admin/content enhancements 
- [ ] view UI (Yikes - completely unuseable right now. The template strucutre for this page is very.. bad)
- [ ] form styling defaults
- [x] Table and bulk selecting
- [x] Status Message
- [x] Theme Select page
- [ ] Node add/edit
- [ ] jqueryUI Dialog Theme & Enhancements
- [ ] Behat Testing

## Clean-up oganization To-Do
Since this is just a POC, code is not very well organized and needs to be matured. here is what I see so far:

- [ ] Make JS features optional in settings
- [ ] Move SCSS out of admin.scss into sub components (e.g. navigation, buttons, forms (done), etc.)
- [ ] move preprocess functions into .inc files and out of .theme
- [ ] Better way to handle materializecss overrides
- [ ] Remove Classy as a parent theme entirely?
- [ ] Prod deployment packaging (Min, optimize, etc)

## Meta

- Icons: currently using font awesome because sass integration allows for simple integration in D8 admin methods -- but looking at google material icons it might work fine -- switch for consistancy?

- Grid: Impliment a more struture grid system. The template sturcutre in D8 has basically no notion of grid system. I have started to add in materializecss very light grid system but its awkward.





