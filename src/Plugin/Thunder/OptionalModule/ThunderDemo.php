<?php

namespace Drupal\thunder\Plugin\Thunder\OptionalModule;

/**
 * Thunder Demo Content.
 *
 * @ThunderOptionalModule(
 *   id = "thunder_demo",
 *   label = @Translation("Thunder Demo Content"),
 *   description = @Translation("Installs demo content to show how Thunder works."),
 *   type = "module",
 *   standardlyEnabled = 1,
 *   weight = -1
 * )
 */
class ThunderDemo extends AbstractOptionalModule {}
