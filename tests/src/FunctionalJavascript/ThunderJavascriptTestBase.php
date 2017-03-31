<?php

namespace Drupal\Tests\thunder\FunctionalJavascript;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\DocumentElement;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AnonymousUserSession;
use Drupal\FunctionalJavascriptTests\JavascriptTestBase;
use Drupal\Tests\BrowserTestBase;
use Drupal\thunder\ThunderTestTrait;

/**
 * Base class for Thunder Javascript functional tests.
 *
 * @package Drupal\Tests\thunder\FunctionalJavascript
 */
abstract class ThunderJavascriptTestBase extends JavascriptTestBase {

  use ThunderTestTrait;

  /**
   * Modules to enable.
   *
   * The test runner will merge the $modules lists from this class, the class
   * it extends, and so on up the class hierarchy. It is not necessary to
   * include modules in your list that a parent class has already declared.
   *
   * @var string[]
   *
   * @see \Drupal\Tests\BrowserTestBase::installDrupal()
   */
  protected static $modules = ['thunder_demo'];

  /**
   * The profile to install as a basis for testing.
   *
   * @var string
   */
  protected $profile = 'thunder';

  /**
   * {@inheritdoc}
   */
  protected $minkDefaultDriverClass = Selenium2Driver::class;

  /**
   * Directory path for saving screenshots.
   *
   * @var string
   */
  protected $screenshotDirectory = '/tmp/thunder-travis-ci';

  /**
   * Default user login role used for testing.
   *
   * @var string
   */
  protected static $defaultUserRole = 'editor';

  /**
   * {@inheritdoc}
   */
  protected function initMink() {
    $this->minkDefaultDriverArgs = $this->getDriverArgs();

    try {
      return BrowserTestBase::initMink();
    }
    catch (Exception $e) {
      $this->markTestSkipped('An unexpected error occurred while starting Mink: ' . $e->getMessage());
    }
  }

