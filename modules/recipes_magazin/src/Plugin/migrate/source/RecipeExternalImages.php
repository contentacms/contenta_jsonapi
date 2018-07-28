<?php

namespace Drupal\recipes_magazin\Plugin\migrate\source;

use Drupal\Component\Utility\UrlHelper;

/**
 * Extends the CSV migration to make it possible to import terms.
 *
 * @MigrateSource(
 *   id = "recipes_magazin__external_images"
 * )
 */
class RecipeExternalImages extends CSV {

  /**
   * {@inheritdoc}
   */
  public function initializeIterator() {
    $values = [];
    foreach (parent::initializeIterator() as $row) {
      if (isset($row[$this->configuration['column']])) {
        foreach (explode(',', $row[$this->configuration['column']]) as $single_image_url) {
          if (UrlHelper::isExternal($single_image_url)) {
            // Some URLs are duplicated, as recipes are more or less a clone of
            // another one. Therefore we key by URL here, so migrate doesn't
            // believe we have duplicate rows.
            $values[$single_image_url] = [
              $this->configuration['column'] => $single_image_url,
            ];
          }
        }
      }
    }
    return new \ArrayIterator($values);
  }

}
