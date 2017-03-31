<?php

namespace Drupal\Tests\thunder\FunctionalJavascript;

/**
 * Testing of Meta Information.
 *
 * @group Thunder
 *
 * @package Drupal\Tests\thunder\FunctionalJavascript
 */
class MetaInformationTest extends ThunderJavascriptTestBase {

  use ThunderMetaTagTrait;
  use ThunderMediaTestTrait;

  /**
   * Default user login role used for testing.
   *
   * @var string
   */
  protected static $defaultUserRole = 'administrator';

  /**
   * Meta tag configuration that will be set for Global meta tags.
   *
   * @var array
   */
  protected static $globalMetaTags = [
    'basic title' => 'Global Title',
    'basic keywords' => 'Thunder,CMS,Burda',
    'basic abstract' => '[random]',
    'basic description' => '[random]',
  ];

  /**
   * Meta tag configuration that will be set for Content meta tags.
   *
   * @var array
   */
  protected static $contentMetaTags = [
    'basic title' => '[node:title]',
    'basic abstract' => '[random]',
  ];

  /**
   * Meta tag configuration that will be set for Content->Article meta tags.
   *
   * @var array
   */
  protected static $articleMetaTags = [
    'basic title' => 'Test [node:field_teaser_text]',
    'basic description' => '[random]',
    'advanced robots' => 'index, follow, noydir',
    'advanced referrer' => 'no-referrer-when-downgrade',

    // OpenGraph Meta Tags.
    'open_graph og:image' => '[node:field_teaser_media:entity:field_image:facebook]',
    'open_graph og:image:type' => '[node:field_teaser_media:entity:field_image:facebook:mimetype]',
    'open_graph og:image:height' => '[node:field_teaser_media:entity:field_image:facebook:height]',
    'open_graph og:image:width' => '[node:field_teaser_media:entity:field_image:facebook:width]',
    'open_graph og:description' => '[node:field_teaser_text]',
    'open_graph og:title' => '[node:field_seo_title]',
    'open_graph og:site_name' => '[node:title]',
    'open_graph og:type' => 'article',
  ];

  /**
   * Custom meta tag configuration that will be set for Article meta tags.
   *
   * @var array
   */
  protected static $customMetaTags = [
    'basic title' => 'Custom [node:field_teaser_text]',
    'basic description' => '[random]',
    'advanced robots' => 'follow',
    'advanced referrer' => 'no-referrer',
  ];

  /**
   * List of Tokens that will be replaced with values.
   *
   * @var array
   */
  protected static $tokens = [
    '[node:field_seo_title]' => 'Test SEO Title',
    '[node:field_teaser_text]' => 'Test Teaser Text',
    '[node:title]' => 'Test Note Title',

    // For testing Media:1 is used for teaser.
    '[node:field_teaser_media:entity:field_image:facebook]' => 'LIKE:/files/styles/facebook/public/2016-05/thunder.jpg?itok=',
    '[node:field_teaser_media:entity:field_image:facebook:mimetype]' => 'image/jpeg',
    '[node:field_teaser_media:entity:field_image:facebook:height]' => '630',
    '[node:field_teaser_media:entity:field_image:facebook:width]' => '1200',
  ];

  /**
   * Set meta tag configuration for administration url.
   *
   * @param string $pageUrl
   *   Url to page where configuration should be set.
   * @param array $configuration
   *   List of configuration what will be set for meta tag.
   */
  protected function setMetaTagConfigurationForUrl($pageUrl, array $configuration) {
    $this->drupalGet($pageUrl);

    $page = $this->getSession()->getPage();
    $this->expandAllTabs();
    $this->setFieldValues($page, $this->generateMetaTagFieldValues($configuration));

    $this->scrollElementInView('[name="op"]');
    $page->find('xpath', '//input[@name="op"]')->click();
  }

  /**
   * Create simple article for meta tag testing.
   *
   * @param array $fieldValues
   *   Custom meta tag configuration for article.
   */
  protected function createArticleWithFields(array $fieldValues = NULL) {
    $this->drupalGet('node/add/article');
    $this->assertSession()->assertWaitOnAjaxRequest();

    $page = $this->getSession()->getPage();

    $page->selectFieldOption('field_channel', 1);
    $page->fillField('title[0][value]', static::$tokens['[node:title]']);
    $page->fillField('field_seo_title[0][value]', static::$tokens['[node:field_seo_title]']);
    $page->fillField('field_teaser_text[0][value]', static::$tokens['[node:field_teaser_text]']);

    $this->selectMedia('field_teaser_media', 'image_browser', ['media:1']);

    if (isset($fieldValues)) {
      $this->expandAllTabs();
      $this->setFieldValues($page, $fieldValues);
    }

    $this->clickArticleSave();
  }

