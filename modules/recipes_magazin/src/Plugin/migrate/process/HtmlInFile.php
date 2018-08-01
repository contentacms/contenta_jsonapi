<?php

namespace Drupal\recipes_magazin\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Extends the CSV migration to make it possible to import terms.
 *
 * @MigrateProcessPlugin(
 *   id = "recipes_magazin__html_in_file"
 * )
 */
class HtmlInFile extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    // Filename is required.
    if (empty($this->configuration['source'])) {
      throw new MigrateException('You must declare the "source" that contains the filename of the HTML file.');
    }
    // Directory is required.
    if (empty($this->configuration['directory'])) {
      throw new MigrateException('You must declare the "directory" that contains the HTML files.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $path = sprintf(
      '%s/../../../../default_content/%s/%s',
      __DIR__,
      $this->configuration['directory'],
      $row->getSourceProperty($this->configuration['source'])
    );
    if (!file_exists($path)) {
      return null;
    }
    // Read the HTML file and return the contents.
    return file_get_contents($path);
  }


}
