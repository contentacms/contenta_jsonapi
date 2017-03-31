<?php

namespace Drupal\thunder\Plugin\Thunder\OptionalModule;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Google Analytics.
 *
 * @ThunderOptionalModule(
 *   id = "google_analytics",
 *   label = @Translation("Google Analytics"),
 *   description = @Translation("Google Analytics lets you measure your advertising ROI as well as track your video, and social networking sites and applications."),
 *   type = "module",
 * )
 */
class GoogleAnalytics extends AbstractOptionalModule {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $form['google_analytics']['ga_account'] = [
      '#description' => t('This ID is unique to each site you want to track separately, and is in the form of UA-xxxxxxx-yy. To get a Web Property ID, <a href=":analytics" target="_blank">register your site with Google Analytics</a>, or if you already have registered your site, go to your Google Analytics Settings page to see the ID next to every site profile. <a href=":webpropertyid"  target="_blank">Find more information in the documentation</a>.', [
        ':analytics' => 'http://www.google.com/analytics/',
        ':webpropertyid' => Url::fromUri('https://developers.google.com/analytics/resources/concepts/gaConceptsAccounts', ['fragment' => 'webProperty'])
          ->toString(),
      ]),
      '#maxlength' => 20,
      '#placeholder' => 'UA-',
      '#size' => 15,
      '#title' => t('Web Property ID'),
      '#type' => 'textfield',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array $formValues) {

    $this->configFactory->getEditable('google_analytics.settings')
      ->set('account', (string) $formValues['ga_account'])
      ->save(TRUE);
  }

}
