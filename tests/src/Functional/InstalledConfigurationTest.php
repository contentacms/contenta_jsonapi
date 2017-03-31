<?php

namespace Drupal\Tests\thunder\Functional;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\Schema\SchemaCheckTrait;
use Drupal\thunder\ThunderBaseTest;

/**
 * Test for checking of configuration after install of thunder profile.
 *
 * @package Drupal\Tests\thunder\Kernel
 *
 * @group ThunderConfig
 */
class InstalledConfigurationTest extends ThunderBaseTest {

  use SchemaCheckTrait;

  /**
   * Modules to enable.
   *
   * The test runner will merge the $modules lists from this class, the class
   * it extends, and so on up the class hierarchy. It is not necessary to
   * include modules in your list that a parent class has already declared.
   *
   * @var string[]
   *
   * @see \Drupal\Tests\BrowserTestBase::installDrupal()
   */
  protected static $modules = [
    'thunder_demo',
    'google_analytics',
    'nexx_integration',
    'ivw_integration',
    'adsense',

    // Additional modules.
    // 'thunder_fia',
    // 'paragraphs_riddle_marketplace',
    // Simple_gmap module. Issue: https://www.drupal.org/node/2859165
    // 'thunder_liveblog',
    // There is already commit that should be pushed to drupal.org HM sandbox.
    // 'harbourmaster',
    // end of list.
  ];

  /**
   * Theme name that will be used on installation of test.
   *
   * @var string
   */
  protected $defaultTheme = 'stable';

  /**
   * Ignore list of Core related configurations.
   *
   * @var array
   */
  protected static $ignoreCoreConfigs = [
    'checklistapi.progress.thunder_updater',
    'thunder_base.settings',
    'system.site',
    'core.extension',
    'system.performance',
    'system.theme',

    // Configs created by User module.
    'system.action.user_add_role_action.administrator',
    'system.action.user_add_role_action.editor',
    'system.action.user_add_role_action.seo',
    'system.action.user_remove_role_action.administrator',
    'system.action.user_remove_role_action.editor',
    'system.action.user_remove_role_action.seo',
    'system.action.user_add_role_action.harbourmaster',
    'system.action.user_remove_role_action.harbourmaster',

    // Configs created by Token module.
    'core.entity_view_mode.access_token.token',
    'core.entity_view_mode.block.token',
    'core.entity_view_mode.crop.token',
    'core.entity_view_mode.file.token',
    'core.entity_view_mode.menu_link_content.token',
    'core.entity_view_mode.node.token',
    'core.entity_view_mode.paragraph.token',
    'core.entity_view_mode.taxonomy_term.token',
    'core.entity_view_mode.user.token',

    // Core Tour/Language.
    'tour.tour.language',
    'tour.tour.language-add',
    'tour.tour.language-edit',
  ];

