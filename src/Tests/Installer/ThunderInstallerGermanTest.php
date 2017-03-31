<?php

namespace Drupal\thunder\Tests\Installer;

/**
 * Tests the interactive installer installing the standard profile.
 *
 * @group ThunderInstaller
 */
class ThunderInstallerGermanTest extends ThunderInstallerTest {

  protected $langcode = 'de';

  protected $translations = [
    'Save and continue' => 'Speichern und fortfahren',
  ];

  /**
   * Installer step: Select language.
   */
  protected function setUpLanguage() {
    $edit = [
      'langcode' => $this->langcode,
    ];
    $this->drupalPostForm(NULL, $edit, 'Save and continue');
  }

}
