<?php

namespace Drupal\thunder\Tests\Installer;

use Drupal\Core\DrupalKernel;
use Drupal\Core\Language\Language;
use Drupal\Core\Session\UserSession;
use Drupal\Core\Site\Settings;
use Drupal\simpletest\InstallerTestBase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Tests the interactive installer installing the standard profile.
 *
 * @group ThunderInstaller
 */
class ThunderInstallerTest extends InstallerTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {

    $this->isInstalled = FALSE;

    // Define information about the user 1 account.
    $this->rootUser = new UserSession([
      'uid' => 1,
      'name' => 'admin',
      'mail' => 'admin@example.com',
      'pass_raw' => $this->randomMachineName(),
    ]);

    // If any $settings are defined for this test, copy and prepare an actual
    // settings.php, so as to resemble a regular installation.
    if (!empty($this->settings)) {
      // Not using File API; a potential error must trigger a PHP warning.
      copy(DRUPAL_ROOT . '/sites/default/default.settings.php', DRUPAL_ROOT . '/' . $this->siteDirectory . '/settings.php');
      $this->writeSettings($this->settings);
    }

    // Note that WebTestBase::installParameters() returns form input values
    // suitable for a programmed \Drupal::formBuilder()->submitForm().
    // @see WebTestBase::translatePostValues()
    $this->parameters = $this->installParameters();

    // Set up a minimal container (required by WebTestBase).
    // @see install_begin_request()
    $request = Request::create($GLOBALS['base_url'] . '/core/install.php');
    $this->container = new ContainerBuilder();
    $request_stack = new RequestStack();
    $request_stack->push($request);
    $this->container
      ->set('request_stack', $request_stack);
    $this->container
      ->setParameter('language.default_values', Language::$defaultValues);
    $this->container
      ->register('language.default', 'Drupal\Core\Language\LanguageDefault')
      ->addArgument('%language.default_values%');
    $this->container
      ->register('string_translation', 'Drupal\Core\StringTranslation\TranslationManager')
      ->addArgument(new Reference('language.default'));
    $this->container
      ->set('app.root', DRUPAL_ROOT);
    \Drupal::setContainer($this->container);

    $this->drupalGet($GLOBALS['base_url'] . '/core/install.php');

    // Select language.
    $this->setUpLanguage();

    // Select profile.
    $this->setUpProfile();

    // Configure settings.
    $this->setUpSettings();

    // Configure site.
    $this->setUpSite();

    // Configure modules.
    $this->setUpModules();

    // Click link, we don't get redirected automatically in tests.
    $this->clickLink('click here');
    $this->isInstalled = TRUE;

    if ($this->isInstalled) {
      // Import new settings.php written by the installer.
      $request = Request::createFromGlobals();
      $class_loader = require $this->container->get('app.root') . '/autoload.php';
      Settings::initialize($this->container->get('app.root'), DrupalKernel::findSitePath($request), $class_loader);
      foreach ($GLOBALS['config_directories'] as $type => $path) {
        $this->configDirectories[$type] = $path;
      }

      // After writing settings.php, the installer removes write permissions
      // from the site directory. To allow drupal_generate_test_ua() to write
      // a file containing the private key for drupal_valid_test_ua(), the site
      // directory has to be writable.
      // WebTestBase::tearDown() will delete the entire test site directory.
      // Not using File API; a potential error must trigger a PHP warning.
      chmod($this->container->get('app.root') . '/' . $this->siteDirectory, 0777);
      $this->kernel = DrupalKernel::createFromRequest($request, $class_loader, 'prod', FALSE);
      $this->kernel->prepareLegacyRequest($request);
      $this->container = $this->kernel->getContainer();

      // Manually configure the test mail collector implementation to prevent
      // tests from sending out emails and collect them in state instead.
      $this->container->get('config.factory')
        ->getEditable('system.mail')
        ->set('interface.default', 'test_mail_collector')
        ->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function setUpLanguage() {
    // Verify that the distribution name appears.
    $this->assertRaw('thunder');
    // Verify that the "Choose profile" step does not appear.
    $this->assertNoText('profile');

    parent::setUpLanguage();
  }

  /**
   * {@inheritdoc}
   */
  protected function setUpProfile() {
    // This step is skipped, because there is a distribution profile.
  }

  /**
   * Final installer step: Configure site.
   */
  protected function setUpSite() {
    $edit = $this->translatePostValues($this->parameters['forms']['install_configure_form']);
    $this->drupalPostForm(NULL, $edit, $this->translations['Save and continue']);
    // If we've got to this point the site is installed using the regular
    // installation workflow.
  }

  /**
   * Setup modules -> subroutine of test setUp process.
   */
  protected function setUpModules() {

    $this->drupalPostForm(NULL, [], $this->translations['Save and continue']);
  }

  /**
   * Confirms that the installation succeeded.
   */
  public function testInstalled() {
    $this->assertUrl('?tour=1');
    $this->assertResponse(200);
    // Confirm that we are logged-in after installation.
    $this->assertText($this->rootUser->getUsername());

    /** @var \Drupal\Core\Database\Query\SelectInterface $query */
    $query = \Drupal::database()->select('watchdog', 'w')
      ->condition('severity', 4, '<');

    // We have one expected warning from the simple_sitemap module.
    $this->assertEqual($query->countQuery()->execute()->fetchField(), 1);

  }

}
