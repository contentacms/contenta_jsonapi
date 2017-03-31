<?php

namespace Drupal\thunder\Plugin\Thunder\OptionalModule;

use Drupal\Core\Form\FormStateInterface;

/**
 * Harbourmaster.
 *
 * @ThunderOptionalModule(
 *   id = "harbourmaster",
 *   label = @Translation("Harbourmaster SSO connector"),
 *   description = @Translation("Harbourmaster is providing a single sign-on solution."),
 *   type = "module",
 * )
 */
class Harbourmaster extends AbstractOptionalModule {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $form['harbourmaster']['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Integrates Harbourmaster providing a single sign-on solution for Drupal. You will need an instance of the harbourmaster running. See <a href=":url" target="_blank">harbourmaster documentation</a> for more details.', [
        ':url' => 'https://valiton.github.io/harbourmaster-docs/index.html',
      ]),
    ];

    return $form;
  }

}
