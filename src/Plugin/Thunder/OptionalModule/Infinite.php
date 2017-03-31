<?php

namespace Drupal\thunder\Plugin\Thunder\OptionalModule;

/**
 * Google Analytics.
 *
 * @ThunderOptionalModule(
 *   id = "infinite",
 *   label = @Translation("Infinite Theme"),
 *   description = @Translation("Whenever a user reaches the end of an article, the Infinite Theme loads further relevant content."),
 *   type = "theme",
 * )
 */
class Infinite extends AbstractOptionalModule {

  /**
   * {@inheritdoc}
   */
  public function submitForm(array $formValues) {

    \Drupal::configFactory()
      ->getEditable('system.theme')
      ->set('default', 'infinite')
      ->save(TRUE);
  }

}
