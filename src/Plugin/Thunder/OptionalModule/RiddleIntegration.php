<?php

namespace Drupal\thunder\Plugin\Thunder\OptionalModule;

use Drupal\Core\Form\FormStateInterface;

/**
 * Riddle integration.
 *
 * @ThunderOptionalModule(
 *   id = "paragraphs_riddle_marketplace",
 *   label = @Translation("Riddle integration"),
 *   description = @Translation("Riddle makes it easy to quickly create beautiful and highly shareable quizzes, tests, lists, polls, and more."),
 *   type = "module",
 * )
 */
class RiddleIntegration extends AbstractOptionalModule {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $form['paragraphs_riddle_marketplace']['riddle_token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Riddle token'),
      '#description' => $this->t('Register a new account at <a href=":riddle" target="_blank">riddle.com</a> and get a token from the Account->Plugins page (you may need to reset to get the first token). To get a free riddle basic account use this voucher "THUNDER_3eX4_freebasic".',
        [':riddle' => 'http://www.riddle.com']),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array $formValues) {

    $this->configFactory->getEditable('riddle_marketplace.settings')
      ->set('riddle_marketplace.token', (string) $formValues['riddle_token'])
      ->save(TRUE);

  }

}
