<?php

namespace Drupal\recipes_magazin\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Extends the CSV migration to make it possible to import terms.
 *
 * @MigrateProcessPlugin(
 *   id = "recipe_magazin__existing_user"
 * )
 */
class ExistingUser extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $users = \Drupal::service('entity_type.manager')->getStorage('user')->loadByProperties(['name' => $value]);
    $user = reset($users);
    /** @var \Drupal\Core\Logger\LoggerChannelInterface $logger */
    $logger = \Drupal::service('logger.channel.contentacms');
    var_dump($value);
    $logger->info($value);
    return empty($user) ? $value : 0;
  }


}
