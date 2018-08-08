<?php

namespace Drupal\contenta_jsonapi\Plugin\Contenta\OptionalModule;

use Drupal\Core\Form\FormStateInterface;

/**
 * Recipes Magazine.
 *
 * @ContentaOptionalModule(
 *   id = "consumer_image_styles",
 *   label = @Translation("Consumer Image Styles"),
 *   description = @Translation("Consumer Image Styles integrates with JSON API to provide image styles to your images in your decoupled project. Use this module if you need image styles when working with decoupled Drupal using the JSON API module."),
 *   type = "module",
 * )
 */
class ConsumerImageStyles extends AbstractOptionalModule {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $form['consumer_image_styles']['project_info'] = [
      '#type' => 'item',
      '#description' => $this->t("This is a lower budget alternative to services
      like Cloudinary, Akamai Image Converter, etc. This module will cover your
      needs in 90% of the decoupled projects."),
    ];

    return $form;
  }

}
