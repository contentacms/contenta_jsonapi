<?php

namespace Drupal\recipes_magazin\Plugin\migrate\source;

/**
 * Extends the CSV migration to make it possible to import terms.
 *
 * @MigrateSource(
 *   id = "recipes_magazin__terms"
 * )
 */
class Terms extends CSV {

  /**
   * {@inheritdoc}
   */
  public function initializeIterator() {
    $values = [];
    foreach (parent::initializeIterator() as $row) {
      if (isset($row[$this->configuration['column']])) {
        $terms = explode(',', $row[$this->configuration['column']]);
        foreach (array_filter(array_map('trim', $terms)) as $single_term) {
          $values[$single_term] = [
            $this->configuration['column'] => $single_term,
          ];
        }
      }
    }
    return new \ArrayIterator($values);
  }

}