  /**
   * Check saved configuration on meta tag overview page.
   *
   * @param string $configurationUrl
   *   Url to page where configuration should be set.
   * @param array $configuration
   *   List of configuration what will be set for meta tag.
   */
  protected function checkSavedConfiguration($configurationUrl, array $configuration) {
    $this->drupalGet('admin/config/search/metatag');
    $page = $this->getSession()->getPage();

    $this->expandAllTabs();

    foreach ($configuration as $metaTagName => $metaTagValue) {
      $metaTag = explode(' ', $metaTagName);
      $fieldName = $this->getMetaTagFieldName($metaTag[1]);

      $this->assertNotEquals(
        NULL,
        $page->find(
          'xpath',
          '//tr[.//a[contains(@href, "/' . $configurationUrl . '")]]/td[1]//table//tr[./td[text()="' . $fieldName . ':"] and ./td[text()="' . $metaTagValue . '"]]'
        )
      );
    }
  }

  /**
   * Test Meta Tag default configuration and custom configuration for article.
   */
  public function testArticleMetaTags() {
    $globalConfigs = $this->generateMetaTagConfiguration([static::$globalMetaTags]);
    $contentConfigs = $this->generateMetaTagConfiguration([static::$contentMetaTags]);
    $articleConfigs = $this->generateMetaTagConfiguration([static::$articleMetaTags]);
    $customConfigs = $this->generateMetaTagConfiguration([static::$customMetaTags]);

    // Generate check configuration for default configuration.
    $checkArticleConfigs = $this->generateMetaTagConfiguration([
      $globalConfigs,
      $contentConfigs,
      $articleConfigs,
    ]);
    $checkArticleMetaTags = $this->replaceTokens($checkArticleConfigs, static::$tokens);

    // Generate check configuration for custom configuration.
    $checkCustomConfigs = $this->generateMetaTagConfiguration([
      $checkArticleConfigs,
      $customConfigs,
    ]);
    $checkCustomMetaTags = $this->replaceTokens($checkCustomConfigs, static::$tokens);

    // Edit Global configuration.
    $configurationUrl = 'admin/config/search/metatag/global';
    $this->setMetaTagConfigurationForUrl($configurationUrl, $globalConfigs);
    $this->checkSavedConfiguration($configurationUrl, $globalConfigs);

    // Edit Content configuration.
    $configurationUrl = 'admin/config/search/metatag/node';
    $this->setMetaTagConfigurationForUrl($configurationUrl, $contentConfigs);
    $this->checkSavedConfiguration($configurationUrl, $contentConfigs);

    // Edit Article configuration.
    $configurationUrl = 'admin/config/search/metatag/node__article';
    $this->setMetaTagConfigurationForUrl($configurationUrl, $articleConfigs);
    $this->checkSavedConfiguration($configurationUrl, $articleConfigs);

    // Create Article with default meta tags and check it.
    $this->createArticleWithFields();
    $this->checkMetaTags($checkArticleMetaTags);

    // Create Article with custom meta tags and check it.
    $this->createArticleWithFields($this->generateMetaTagFieldValues($checkCustomConfigs, 'field_meta_tags[0]'));
    $this->checkMetaTags($checkCustomMetaTags);
  }

  /**
   * Test Scheduling of Article.
   */
  public function testArticleScheduling() {
    $articleId = 10;

    // Create article with published 2 days ago, unpublish tomorrow.
    $startTimestamp = strtotime('-2 days');
    $endTimestamp = strtotime('+1 day');

    $fieldValues = [
      'publish_on[0][value][date]' => date('Y-m-d', $startTimestamp),
      'publish_on[0][value][time]' => date('H:i:s', $startTimestamp),
      'unpublish_on[0][value][date]' => date('Y-m-d', $endTimestamp),
      'unpublish_on[0][value][time]' => date('H:i:s', $endTimestamp),
    ];

    $this->createArticleWithFields($fieldValues);

    // Check that Article is unpublished.
    $this->drupalGet('node/' . $articleId);
    $this->assertSession()
      ->elementExists('xpath', '//div[@class="content"]/article[contains(@class, "node--unpublished")]');

    $this->runCron();

    // Check that Article is published.
    $this->drupalGet('node/' . $articleId);
    $this->assertSession()
      ->elementNotExists('xpath', '//div[@class="content"]/article[contains(@class, "node--unpublished")]');

    // Check that Article is published.
    $this->drupalGet('node/' . $articleId . '/edit');
    $page = $this->getSession()->getPage();

    // Edit article and set un-publish date same as publish date.
    $unPublishDiffSeconds = 5;
    $unPublishTimestamp = strtotime("+{$unPublishDiffSeconds} seconds");
    $unPublishFieldValues = [
      'unpublish_on[0][value][date]' => date('Y-m-d', $unPublishTimestamp),
      'unpublish_on[0][value][time]' => date('H:i:s', $unPublishTimestamp),
    ];

    $this->expandAllTabs();
    $this->setFieldValues($page, $unPublishFieldValues);

    $this->clickArticleSave();

    // Check that Article is published.
    $this->drupalGet('node/' . $articleId);
    $this->assertSession()
      ->elementNotExists('xpath', '//div[@class="content"]/article[contains(@class, "node--unpublished")]');

    // Wait sufficient time before cron is executed.
    sleep($unPublishDiffSeconds + 2);

    $this->runCron();

    // Check that Article is unpublished.
    $this->drupalGet('node/' . $articleId);
    $this->assertSession()
      ->elementExists('xpath', '//div[@class="content"]/article[contains(@class, "node--unpublished")]');
  }

