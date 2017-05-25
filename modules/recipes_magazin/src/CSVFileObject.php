<?php

namespace Drupal\recipes_magazin;

/**
 * Defines a CSV file object.
 *
 * @package Drupal\migrate_source_csv.
 *
 * Extends SPLFileObject to:
 * - assume CSV format
 * - skip header rows on rewind()
 * - address columns by header row name instead of index.
 */
class CSVFileObject extends \SplFileObject {

  /**
   * The number of rows in the CSV file before the data starts.
   *
   * @var integer
   */
  protected $headerRowCount = 0;

  /**
   * The human-readable column headers, keyed by column index in the CSV.
   *
   * @var array
   */
  protected $columnNames = [];

  /**
   * {@inheritdoc}
   */
  public function __construct($file_name) {
    // Necessary to use this approach because SplFileObject doesn't like NULL
    // arguments passed to it.
    call_user_func_array(['parent', '__construct'], func_get_args());

    $this->setFlags(CSVFileObject::READ_CSV | CSVFileObject::READ_AHEAD | CSVFileObject::DROP_NEW_LINE | CSVFileObject::SKIP_EMPTY);
  }

  /**
   * {@inheritdoc}
   */
  public function rewind() {
    $this->seek($this->getHeaderRowCount());
  }

  /**
   * {@inheritdoc}
   */
  public function current() {
    $row = parent::current();

    if ($row && !empty($this->columnNames)) {
      // Only use columns specified in the defined CSV columns.
      $row = array_intersect_key($row, $this->columnNames);
      // Set meaningful keys for the columns mentioned in $this->csvColumns.
      foreach ($this->columnNames as $key => $value) {
        // Copy value to more descriptive key and unset original.
        $value = key($value);
        $row[$value] = isset($row[$key]) ? $row[$key] : NULL;
        unset($row[$key]);
      }
    }

    return $row;
  }

  /**
   * Return a count of all available source records.
   */
  public function count() {
    return iterator_count($this);
  }

  /**
   * Number of header rows.
   *
   * @return int
   *   Get the number of header rows, zero if no header row.
   */
  public function getHeaderRowCount() {
    return $this->headerRowCount;
  }

  /**
   * Number of header rows.
   *
   * @param int $header_row_count
   *   Set the number of header rows, zero if no header row.
   */
  public function setHeaderRowCount($header_row_count) {
    $this->headerRowCount = $header_row_count;
  }

  /**
   * CSV column names.
   *
   * @return array
   *   Get CSV column names.
   */
  public function getColumnNames() {
    return $this->columnNames;
  }

  /**
   * CSV column names.
   *
   * @param array $column_names
   *   Set CSV column names.
   */
  public function setColumnNames(array $column_names) {
    $this->columnNames = $column_names;
  }

}
