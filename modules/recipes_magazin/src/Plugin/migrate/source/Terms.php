<?php

namespace Drupal\recipes_magazin\Plugin\migrate\source;

/**
 * Extends the CSV migration to make it possible to import terms.
 *
 * @MigrateSource(
 *   id = "recipe_magazin__terms"
 * )
 */
class Terms extends CSV {

  /**
   * {@inheritdoc}
   */
  public function initializeIterator() {
    foreach (parent::initializeIterator() as $row) {
      if (isset($row[$this->configuration['column']])) {
        foreach (explode(',', $row[$this->configuration['column']]) as $single_term) {
          yield [
            $this->configuration['column'] => $single_term,
          ];
        }
      }
    }
  }

}
