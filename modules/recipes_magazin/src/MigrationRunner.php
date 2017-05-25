<?php

namespace Drupal\recipes_magazin;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;

/**
 * Runs the migration on demand.
 */
class MigrationRunner {

  /**
   * The migration plugin manager.
   *
   * @var \Drupal\migrate\Plugin\MigrationPluginManagerInterface
   */
  protected $manager;

  /**
   * MigrationRunner constructor.
   *
   * @param \Drupal\migrate\Plugin\MigrationPluginManagerInterface $manager
   *   The migration plugin manager.
   */
  public function __construct(MigrationPluginManagerInterface $manager) {
    $this->manager = $manager;
  }

  /**
   * Import content from migrations.
   *
   * @param string[] $migration_ids
   *   The list of migrations to run.
   */
  public function run(array $migration_ids) {
    $this->execute($migration_ids, 'import');
  }

  /**
   * Remove content from migrations.
   *
   * @param string[] $migration_ids
   *   The list of migrations to run.
   */
  public function remove(array $migration_ids) {
    $this->execute($migration_ids, 'rollback');
  }

  /**
   * Import or remove content from migrations.
   *
   * @param string[] $migration_ids
   *   The list of migrations to run.
   * @param string $method_name
   *   The method to execute: import or rollback.
   */
  protected function execute(array $migration_ids, $method_name) {
    array_walk($migration_ids, function ($migration_id) use ($method_name) {
      /** @var \Drupal\migrate\Plugin\Migration $migration */
      if (!$migration = $this->manager->createInstance($migration_id)) {
        throw new PluginNotFoundException($migration_id);
      }
      $migrate_executable = (new MigrateExecutable($migration, new MigrateMessage()));
      call_user_func_array([$migrate_executable, $method_name], []);
    });
  }

}