  /**
   * Get SiteMap dom elements by XPath.
   *
   * @param string $content
   *   XML string content of Site Map.
   * @param string $xpathQuery
   *   XPath to fetch elements from Site Map.
   *
   * @return \DOMNodeList
   *   Returns list of elements matching provided XPath.
   */
  public function getSiteMapDomElements($content, $xpathQuery) {
    $domDoc = new \DOMDocument();
    $domDoc->loadXML($content);

    $xpath = new \DOMXpath($domDoc);
    $xpath->registerNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');

    $elements = $xpath->query($xpathQuery);

    return $elements;
  }

  /**
   * Test Site Map for Article.
   */
  public function testSiteMap() {
    $articleId = 10;
    $articleUrl = 'test-sitemap-seo-title';

    $customFields = [
      'field_seo_title[0][value]' => $articleUrl,
    ];

    $this->createArticleWithFields($customFields);

    $this->drupalGet('node/' . $articleId . '/edit');

    // Publish article.
    $this->clickArticleSave(3);

    $this->runCron();
    $this->drupalGet('sitemap.xml');

    $content = $this->getSession()->getPage()->getContent();
    $domElements = $this->getSiteMapDomElements($content, '//sm:loc[contains(text(),"/' . $articleUrl . '")]/parent::sm:url/sm:priority');
    $this->assertEquals(1, $domElements->length);
    $this->assertEquals('0.5', $domElements->item(0)->nodeValue);

    // After sitemap.xml -> we have to open page without setting cookie before.
    $this->getSession()->visit($this->buildUrl('node/' . $articleId . '/edit'));
    $page = $this->getSession()->getPage();

    $this->expandAllTabs();
    $this->setFieldValues($page, [
      'simple_sitemap_priority' => '0.9',
    ]);

    $this->clickArticleSave();

    $this->runCron();
    $this->drupalGet('sitemap.xml');

    $content = $this->getSession()->getPage()->getContent();
    $domElements = $this->getSiteMapDomElements($content, '//sm:loc[contains(text(),"/' . $articleUrl . '")]/parent::sm:url/sm:priority');
    $this->assertEquals(1, $domElements->length);
    $this->assertEquals('0.9', $domElements->item(0)->nodeValue);

    // After sitemap.xml -> we have to open page without setting cookie before.
    $this->getSession()
      ->visit($this->buildUrl('admin/config/search/simplesitemap'));
    $page = $this->getSession()->getPage();
    $this->setFieldValues($page, [
      'max_links' => '2',
    ]);
    $page->find('xpath', '//input[@id="edit-submit"]')->click();

    $this->runCron();

    // Check loc, that it's pointing to sitemap.xml file.
    $this->drupalGet('sitemap.xml');
    $content = $this->getSession()->getPage()->getContent();
    $domElements = $this->getSiteMapDomElements($content, '(//sm:loc)[last()]');
    $lastSiteMapUrl = $domElements->item(0)->nodeValue;
    $this->assertStringEndsWith('/sitemap.xml', $lastSiteMapUrl);

    // Get 3rd sitemap.xml file and check that link exits there.
    $this->getSession()->visit($this->buildUrl('sitemaps/3/sitemap.xml'));
    $content = $this->getSession()->getPage()->getContent();
    $domElements = $this->getSiteMapDomElements($content, '//sm:loc[contains(text(),"/' . $articleUrl . '")]/parent::sm:url/sm:priority');
    $this->assertEquals(1, $domElements->length);
    $this->assertEquals('0.9', $domElements->item(0)->nodeValue);

    // After sitemap.xml -> we have to open page without setting cookie before.
    $this->getSession()->visit($this->buildUrl('node/' . $articleId . '/edit'));
    $page = $this->getSession()->getPage();

    $this->expandAllTabs();
    $this->scrollElementInView('[name="simple_sitemap_index_content"]');
    $page->find('css', '[name="simple_sitemap_index_content"]')->click();

    $this->clickArticleSave();

    $this->runCron();
    $this->drupalGet('sitemaps/3/sitemap.xml');

    $content = $this->getSession()->getPage()->getContent();
    $domElements = $this->getSiteMapDomElements($content, '//sm:loc[contains(text(),"/' . $articleUrl . '")]');

    $this->assertEquals(0, $domElements->length);

    $this->getSession()->visit($this->buildUrl('node/' . $articleId . '/edit'));
  }

}
