<?php

namespace Drupal\recipes_magazin\Plugin\migrate\source;

use Drupal\migrate\MigrateException;
use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\recipes_magazin\CSVFileObject;

/**
 * Source for CSV.
 *
 * If the CSV file contains non-ASCII characters, make sure it includes a
 * UTF BOM (Byte Order Marker) so they are interpreted correctly.
 *
 * @MigrateSource(
 *   id = "recipes_magazin__csv"
 * )
 */
class CSV extends SourcePluginBase {

  /**
   * List of available source fields.
   *
   * Keys are the field machine names as used in field mappings, values are
   * descriptions.
   *
   * @var array
   */
  protected $fields = [];

  /**
   * List of key fields, as indexes.
   *
   * @var array
   */
  protected $keys = [];

  /**
   * The file class to read the file.
   *
   * @var string
   */
  protected $fileClass = '';

  /**
   * The file object that reads the CSV file.
   *
   * @var \SplFileObject
   */
  protected $file = NULL;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);

    // Path is required.
    if (empty($this->configuration['path'])) {
      throw new MigrateException('You must declare the "path" to the source CSV file in your source settings.');
    }

    // Key field(s) are required.
    if (empty($this->configuration['keys'])) {
      throw new MigrateException('You must declare "keys" as a unique array of fields in your source settings.');
    }

    $this->fileClass = empty($configuration['file_class']) ? '\Drupal\recipes_magazin\CSVFileObject' : $configuration['file_class'];
  }

  /**
   * Return a string representing the source file path.
   *
   * @return string
   *   The file path.
   */
  public function __toString() {
    return $this->configuration['path'];
  }

  /**
   * {@inheritdoc}
   */
  public function initializeIterator() {
    // File handler using header-rows-respecting extension of SPLFileObject.
    $module_handler = $this->getModuleHandler();
    $migration_info = $this->migration->getPluginDefinition();
    $module = $module_handler->getModule($migration_info['provider']);
    $this->file = new $this->fileClass(DRUPAL_ROOT . '/' . $module->getPath() . '/' . $this->configuration['path']);

    // Set basics of CSV behavior based on configuration.
    $delimiter = !empty($this->configuration['delimiter']) ? $this->configuration['delimiter'] : ',';
    $enclosure = !empty($this->configuration['enclosure']) ? $this->configuration['enclosure'] : '"';
    $escape = !empty($this->configuration['escape']) ? $this->configuration['escape'] : '\\';
    $this->file->setCsvControl($delimiter, $enclosure, $escape);

    // Figure out what CSV column(s) to use. Use either the header row(s) or
    // explicitly provided column name(s).
    if (!empty($this->configuration['header_row_count'])) {
      $this->file->setHeaderRowCount($this->configuration['header_row_count']);

      // Find the last header line.
      $this->file->rewind();
      $this->file->seek($this->file->getHeaderRowCount() - 1);

      $row = $this->file->current();
      foreach ($row as $header) {
        $header = trim($header);
        $column_names[] = [$header => $header];
      }
      $this->file->setColumnNames($column_names);
    }
    // An explicit list of column name(s) will override any header row(s).
    if (!empty($this->configuration['column_names'])) {
      $this->file->setColumnNames($this->configuration['column_names']);
    }

    return $this->file;
  }

  /**
   * {@inheritdoc}
   */
  public function getIDs() {
    $ids = [];
    foreach ($this->configuration['keys'] as $delta => $value) {
      if (is_array($value)) {
        $ids[$delta] = $value;
      }
      else {
        $ids[$value]['type'] = 'string';
      }
    }
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [];
    foreach ($this->getIterator()->getColumnNames() as $column) {
      $fields[key($column)] = reset($column);
    }

    // Any caller-specified fields with the same names as extracted fields will
    // override them; any others will be added.
    if (!empty($this->configuration['fields'])) {
      $fields = $this->configuration['fields'] + $fields;
    }

    return $fields;
  }

}
