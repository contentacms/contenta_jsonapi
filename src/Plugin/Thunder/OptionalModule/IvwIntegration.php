<?php

namespace Drupal\thunder\Plugin\Thunder\OptionalModule;

use Drupal\Core\Form\FormStateInterface;

/**
 * IVW Integration.
 *
 * @ThunderOptionalModule(
 *   id = "ivw_integration",
 *   label = @Translation("IVW Integration"),
 *   description = @Translation("Integration module for the German audience measurement organisation IVW."),
 *   type = "module",
 * )
 */
class IvwIntegration extends AbstractOptionalModule {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $form['ivw_integration']['ivw_site'] = [
      '#type' => 'textfield',
      '#title' => t('IVW Site name'),
      '#description' => t('Site name as given by IVW, this is used as default for the "st" parameter in the iam_data object'),
    ];

    $form['ivw_integration']['mobile_site'] = [
      '#type' => 'textfield',
      '#title' => t('IVW Mobile Site name'),
      '#description' => t('Mobile site name as given by IVW, this is used as default for the "st" parameter in the iam_data object'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array $formValues) {

    $this->configFactory->getEditable('ivw_integration.settings')
      ->set('site', (string) $formValues['ivw_site'])
      ->set('mobile_site', (string) $formValues['mobile_site'])
      ->save(TRUE);
  }

}
