# Change Log

## [8.1.2](https://github.com/BurdaMagazinOrg/thunder-distribution/tree/8.1.2) 2017-03-28
[Full Changelog](https://github.com/BurdaMagazinOrg/thunder-distribution/compare/8.x-1.1...8.x-1.2)

- Update Linkit to 4.3 because of a security release on [2017-03-21](https://www.drupal.org/project/linkit/releases/8.x-4.3)
- Update of contrib modules
- Set a fixed version in build-thunder.make to get Thunder up and running on simplytest.me

## [8.1.1](https://github.com/BurdaMagazinOrg/thunder-distribution/tree/8.1.1) 2017-03-20
[Full Changelog](https://github.com/BurdaMagazinOrg/thunder-distribution/compare/8.x-1.0...8.x-1.1)

- Update of contrib modules
- Fixing coding style issues
- Adding new tours (they will just appear for new installations)

## [8.1.0](https://github.com/BurdaMagazinOrg/thunder-distribution/tree/8.1.0) 2017-01-30
[Full Changelog](https://github.com/BurdaMagazinOrg/thunder-distribution/compare/8.x-1.0-rc6...8.x-1.0)

Release of thunder 8.1.0. This is almost identical to the rc6. We fixed a small css bug, and added a fixed the version for
entity_reference_revisions to the one of rc6.

See also:

- [Set entity_reference_revision to a strict version](https://www.drupal.org/node/2848067#comment-11899804)
- [Set entity_reference_revision to a strict version](https://www.drupal.org/node/2848066#comment-11899801)

## [8.1.0-rc6](https://github.com/BurdaMagazinOrg/thunder-distribution/tree/8.1.0-rc6) 2017-01-25
[Full Changelog](https://github.com/BurdaMagazinOrg/thunder-distribution/compare/8.x-1.0-rc5...8.x-1.0-rc6)

This release candidate mainly improves the updater service and updates most contrib modules to the newest versions. 
An exception to the updates are the blazy and slick modules, which do not work properly after update.
Additionally we now use the pecl yaml extension when validating our yml files, since it is more strict then the previously 
used Symfony component.
lat but not least, we introduced fixed creation dates for the demo articles to prevent having articles with exactly the same creation dates.

## [8.1.0-rc5](https://github.com/BurdaMagazinOrg/thunder-distribution/tree/8.1.0-rc5) 2016-12-01
[Full Changelog](https://github.com/BurdaMagazinOrg/thunder-distribution/compare/8.x-1.0-rc4...8.x-1.0-rc5)

rc4 introduced a bug where part of the configuration could be invalid after updating under some circumstances. This release just fixes this update bug.
If you already upgraded to rc4 you can check if your update has the problem by exporting your configuration (do a drush config-export) and checking if the following files in the config directory are valid:

- entity_browser.browser.gallery_browser.yml
- entity_browser.browser.multiple_image_browser.yml

I you do not have these files, it is ok, if you have them open them and check that they do not look like this:

entity_browser.browser.gallery_browser.yml

```
display_configuration:
  auto_open: true
```

entity_browser.browser.multiple_image_browser.yml

```
widgets:
  7d7f8f45-f628-48a3-84a8-c962c73f39e8:
    settings:
      auto_select: true
  89532aea-140d-4b9e-96f4-2aa489c095cb:
    settings:
      auto_select: true
```

As you can see, those configurations would be incomplete. If your Files look similar to the other entity_browser.browser.* config files, everything is fine.
  

## [8.1.0-rc4](https://github.com/BurdaMagazinOrg/thunder-distribution/tree/8.1.0-rc4) 2016-12-01
[Full Changelog](https://github.com/BurdaMagazinOrg/thunder-distribution/compare/8.x-1.0-rc2...8.x-1.0-rc4)

Test improvements
- Added tests for SEO functionality (Metatag, Sitemap)
- Code style tests and fixes
- Update tests to work with current module markup
 
Config changes
- Activated revisions for article and basic page
- Media URLs are required for twitter and instagram entities
- UX: better responsiveness on content list 
- Improve image style for media thumbnails
- UX: Add author filter to content overview
- UX: Add sorting to entity browser views
- Enable auto select on multiple_image_browser 
- Reorganize node and term edit pages
- Auto open media browser
- reduce allowed allowed upload extensions in image browser
- Label form element non-required in the entity form
- Change gallery paragraph to simple widget
- Use responsive images in gallery

Fixes
- Fix entity browser preselection after error
- Add missing svg files for entity browser
- UX dropzone: [Checkmark indicator for upload screen](https://www.drupal.org/node/2696915) 
- UX dropzone: [Implement maxFiles](https://www.drupal.org/node/2633346) 
- UX dropzone: [Improved MultiStep selection display](https://www.drupal.org/node/2823670) 
- Fix term access for unpublished terms
- UX media browser: [Open entity browser with one click from Entity Browser IEF widget](https://www.drupal.org/node/2778305) 
- [Make instagram responsive](https://www.drupal.org/node/2807735) 
- Fix PHP Notice 
- Better entity browser usability on mobile devices

## [8.1.0-rc3]
skipped release

## [8.1.0-rc2](https://github.com/BurdaMagazinOrg/thunder-distribution/tree/8.1.0-rc2) 2016-10-18
[Full Changelog](https://github.com/BurdaMagazinOrg/thunder-distribution/compare/8.x-1.0-rc1...8.x-1.0-rc2)

- Update to Drupal 8.2.1
- Introduce functional Javascript testing
- Reorganized travis.yml
- Fix metatag default configuration
- Fix display of default values in meta tag
- Fix media expire
- Fix saving of galleries in entity browser (did not save without saving the article). Yay! [[Needs tests] Entity Browser widget loses selected images in inline entity form](https://www.drupal.org/node/2764889)
- Fix save after reordering a newly added paragraph. Also Yay! [Saving problem in preview mode with IEF items](https://www.drupal.org/node/2804377)
- Fix paragraphs viewmode [IEF Simple Widget not working in paragraphs with preview mode](https://www.drupal.org/node/2722097)
- Fix several schema files
- Fix vanilla slick gallery display
- Move FIA code to a separate module

## [8.1.0-rc1](https://github.com/BurdaMagazinOrg/thunder-distribution/tree/8.1.0-rc1) 2016-09-08
[Full Changelog](https://github.com/BurdaMagazinOrg/thunder-distribution/compare/8.x-1.0-beta12...8.x-1.0-rc1)

- Integration of Harbourmaster single sign-on solution
- Nicer content overview with better searchability
- Taxonomy term status is now actually beeing used. Beware: If you have taxonomy terms without a status they will not be shown anymore!
- Improved demo content

## [8.1.0-beta12](https://github.com/BurdaMagazinOrg/thunder-distribution/tree/8.1.0-beta12) 2016-09-08
[Full Changelog](https://github.com/BurdaMagazinOrg/thunder-distribution/compare/8.x-1.0-beta11...8.x-1.0-beta12)

- Cleanup Release
- removing obsolete patches
- updating to current module versions, drupal core
- Fine tune editor permissions
- deeper integration of facebook instant articles
- ckeditor cleanups
- updated README.md

## [8.1.0-beta11](https://github.com/BurdaMagazinOrg/thunder-distribution/tree/8.1.0-beta11) 2016-08-23
[Full Changelog](https://github.com/BurdaMagazinOrg/thunder-distribution/compare/8.x-1.0-beta10...8.x-1.0-beta11)

- Configuration cleanup
- Paragraphs are closed on default
- Add tour for article creation and paragraph usage
- add nexx integration module
- fix focal point module

## [8.1.0-beta10](https://github.com/BurdaMagazinOrg/thunder-distribution/tree/8.1.0-beta10) 2016-08-16
[Full Changelog](https://github.com/BurdaMagazinOrg/thunder-distribution/compare/8.x-1.0-beta9...8.x-1.0-beta10)

- Security update for google analytics module
- Added google adsense module

## [8.1.0-beta9](https://github.com/BurdaMagazinOrg/thunder-distribution/tree/8.1.0-beta9) 2016-07-28
[Full Changelog](https://github.com/BurdaMagazinOrg/thunder-distribution/compare/8.x-1.0-beta8...8.x-1.0-beta9)

- Update to drupal 8.1.8
- Remove already merged dropzone patch.
- Add media_entity_slideshow patch, that makes it possible to update slideshows again.

## [8.1.0-beta8](https://github.com/BurdaMagazinOrg/thunder-distribution/tree/8.1.0-beta8) 2016-07-28
[Full Changelog](https://github.com/BurdaMagazinOrg/thunder-distribution/compare/8.x-1.0-beta7...8.x-1.0-beta8)

- Update to drupal 8.1.7
- Updated modules to most recent release
- Infinite module and theme are not installed by default
- Gallery is handled by slick
- Changed composer repository from https://packagist.drupal-composer.org to https://packages.drupal.org/8
  be warned, that if you require the distribution that you change this in your composer file as well 
  see [https://www.drupal.org/node/2718229](https://www.drupal.org/node/2718229)
- Installation of front end modules is handled by bower
- Improve test coverage

## [8.1.0-beta7](https://github.com/BurdaMagazinOrg/thunder-distribution/tree/8.1.0-beta7) (2016-06-10)
[Full Changelog](https://github.com/BurdaMagazinOrg/thunder-distribution/compare/8.x-1.0-beta6...8.x-1.0-beta7)
- Include media browser and dropzonejs
- Update metatag module to beta8
- Add default content

## [8.x-1.0-beta6](https://github.com/BurdaMagazinOrg/thunder-distribution/tree/8.x-1.0-beta6) (2016-05-11)
[Full Changelog](https://github.com/BurdaMagazinOrg/thunder-distribution/compare/8.x-1.0-beta5...8.x-1.0-beta6)

- Integrate video\_embed\_field
- Add default media paragraphs
- Add a file rename test
