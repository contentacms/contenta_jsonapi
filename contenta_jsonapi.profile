<?php

/**
 * @file
 * Enables modules and site configuration for a api first site installation.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter() for install_configure_form().
 *
 * Allows the profile to alter the site configuration form.
 */
function contenta_jsonapi_form_install_configure_form_alter(&$form, FormStateInterface $form_state) {
  // Add a value as example that one can choose an arbitrary site name.
  $form['site_information']['site_name']['#placeholder'] = t('Contenta JSON API');

  $form['contenta_jsonapi'] = [
    '#type' => 'fieldset',
    '#title' => t('Contenta settings'),
    '#weight' => -10,
  ];
    
  $form['contenta_jsonapi']['include_recipes_magazin'] = [
    '#title' => t('Include the recipes magazin'),
    '#type' => 'checkbox',
  ];

  $form['#submit'][] = 'contenta_jsonapi_install_configure_form_submit';
}

/**
 * Submit handler for install_configure_form().
 */
function contenta_jsonapi_install_configure_form_submit(&$form, FormStateInterface $form_state) {
  if ($form_state->getValue('include_recipes_magazin')) {
    drupal_set_message(t('Recipe magazin installed'));
    \Drupal::service('module_installer')->install(['recipes_magazin']);
  }
}
