<?php

namespace Drupal\contenta_enhancements\Form;

use Drupal\Core\Config\Config;
use Drupal\Core\Extension\ModuleInstallerInterface;
use Drupal\Core\Extension\ThemeInstallerInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RevertForm extends ConfirmFormBase {

  /**
   * @var \Drupal\Core\Extension\ModuleInstallerInterface
   */
  protected $moduleInstaller;

  /**
   * @var \Drupal\Core\Config\Config
   */
  protected $siteConfig;

  /**
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * RevertForm constructor.
   *
   * @param \Drupal\Core\Extension\ModuleInstallerInterface $module_installer
   * @param \Drupal\Core\Extension\ModuleInstallerInterface $module_installer
   * @param \Drupal\Core\Config\Config $theme_config
   * @param \Drupal\Core\Config\Config $site_config
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   */
  public function __construct(ModuleInstallerInterface $module_installer, Config $site_config, MessengerInterface $messenger) {
    $this->moduleInstaller = $module_installer;
    $this->siteConfig = $site_config;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('module_installer'),
      $container->get('config.factory')->getEditable('system.site'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'contenta_enhancements.revert.confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Use the admin theme in the front-end.
    $this->siteConfig->set('page.front', '/admin/content');
    $this->siteConfig->save();
    $this->moduleInstaller->uninstall(['recipes_magazin']);
    $this->messenger->addMessage($this->t('Contenta has successfully reverted to a clean state!'));
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Revert to clean state?');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('This action will remove all the default content and the front-end theme. Are you sure you want to proceed?');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('<front>');
  }

}
