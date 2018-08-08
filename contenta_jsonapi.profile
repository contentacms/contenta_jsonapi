<?php

/**
 * @file
 * Enables modules and site configuration for a api first site installation.
 */

use Drupal\contenta_jsonapi\Plugin\Contenta\OptionalModule\AbstractOptionalModule;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Serialization\Yaml;

/**
 * Implements hook_form_FORM_ID_alter() for install_configure_form().
 *
 * Allows the profile to alter the site configuration form.
 */
function contenta_jsonapi_form_install_configure_form_alter(&$form, FormStateInterface $form_state) {
  // Add a value as example that one can choose an arbitrary site name.
  $form['site_information']['site_name']['#placeholder'] = t('Contenta JSON API');
}

/**
 * Implements hook_install_tasks().
 */
function contenta_jsonapi_install_tasks(&$install_state) {
  $tasks = [
    '_contenta_jsonapi_generate_keys' => [
      'display_name' => t('Generate OAuth 2 keys'),
    ],
    '_contenta_jsonapi_enable_cors' => [
      'display_name' => t('Enable CORS by default'),
    ],
    'contenta_jsonapi_module_configure_form' => [
      'display_name' => t('Configure additional modules'),
      'type' => 'form',
      'function' => 'Drupal\contenta_jsonapi\Installer\Form\ModuleConfigureForm',
    ],
    'contenta_jsonapi_module_install' => [
      'display_name' => t('Install additional modules'),
    ],
  ];

  return $tasks;
}

/**
 * Generates the OAuth Keys.
 */
function _contenta_jsonapi_generate_keys() {
  // Build all the dependencies manually to avoid having to rely on the
  // container to be ready.
  $dir_name = 'keys';
  /** @var \Drupal\simple_oauth\Service\KeyGeneratorService $key_gen */
  $key_gen = \Drupal::service('simple_oauth.key.generator');
  /** @var \Drupal\simple_oauth\Service\Filesystem\Filesystem $file_system */
  $file_system = \Drupal::service('simple_oauth.filesystem');
  /** @var \Drupal\Core\Logger\LoggerChannelInterface $logger */
  $logger = \Drupal::service('logger.channel.contentacms');

  $relative_path = DRUPAL_ROOT . '/../' . $dir_name;
  if (!$file_system->isDirectory($relative_path)) {
    $file_system->mkdir($relative_path);
  }
  $keys_path = $file_system->realpath($relative_path);
  $pub_filename = sprintf('%s/public.key', $keys_path);
  $pri_filename = sprintf('%s/private.key', $keys_path);

  if ($file_system->fileExist($pub_filename) && $file_system->fileExist($pri_filename)) {
    // 1. If the file already exists, then just set the correct permissions.
    $file_system->chmod($pub_filename, 0600);
    $file_system->chmod($pri_filename, 0600);
    $logger->info('Key pair for OAuth 2 token signing already exists.');
  }
  else {
    // 2. Generate the pair in the selected directory.
    try {
      $key_gen->generateKeys($keys_path);
    } catch (\Exception $e) {
      // Unable to generate files after all.
      $logger->error($e->getMessage());
      return;
    }
  }
}

/**
 * Installs the contenta_jsonapi modules.
 *
 * @param array $install_state
 *   The install state.
 */
function contenta_jsonapi_module_install(array &$install_state) {
  set_time_limit(0);

  $extensions = $install_state['contenta_jsonapi_additional_modules'];
  $form_values = $install_state['form_state_values'];

  $optional_modules_manager = \Drupal::service('plugin.manager.contenta_jsonapi.optional_modules');
  $definitions = array_map(function ($extension_name) use ($optional_modules_manager) {
    return $optional_modules_manager->getDefinition($extension_name);
  }, $extensions);
  $modules = array_filter($definitions, function (array $definition) {
    return $definition['type'] == 'module';
  });
  $themes = array_filter($definitions, function (array $definition) {
    return $definition['type'] == 'theme';
  });
  if (!empty($modules)) {
    /** @var \Drupal\Core\Extension\ModuleInstallerInterface $installer */
    $installer = \Drupal::service('module_installer');
    $installer->install(array_map(function (array $module) {
      return $module['id'];
    }, $modules));
  }
  if (!empty($themes)) {
    /** @var \Drupal\Core\Extension\ModuleInstallerInterface $installer */
    $installer = \Drupal::service('theme_installer');
    $installer->install(array_map(function (array $theme) {
      return $theme['id'];
    }, $themes));
  }
  $instances = array_map(function ($extension_name) use ($optional_modules_manager) {
    return $optional_modules_manager->createInstance($extension_name);
  }, $extensions);
  array_walk($instances, function (AbstractOptionalModule $instance) use ($form_values) {
    $instance->submitForm($form_values);
  });
}

/**
 * Alters the services.yml to enable CORS by default.
 */
function _contenta_jsonapi_enable_cors() {
  // Enable CORS for localhost.
  /** @var \Drupal\Core\DrupalKernelInterface $drupal_kernel */
  $drupal_kernel = \Drupal::service('kernel');
  $file_path = $drupal_kernel->getAppRoot() . '/' . $drupal_kernel->getSitePath();
  $filename = $file_path . '/services.yml';
  if (file_exists($filename)) {
    $services_yml = file_get_contents($filename);

    $yml_data = Yaml::decode($services_yml);
    if (empty($yml_data['parameters']['cors.config']['enabled'])) {
      $yml_data['parameters']['cors.config']['enabled'] = TRUE;
      $yml_data['parameters']['cors.config']['allowedHeaders'] = ['*'];
      $yml_data['parameters']['cors.config']['allowedMethods'] = ['*'];
      $yml_data['parameters']['cors.config']['allowedOrigins'] = ['localhost'];
      $yml_data['parameters']['cors.config']['allowedOriginsPatterns'] = ['/localhost:\d+/'];

      file_put_contents($filename, Yaml::encode($yml_data));
    }
  }
}
