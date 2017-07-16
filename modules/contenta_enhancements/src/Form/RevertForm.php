<?php

namespace Drupal\contenta_enhancements\Form;

use Drupal\Core\Config\Config;
use Drupal\Core\Extension\ModuleInstallerInterface;
use Drupal\Core\Extension\ThemeInstallerInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RevertForm extends ConfirmFormBase {

  /**
   * @var \Drupal\Core\Extension\ModuleInstallerInterface
   */
  protected $moduleInstaller;

  /**
   * @var \Drupal\Core\Extension\ThemeInstallerInterface
   */
  protected $themeInstaller;

  /**
   * @var \Drupal\Core\Config\Config
   */
  protected $themeConfig;

  /**
   * RevertForm constructor.
   *
   * @param \Drupal\Core\Extension\ModuleInstallerInterface $module_installer
   * @param \Drupal\Core\Extension\ModuleInstallerInterface $module_installer
   * @param \Drupal\Core\Config\Config $theme_config
   */
  public function __construct(ModuleInstallerInterface $module_installer, ThemeInstallerInterface $theme_installer, Config $theme_config) {
    $this->moduleInstaller = $module_installer;
    $this->themeInstaller = $theme_installer;
    $this->themeConfig = $theme_config;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('module_installer'),
      $container->get('theme_installer'),
      $container->get('config.factory')->getEditable('system.theme')
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
    $this->themeConfig->set('default', 'material_admin');
    $this->themeConfig->save();
    $this->themeInstaller->uninstall(['materialize_contenta', 'materialize']);
    $this->moduleInstaller->uninstall(['recipes_magazin', 'recipes_magazin_contenta']);
    drupal_set_message($this->t('Contenta has successfully reverted to a clean state!'));
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('This action will remove all the default content and the front-end theme. Are you sure you want to proceed?');
  }

  public function getCancelUrl() {
    return new Url('<front>');
  }

}
