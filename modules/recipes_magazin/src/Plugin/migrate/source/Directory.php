<?php

namespace Drupal\recipes_magazin\Plugin\migrate\source;

use Drupal\migrate\MigrateException;
use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\migrate\Plugin\MigrationInterface;

/**
 * Source for a given directory path.
 *
 * @MigrateSource(
 *   id = "recipes_magazin__dir"
 * )
 */
class Directory extends SourcePluginBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);

    // Path is required.
    if (empty($this->configuration['path'])) {
      throw new MigrateException('You must declare the "path" to search for files in your source settings.');
    }
  }

  /**
   * Return a string representing the source file path.
   *
   * @return string
   *   The file path.
   */
  public function __toString() {
    return implode(',', $this->configuration['path']);
  }

  /**
   * {@inheritdoc}
   */
  public function initializeIterator() {
    $module_handler = $this->getModuleHandler();
    $migration_info = $this->migration->getPluginDefinition();
    $module = $module_handler->getModule($migration_info['provider']);
    $it = new \DirectoryIterator(DRUPAL_ROOT . '/' . $module->getPath() . '/' . $this->configuration['path']);
    foreach ($it as $fileinfo) {
      if (!$fileinfo->isDot() && !$fileinfo->isDir()) {
        $path = $fileinfo->getPath();
        $filename = $fileinfo->getFilename();
        $pathname = $path . '/' . $filename;

        if (empty($this->configuration['file_ext']) || $fileinfo->getExtension() == $this->configuration['file_ext']) {
          yield [
            'path' => $path,
            'filename' => $filename,
            'pathname' => $pathname,
          ];
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getIDs() {
    return ['filename' => ['type' => 'string']];
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return ['path', 'filename', 'pathname'];
  }

}
