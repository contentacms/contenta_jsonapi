<?php

namespace Drupal\contenta_jsonapi\Plugin\Contenta\OptionalModule;

use Drupal\Core\Form\FormStateInterface;

/**
 * Recipes Magazine.
 *
 * @ContentaOptionalModule(
 *   id = "recipes_magazin",
 *   label = @Translation("Recipes Magazine"),
 *   description = @Translation("The Recipes Magazine module adds a content model and default content for the Umami demo."),
 *   type = "module",
 *   weight = 10,
 *   standardlyEnabled = true,
 * )
 */
class RecipesMagazin extends AbstractOptionalModule {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $form['recipes_magazin']['project_info'] = [
      '#type' => 'item',
      '#description' => $this->t("By installing the demo content Contenta will
        create a set of content types and populate them with real data. You can
        remove the demo content and the associated content types with a single
        click whenever you want."),
    ];

    return $form;
  }

}
