<?php

namespace Drupal\thunder;

use Drupal\Tests\BrowserTestBase;

/**
 * Class ThunderBaseTest.
 *
 * @package Drupal\thunder
 */
class ThunderBaseTest extends BrowserTestBase {

  use ThunderTestTrait;

  protected $profile = 'thunder';

}
