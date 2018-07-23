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
 *   id = "recipes_magazin__prepend"
 * )
 */
class Prepend extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    // Prefix is required.
    if (empty($this->configuration['prefix'])) {
      throw new MigrateException('You must provide a "prefix" to prepend to the field.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (is_array($value)) {
      $callback = function ($item) use ($migrate_executable, $row, $destination_property) {
        return $this->transform(
          $item,
          $migrate_executable,
          $row,
          $destination_property
        );
      };
      return array_map($callback, $value);
    }
    if (!is_string($value)) {
      throw new MigrateException('Prepend can only deal with strings or array of strings.');
    }
    return sprintf('%s%s', $this->configuration['prefix'], $value);
  }


}
