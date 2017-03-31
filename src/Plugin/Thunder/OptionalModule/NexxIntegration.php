<?php

namespace Drupal\thunder\Plugin\Thunder\OptionalModule;

use Drupal\Core\Form\FormStateInterface;

/**
 * Riddle integration.
 *
 * @ThunderOptionalModule(
 *   id = "nexx_integration",
 *   label = @Translation("Nexx video integration"),
 *   description = @Translation("nexx.tv offers end-to-end online video platform solutions."),
 *   type = "module",
 * )
 */
class NexxIntegration extends AbstractOptionalModule {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $form['nexx_integration']['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Register a new account at <a href=":nexx_url" target="_blank">http://www.nexx.tv/thunder</a> and get a domain ID and an installation code. You can provide theme right here or at a later stage on the nexx Settings form',
        [':nexx_url' => 'http://www.nexx.tv/thunder']),
    ];

    $form['nexx_integration']['omnia_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Domain ID'),
    ];
    $form['nexx_integration']['nexx_api_authkey'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Installation Code'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array $formValues) {
    $this->configFactory->getEditable('nexx_integration.settings')
      ->set('nexx_api_authkey', $formValues['nexx_api_authkey'])
      ->set('omnia_id', $formValues['omnia_id'])
      ->save(TRUE);
  }

}
