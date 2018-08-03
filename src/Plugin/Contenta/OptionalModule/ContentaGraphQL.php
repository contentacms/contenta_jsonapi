<?php

namespace Drupal\contenta_jsonapi\Plugin\Contenta\OptionalModule;

use Drupal\Core\Form\FormStateInterface;

/**
 * GraphQL.
 *
 * @ContentaOptionalModule(
 *   id = "contenta_graphql",
 *   label = @Translation("Contenta GraphQL"),
 *   description = @Translation("A GraphQL API inside of Drupal."),
 *   type = "module",
 * )
 */
class ContentaGraphQL extends AbstractOptionalModule {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $form['contenta_graphql']['project_info'] = [
      '#type' => 'item',
      '#description' => $this->t("Contenta CMS is primarily focused on JSON API.
        If you want to expose a GraphQL API consider looking at the GraphQL
        integration in Contenta JS. If you still want to install GraphQL inside
        of Drupal select this module."),
    ];

    return $form;
  }

}