  /**
   * Get Web Driver arguments.
   *
   * Driver arguments depends on used environment where tests are executed.
   * Currently it supports local environment (locally and on Travis CI) and
   * SauceLabs environment on Travis CI.
   *
   * @return array
   *   Returns default driver arguments.
   */
  protected function getDriverArgs() {
    $desiredCapabilities = NULL;
    $webDriverUrl = 'http://127.0.0.1:4444/wd/hub';

    // Get Sauce Labs variables from environment, if Sauce Labs build is set.
    if (!empty(getenv('SAUCE_LABS_ENABLED'))) {
      $sauceUser = getenv('SAUCE_USERNAME');
      $sauceKey = getenv('SAUCE_ACCESS_KEY');

      $desiredCapabilities = [
        'browserName' => 'chrome',
        'version' => '55.0',
        'platform' => 'macOS 10.12',
        'screenResolution' => '1400x1050',
        'tunnelIdentifier' => getenv('TRAVIS_JOB_NUMBER'),
        'name' => get_class($this),
      ];

      $webDriverUrl = "https://{$sauceUser}:{$sauceKey}@ondemand.saucelabs.com:443/wd/hub";
    }

    return [
      'chrome',
      $desiredCapabilities,
      $webDriverUrl,
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function drupalLogin(AccountInterface $account) {
    if ($this->loggedInUser) {
      $this->drupalLogout();
    }

    // Add waiting time, before opening of new page.
    $this->assertSession()->assertWaitOnAjaxRequest();

    $this->drupalGet('user');
    $this->submitForm([
      'name' => $account->getUsername(),
      'pass' => $account->passRaw,
    ], t('Log in'));

    // @see BrowserTestBase::drupalUserIsLoggedIn()
    $account->sessionId = $this->getSession()
      ->getCookie($this->getSessionName());
    $this->assertTrue($this->drupalUserIsLoggedIn($account), SafeMarkup::format('User %name successfully logged in.', ['name' => $account->getUsername()]));

    $this->loggedInUser = $account;
    $this->container->get('current_user')->setAccount($account);
  }

  /**
   * {@inheritdoc}
   */
  protected function drupalLogout() {
    // Make a request to the logout page, and redirect to the user page, the
    // idea being if you were properly logged out you should be seeing a login
    // screen.
    $assert_session = $this->assertSession();
    $this->drupalGet('user/logout', ['query' => ['destination' => 'user']]);
    $assert_session->fieldExists('name');
    $assert_session->fieldExists('pass');

    // @see BrowserTestBase::drupalUserIsLoggedIn()
    unset($this->loggedInUser->sessionId);
    $this->loggedInUser = FALSE;
    $this->container->get('current_user')
      ->setAccount(new AnonymousUserSession());
  }

  /**
   * {@inheritdoc}
   */
  protected function getHtmlOutputHeaders() {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {

    parent::setUp();

    $this->logWithRole(static::$defaultUserRole);

    // Set window width/height.
    $this->getSession()->getDriver()->resizeWindow(1280, 768);
  }

  /**
   * LogIn with defined role assigned to user.
   *
   * @param string $role
   *   Role name that will be assigned to user.
   */
  protected function logWithRole($role) {
    $editor = $this->drupalCreateUser();
    $editor->addRole($role);
    $editor->save();
    $this->drupalLogin($editor);
  }

  /**
   * Waits and asserts that a given element is visible.
   *
   * @param string $selector
   *   The CSS selector.
   * @param int $timeout
   *   (Optional) Timeout in milliseconds, defaults to 1000.
   * @param string $message
   *   (Optional) Message to pass to assertJsCondition().
   */
  public function waitUntilVisible($selector, $timeout = 1000, $message = '') {
    $condition = "jQuery('" . $selector . ":visible').length > 0";
    $this->assertJsCondition($condition, $timeout, $message);
  }

  /**
   * Get directory for saving of screenshots.
   *
   * Directory will be created if it does not already exist.
   *
   * @return string
   *   Return directory path to store screenshots.
   *
   * @throws \Exception
   */
  protected function getScreenshotFolder() {
    if (!is_dir($this->screenshotDirectory)) {
      if (mkdir($this->screenshotDirectory, 0777, TRUE) === FALSE) {
        throw new \Exception('Unable to create directory: ' . $this->screenshotDirectory);
      }
    }

    return realpath($this->screenshotDirectory);
  }

  /**
   * Scroll element with defined css selector in middle of browser view.
   *
   * @param string $cssSelector
   *   CSS Selector for element that should be centralized.
   */
  public function scrollElementInView($cssSelector) {
    $this->getSession()
      ->executeScript('var viewPortHeight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0); var elementTop = jQuery(\'' . addcslashes($cssSelector, '\'') . '\').offset().top; window.scroll(0, elementTop-(viewPortHeight/2));');
  }

  /**
   * Click on Button based on Drupal selector (data-drupal-selector).
   *
   * @param \Behat\Mink\Element\DocumentElement $page
   *   Current active page.
   * @param string $drupalSelector
   *   Drupal selector.
   * @param bool $waitAfterAction
   *   Flag to wait for AJAX request to finish after click.
   */
  public function clickButtonDrupalSelector(DocumentElement $page, $drupalSelector, $waitAfterAction = TRUE) {
    $cssSelector = 'input[data-drupal-selector="' . $drupalSelector . '"]';

    $this->scrollElementInView($cssSelector);
    $editButton = $page->find('css', $cssSelector);
    $editButton->click();

    if ($waitAfterAction) {
      $this->assertSession()->assertWaitOnAjaxRequest();
    }
  }

  /**
   * Click on Button based on Drupal selector (data-drupal-selector).
   *
   * @param \Behat\Mink\Element\DocumentElement $page
   *   Current active page.
   * @param string $cssSelector
   *   Drupal selector.
   * @param bool $waitAfterAction
   *   Flag to wait for AJAX request to finish after click.
   */
  public function clickButtonCssSelector(DocumentElement $page, $cssSelector, $waitAfterAction = TRUE) {

    $this->scrollElementInView($cssSelector);
    $editButton = $page->find('css', $cssSelector);
    $editButton->click();

    if ($waitAfterAction) {
      $this->assertSession()->assertWaitOnAjaxRequest();
    }
  }

  /**
   * Assert page title.
   *
   * @param string $expectedTitle
   *   Expected title.
   */
  protected function assertPageTitle($expectedTitle) {
    $driver = $this->getSession()->getDriver();
    if ($driver instanceof Selenium2Driver) {
      $actualTitle = $driver->getWebDriverSession()->title();

      static::assertTrue($expectedTitle === $actualTitle, 'Title found');
    }
    else {
      $this->assertSession()->titleEquals($expectedTitle);
    }
  }

  /**
   * Fill CKEditor field.
   *
   * @param \Behat\Mink\Element\DocumentElement $page
   *   Current active page.
   * @param string $ckEditorCssSelector
   *   CSS selector for CKEditor.
   * @param string $text
   *   Text that will be filled into CKEditor.
   */
  public function fillCkEditor(DocumentElement $page, $ckEditorCssSelector, $text) {
    $ckEditor = $page->find('css', $ckEditorCssSelector);
    $ckEditorId = $ckEditor->getAttribute('id');

    $this->getSession()
      ->getDriver()
      ->executeScript("CKEDITOR.instances[\"$ckEditorId\"].setData(\"$text\");");
  }

  /**
   * Set value directly to field value, without formatting applied.
   *
   * @param string $fieldName
   *   Field name.
   * @param string $rawValue
   *   Raw value for field.
   */
  public function setRawFieldValue($fieldName, $rawValue) {
    // Set date over jQuery, because browser drivers handle input value
    // differently. fe. (Firefox will set it as "value" for field, but Chrome
    // will use it as text for that input field, and in that case final value
    // depends on format used for input field. That's why it's better to set it
    // directly to value, independently from format used.
    $this->getSession()
      ->executeScript("jQuery('[name=\"{$fieldName}\"]').val('{$rawValue}')");
  }

  /**
   * Expand all tabs on page.
   *
   * It goes up to level 3 by default.
   *
   * @param int $maxLevel
   *   Max depth of nested collapsed tabs.
   */
  public function expandAllTabs($maxLevel = 3) {
    $jsScript = 'jQuery(\'details.js-form-wrapper.form-wrapper:not([open]) > summary\').click().length';

    $numOfOpen = $this->getSession()->evaluateScript($jsScript);
    $this->assertSession()->assertWaitOnAjaxRequest();

    for ($i = 0; $i < $maxLevel && $numOfOpen > 0; $i++) {
      $numOfOpen = $this->getSession()->evaluateScript($jsScript);
      $this->assertSession()->assertWaitOnAjaxRequest();
    }
  }

  /**
   * Execute Cron over UI.
   */
  public function runCron() {
    $this->drupalGet('admin/config/system/cron');

    $this->getSession()
      ->getPage()
      ->find('xpath', '//input[@name="op"]')
      ->click();
  }

  /**
   * Click article save option based on index of action.
   *
   * 1 - Save and continue.
   * 2 - Save as unpublished (default).
   * 3 - Save and publish.
   *
   * @param int $actionIndex
   *   Index for option that should be clicked. (by default 2)
   */
  protected function clickArticleSave($actionIndex = 2) {
    $this->scrollElementInView('[data-drupal-selector="edit-save"]');
    $page = $this->getSession()->getPage();

    if ($actionIndex !== 1) {
      $page->find('xpath', '//ul[@data-drupal-selector="edit-save"]/li[2]/button')
        ->click();
    }

    $page->find('xpath', '(//ul[@data-drupal-selector="edit-save"]/li/input)[' . $actionIndex . ']')
      ->click();
  }

}