  /**
   * Ignore custom keys that are changed during installation process.
   *
   * @var array
   */
  protected static $ignoreConfigKeys = [
    // Node settings is changed by Thunder Install hook.
    'node.settings' => [
      'use_admin_theme' => TRUE,
    ],

    // It's not exported in Yaml, so that new key is generated.
    'scheduler.settings' => [
      'lightweight_cron_access_key' => TRUE,
    ],

    // Changed on installation.
    'system.date' => [
      'timezone' => [
        'default' => TRUE,
      ],
    ],

    // Changed on installation.
    'system.file' => [
      'path' => [
        'temporary' => TRUE,
      ],
    ],

    // Changed on installation.
    'update.settings' => [
      'notification' => [
        'emails' => TRUE,
      ],
    ],

    // Changed on Testing.
    'system.logging' => [
      'error_level' => TRUE,
    ],

    // Changed on Testing.
    'system.mail' => [
      'interface' => ['default' => TRUE],
    ],

    // User register is changed by Thunder Install hook.
    'user.settings' => [
      'register' => TRUE,
    ],

    // Media view status is changed by Thunder Install hook.
    'views.view.media' => [
      'dependencies' => [
        'config' => TRUE,
      ],
      'status' => TRUE,
    ],

    // Changed on installation.
    'views.view.glossary' => [
      'dependencies' => [
        'config' => TRUE,
      ],
    ],

    // Changed on installation.
    'views.view.content_recent' => [
      'display' => [
        'block_1' => ['cache_metadata' => ['max-age' => TRUE]],
        'default' => ['cache_metadata' => ['max-age' => TRUE]],
      ],
    ],

    // Diff Module: changed on installation of module when additional library
    // exists on system: mkalkbrenner/php-htmldiff-advanced.
    'diff.settings' => [
      'general_settings' => [
        'layout_plugins' => [
          'visual_inline' => [
            'enabled' => TRUE,
          ],
        ],
      ],
    ],

    // Infinite Theme - adjusted by Thunder hooks.
    'infinite.settings' => [
      'logo' => TRUE,
    ],

    // Infinite Theme - adjusted by Shariff module hooks.
    'core.entity_view_display.node.article.teaser_landscape_l' => [
      'hidden' => ['shariff_field' => TRUE],
    ],
    'core.entity_view_display.node.article.teaser_landscape_m' => [
      'hidden' => ['shariff_field' => TRUE],
    ],
    'core.entity_view_display.node.article.teaser_portrait_m' => [
      'hidden' => ['shariff_field' => TRUE],
    ],
    'core.entity_view_display.node.article.teaser_portrait_s' => [
      'hidden' => ['shariff_field' => TRUE],
    ],
    'core.entity_view_display.node.article.teaser_square_m' => [
      'hidden' => ['shariff_field' => TRUE],
    ],
    'core.entity_view_display.node.article.teaser_square_s' => [
      'hidden' => ['shariff_field' => TRUE],
    ],
    'core.entity_view_display.node.article.lazyloading' => [
      'hidden' => ['shariff_field' => TRUE],
    ],
    'core.entity_view_display.node.article.presenter_full' => [
      'hidden' => ['shariff_field' => TRUE],
    ],
    'core.entity_view_display.node.article.presenter_half' => [
      'hidden' => ['shariff_field' => TRUE],
    ],
    'core.entity_view_display.node.article.presenter_home_selectable' => [
      'hidden' => ['shariff_field' => TRUE],
    ],

    // Infinite Theme - changed by Thunder.
    'core.entity_view_display.media.gallery.default' => [
      'content' => [
        'field_media_images' => [
          'settings' => [
            'view_mode' => TRUE,
          ],
        ],
      ],
    ],

    // Infinite Theme - changed by Thunder in order to use Slick gallery.
    'core.entity_view_display.media.image.default' => [
      'content' => [
        'field_image' => [
          'settings' => [
            'image_style' => TRUE,
            'responsive_image_style' => TRUE,
          ],
        ],
      ],
    ],

    // Diff module. Issue: https://www.drupal.org/node/2854581.
    'core.entity_view_mode.node.diff' => [
      'langcode' => TRUE,
    ],

    // The thunder profile changes article and channel taxonomy when ivw module
    // is installed.
    'core.entity_form_display.node.article.default' => [
      'content' => [
        'field_ivw' => TRUE,
      ],
      'dependencies' => [
        'config' => TRUE,
        'module' => TRUE,
      ],
    ],
    'core.entity_form_display.taxonomy_term.channel.default' => [
      'content' => [
        'field_ivw' => TRUE,
      ],
      'dependencies' => [
        'config' => TRUE,
        'module' => TRUE,
      ],
    ],
  ];

  /**
   * List of contribution settings that should be ignored.
   *
   * All these settings exists in module configuration Yaml files, but they are
   * not in sync with configuration that is set after installation.
   *
   * @var array
   */
  protected static $ignoreConfigs = [
    // Slick media module. Issue: https://www.drupal.org/node/2852030
    'core.entity_view_mode.media.slick',

    // Paragraphs module. Issue: https://www.drupal.org/node/2852025
    'core.entity_view_mode.paragraph.preview',

    // Focal Point module. Issue: https://www.drupal.org/node/2851587
    'crop.type.focal_point',

    // Metatag module. Issue: https://www.drupal.org/node/2851582.
    'metatag.metatag_defaults.403',
    'metatag.metatag_defaults.404',
    'metatag.metatag_defaults.front',
    'metatag.metatag_defaults.global',
    'metatag.metatag_defaults.node',
    'metatag.metatag_defaults.taxonomy_term',
    'metatag.metatag_defaults.user',
  ];

  /**
   * Set default theme for test.
   *
   * @param string $defaultTheme
   *   Default Theme.
   */
  protected function setDefaultTheme($defaultTheme) {
    \Drupal::service('theme_installer')->install([$defaultTheme]);

    $themeConfig = \Drupal::configFactory()->getEditable('system.theme');
    $themeConfig->set('default', $defaultTheme);
    $themeConfig->save();
  }

