<?php

namespace Drupal\Tests\recipes_magazin\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * @group recipes_magazin
 */
class ReinstallationMinimalTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $profile = 'contenta_jsonapi';

  /**
   * Tests reading multilingual content.
   */
  public function testReinstall() {
    /** @var \Drupal\Core\Extension\ModuleInstallerInterface $module_installer */
    $module_installer = \Drupal::service('module_installer');
    // 1. Install the feature.
    $this->assertTrue($module_installer->install(['recipes_magazin']));
    // 2. Make sure that there is a recipe content type with some content in
    // there.
    $this->assertArrayHasKey('recipe', \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple());
    $count = \Drupal::entityTypeManager()->getStorage('node')->getQuery()
      ->condition('type', 'recipe')
      ->count()
      ->execute();
    $this->assertGreaterThan(0, $count);

    $count = \Drupal::entityTypeManager()->getStorage('node')->getQuery()
      ->condition('type', 'article')
      ->count()
      ->execute();
    $this->assertGreaterThan(0, $count);

    // 3. Uninstall the feature.
    $this->assertTrue($module_installer->uninstall(['recipes_magazin']));
    // 4. Make sure that there is no recipe content type with some content in
    // there. But there is still an article and page content type.
    $this->assertArrayNotHasKey('recipe', \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple());
    // 5. Install the feature.
    $this->assertTrue($module_installer->install(['recipes_magazin']));
    // 6. Make sure that there is a recipe content type with some content in
    // there.
    $this->assertArrayHasKey('recipe', \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple());
    $count = \Drupal::entityTypeManager()->getStorage('node')->getQuery()
      ->condition('type', 'recipe')
      ->count()
      ->execute();
    $this->assertGreaterThan(0, $count);
  }

}
