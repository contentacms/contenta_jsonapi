<?php

namespace Drupal\Tests\thunder\FunctionalJavascript;

/**
 * Tests the article creation.
 *
 * @group Thunder
 */
class ArticleCreationTest extends ThunderJavascriptTestBase {

  use ThunderParagraphsTestTrait;
  use ThunderMediaTestTrait;

  /**
   * Filed name for paragraphs in article content.
   *
   * @var string
   */
  protected static $paragraphsField = 'field_paragraphs';

  /**
   * Test Creation of Article.
   */
  public function testCreateArticle() {
    $this->drupalGet('node/add/article');

    $page = $this->getSession()->getPage();

    $page->selectFieldOption('field_channel', 1);

    $page->fillField('title[0][value]', 'Test article');
    $page->fillField('field_seo_title[0][value]', 'Massive gaining seo traffic text');

    $this->selectMedia('field_teaser_media', 'image_browser', ['media:1']);

    // Add Image Paragraph.
    $this->addImageParagraph(static::$paragraphsField, ['media:5']);

    // Add Text Paragraph.
    $this->addTextParagraph(static::$paragraphsField, 'Awesome text');

    // Add Gallery Paragraph.
    $this->addGalleryParagraph(static::$paragraphsField, 'Test gallery', [
      'media:1',
      'media:5',
    ]);

    // Add Quote Paragraph.
    $this->addTextParagraph(static::$paragraphsField, 'Awesome quote', 'quote');

    // Add Twitter Paragraph.
    $this->addSocialParagraph(static::$paragraphsField, 'https://twitter.com/ThunderCoreTeam/status/776417570756976640', 'twitter');

    // Add Instagram Paragraph.
    $this->addSocialParagraph(static::$paragraphsField, 'https://www.instagram.com/p/BK3VVUtAuJ3/', 'instagram');

    // Add Link Paragraph.
    $this->addLinkParagraph(static::$paragraphsField, 'Link to Thunder', 'http://www.thunder.org');

    // Add Video paragraph.
    $this->addVideoParagraph(static::$paragraphsField, ['media:7']);

    $this->scrollElementInView('#edit-actions');

    $this->createScreenshot($this->getScreenshotFolder() . '/ArticleCreationTest_BeforeSave_' . date('Ymd_His') . '.png');

    $this->clickArticleSave();

    $this->createScreenshot($this->getScreenshotFolder() . '/ArticleCreationTest_AfterSave_' . date('Ymd_His') . '.png');

    $this->assertPageTitle('Massive gaining seo traffic text');
    $this->assertSession()->pageTextContains('Test article');

    // Check Image paragraph.
    $this->assertSession()
      ->elementsCount('xpath', '//div[contains(@class, "field--name-field-paragraphs")]/div[contains(@class, "field__item")][1]//img', 1);

    // Check Text paragraph.
    $this->assertSession()->pageTextContains('Awesome text');

    // Check Gallery paragraph. Ensure that there are 2 images in gallery.
    $this->assertSession()
      ->elementsCount('xpath', '//div[contains(@class, "field--name-field-paragraphs")]/div[contains(@class, "field__item")][3]//div[contains(@class, "slick-track")]/div[not(contains(@class, "slick-cloned"))]//img', 2);

    // Check Quote paragraph.
    $this->assertSession()->pageTextContains('Awesome quote');

    // Check that one Instagram widget is on page.
    $this->getSession()
      ->wait(5000, "jQuery('iframe').filter(function(){return (this.src.indexOf('instagram.com/p/BK3VVUtAuJ3') !== -1);}).length === 1");

    // Check that one Twitter widget is on page.
    $this->getSession()
      ->wait(5000, "jQuery('iframe').filter(function(){return (this.id.indexOf('twitter-widget-0') !== -1);}).length === 1");

    // Check Link Paragraph.
    $this->assertSession()->linkExists('Link to Thunder');
    $this->assertSession()->linkByHrefExists('http://www.thunder.org');

    // Check for sharing buttons.
    $this->assertSession()->elementExists('css', '.shariff-button.twitter');
    $this->assertSession()->elementExists('css', '.shariff-button.facebook');
    $this->assertSession()->elementExists('css', '.shariff-button.googleplus');

    // Check Video paragraph.
    $this->getSession()
      ->wait(5000, "jQuery('iframe').filter(function(){return (this.src.indexOf('youtube.com/embed/Ksp5JVFryEg') !== -1);}).length === 1");
  }

}
