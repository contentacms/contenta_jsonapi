<?php

namespace Drupal\contenta_jsonapi\Plugin\Contenta\OptionalModule;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

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

    $link = Link::fromTextAndUrl(
      'GraphQL integration in Contenta JS',
      Url::fromUri('https://github.com/contentacms/contentajs-graphql')
    )->toString();
    $form['contenta_graphql']['project_info'] = [
      '#type' => 'item',
      '#description' => $this->t(
        "Contenta CMS is primarily focused on JSON API. If you want to expose a
        GraphQL API consider looking at the @link.",
        ['@link' => $link]
      ),
      '#disabled' => TRUE,
    ];

    return $form;
  }

}
