<?php

namespace Drupal\contenta_jsonapi\Plugin\Contenta\OptionalModule;

use Drupal\Core\Form\FormStateInterface;

/**
 * Contenta JS.
 *
 * @ContentaOptionalModule(
 *   id = "contentajs",
 *   label = @Translation("Contenta JS"),
 *   description = @Translation("Provides server side configuration options for Contenta JS. If you use Contenta JS you will need this module."),
 *   type = "module",
 *   standardlyEnabled = true,
 * )
 */
class ContentaJs extends AbstractOptionalModule {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $form['contentajs']['project_info'] = [
      '#type' => 'item',
      '#description' => $this->t("This module provides a JSON-RPC endpoint that
      makes auto-configuration of Contenta JS possible."),
    ];

    return $form;
  }

}
