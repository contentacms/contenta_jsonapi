<?php

namespace Drupal\thunder\Plugin\Thunder\OptionalModule;

use Drupal\Core\Form\FormStateInterface;

/**
 * AMP.
 *
 * @ThunderOptionalModule(
 *   id = "thunder_liveblog",
 *   label = @Translation("Liveblog"),
 *   description = @Translation("The Liveblog module allows you to distribute blog posts to thousands of users in realtime."),
 *   type = "module",
 * )
 */
class Liveblog extends AbstractOptionalModule {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $form['thunder_liveblog']['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Register a new account at <a href=":pusher_url" target="_blank">:pusher_url</a>, create a new app and note down your keys and cluster. You can provide them right here or at a later stage on the liveblog settings form.',
        [':pusher_url' => 'http://pusher.com']),
    ];

    $form['thunder_liveblog']['pusher_app_id'] = [
      '#type' => 'textfield',
      '#title' => t('App ID'),
    ];

    $form['thunder_liveblog']['pusher_key'] = [
      '#type' => 'textfield',
      '#title' => t('Key'),
    ];

    $form['thunder_liveblog']['pusher_secret'] = [
      '#type' => 'textfield',
      '#title' => t('Secret'),
    ];

    $form['thunder_liveblog']['pusher_cluster'] = [
      '#type' => 'textfield',
      '#title' => t('Cluster'),
      '#description' => t('The cluster name to connect to. Leave empty for the default cluster: mt1 (US east coast)'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array $formValues) {
    $this->configFactory->getEditable('liveblog.notification_channel.liveblog_pusher')
      ->set('app_id', $formValues['pusher_app_id'])
      ->set('key', $formValues['pusher_key'])
      ->set('secret', $formValues['pusher_secret'])
      ->set('cluster', $formValues['pusher_cluster'])
      ->save(TRUE);

    if ($formValues['pusher_app_id'] && $formValues['pusher_key'] && $formValues['pusher_secret']) {
      $this->configFactory->getEditable('liveblog.settings')
        ->set('notification_channel', 'liveblog_pusher')
        ->save(TRUE);
    }
  }

}