  /**
   * Return cleaned-up configurations provided as argument.
   *
   * @param array $configurations
   *   List of configurations that will be cleaned-up and returned.
   * @param string $configurationName
   *   Configuration name for provided configurations.
   *
   * @return array
   *   Returns cleaned-up configurations.
   */
  protected function cleanupConfigurations(array $configurations, $configurationName) {
    /** @var \Drupal\Core\Config\ExtensionInstallStorage $optionalStorage */
    $optionalStorage = \Drupal::service('config_update.extension_optional_storage');

    $configCleanup = [];

    // Apply ignore for defined configurations and custom properties.
    if (array_key_exists($configurationName, static::$ignoreConfigKeys)) {
      $configCleanup = static::$ignoreConfigKeys[$configurationName];
    }

    // Ignore configuration dependencies in case of optional configuration.
    if ($optionalStorage->exists($configurationName)) {
      $configCleanup = NestedArray::mergeDeep(
        $configCleanup,
        ['dependencies' => TRUE]
      );
    }

    // If configuration doesn't require cleanup, just return configurations as
    // they are.
    if (empty($configCleanup)) {
      return $configurations;
    }

    // Apply cleanup for configurations.
    foreach ($configurations as $index => $arrayToOverwrite) {
      $configurations[$index] = NestedArray::mergeDeep(
        $arrayToOverwrite,
        $configCleanup
      );
    }

    return $configurations;
  }

  /**
   * Compare active configuration with configuration Yaml files.
   */
  public function testInstalledConfiguration() {
    $this->setDefaultTheme($this->defaultTheme);

    /** @var \Drupal\config_update\ConfigReverter $configUpdate */
    $configUpdate = \Drupal::service('config_update.config_update');

    /** @var \Drupal\Core\Config\TypedConfigManager $typedConfigManager */
    $typedConfigManager = \Drupal::service('config.typed');

    $activeStorage = \Drupal::service('config.storage');
    $installStorage = \Drupal::service('config_update.extension_storage');

    /** @var \Drupal\Core\Config\ExtensionInstallStorage $optionalStorage */
    $optionalStorage = \Drupal::service('config_update.extension_optional_storage');

    // Get list of configurations (active, install and optional).
    $activeList = $activeStorage->listAll();
    $installList = $installStorage->listAll();
    $optionalList = $optionalStorage->listAll();

    // Check that all required configurations are available.
    $installListDiff = array_diff($installList, $activeList);
    $this->assertEquals([], $installListDiff, "All required configurations should be installed.");

    // Filter active list.
    $activeList = array_diff($activeList, static::$ignoreCoreConfigs);

    // Check that all active configuration are provided by Yaml files.
    $activeListDiff = array_diff($activeList, $installList, $optionalList);
    $this->assertEquals([], $activeListDiff, "All active configurations should be defined in Yaml files.");

    /** @var \Drupal\config_update\ConfigDiffer $configDiffer */
    $configDiffer = \Drupal::service('config_update.config_diff');

    $differentConfigNames = [];
    $schemaCheckFail = [];
    foreach ($activeList as $activeConfigName) {
      // Skip incorrect configuration from contribution modules.
      if (in_array($activeConfigName, static::$ignoreConfigs)) {
        continue;
      }

      // Get configuration from file and active configuration.
      $activeConfig = $configUpdate->getFromActive('', $activeConfigName);
      $fileConfig = $configUpdate->getFromExtension('', $activeConfigName);

      // Validate fetched configuration against corresponding schema.
      if ($typedConfigManager->hasConfigSchema($activeConfigName)) {
        // Validate active configuration.
        if ($this->checkConfigSchema($typedConfigManager, $activeConfigName, $activeConfig) !== TRUE) {
          $schemaCheckFail['active'][] = $activeConfigName;
        }

        // Validate configuration from file.
        if ($this->checkConfigSchema($typedConfigManager, $activeConfigName, $fileConfig) !== TRUE) {
          $schemaCheckFail['file'][] = $activeConfigName;
        }
      }
      else {
        $schemaCheckFail['no-schema'][] = $activeConfigName;
      }

      // Clean up configuration if it's required.
      list($activeConfig, $fileConfig) = $this->cleanupConfigurations(
        [
          $activeConfig,
          $fileConfig,
        ],
        $activeConfigName
      );

      // Check is active configuration same as in Yaml file.
      if (!$configDiffer->same($fileConfig, $activeConfig)) {
        $differentConfigNames[] = $activeConfigName;
      }
    }

    // Output different configuration names and failed schema checks.
    if (!empty($differentConfigNames) || !empty($schemaCheckFail)) {
      $errorOutput = [
        'configuration-diff' => $differentConfigNames,
        'schema-check' => $schemaCheckFail,
      ];

      throw new \Exception('Configuration difference is found: ' . print_r($errorOutput, TRUE));
    }
  }

}
