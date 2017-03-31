<?php

namespace Drupal\thunder\Plugin\Thunder\OptionalModule;

use Drupal\Core\Form\FormStateInterface;

/**
 * Adsense.
 *
 * @ThunderOptionalModule(
 *   id = "adsense",
 *   label = @Translation("AdSense"),
 *   description = @Translation("With Google AdSense, you can earn money from your online content."),
 *   type = "module",
 * )
 */
class Adsense extends AbstractOptionalModule {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $form['adsense']['adsense_basic_id'] = [
      '#type' => 'textfield',
      '#title' => t('Site Google AdSense Publisher ID'),
      '#required' => FALSE,
      '#default_value' => '',
      '#pattern' => 'pub-[0-9]+',
      '#description' => t('This is the Google AdSense Publisher ID for the site owner. It is used if no other ID is suitable. Get this in your Google Adsense account. It should be similar to %id.', [
        '%id' => 'pub-9999999999999',
      ]
      ),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array $formValues) {

    $this->configFactory->getEditable('adsense.settings')
      ->set('adsense_basic_id', (string) $formValues['adsense_basic_id'])
      ->save(TRUE);
  }

}
