<?php

/**
 * @file
 * Contains \Drupal\composer\ScriptHandler.
 */

namespace Drupal\composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;
use Composer\Util\ProcessExecutor;

class ScriptHandler {

  protected static function getDrupalRoot($project_root) {
    return $project_root .  '/web';
  }

  public static function buildScaffold(Event $event) {
    $fs = new Filesystem();
    if (!$fs->exists(static::getDrupalRoot(getcwd()) . '/autoload.php')) {
      \DrupalComposer\DrupalScaffold\Plugin::scaffold($event);
    }
  }

  public static function createRequiredFiles(Event $event) {
    $fs = new Filesystem();
    $root = static::getDrupalRoot(getcwd());

    $dirs = [
      'modules',
      'profiles',
      'themes',
    ];

    // Required for unit testing
    foreach ($dirs as $dir) {
      if (!$fs->exists($root . '/'. $dir)) {
        $fs->mkdir($root . '/'. $dir);
        $fs->touch($root . '/'. $dir . '/.gitkeep');
      }
    }


    // Prepare the settings file for installation
    if (!$fs->exists($root . '/sites/default/settings.php')) {
      $fs->chmod($root . '/sites/default/', 0755);
      $fs->copy($root . '/sites/default/default.settings.php', $root . '/sites/default/settings.php');
      $fs->chmod($root . '/sites/default/settings.php', 0666);
      $event->getIO()->write("Create a sites/default/settings.php file with chmod 0666");
    }

    // Prepare the services file for installation
    if (!$fs->exists($root . '/sites/default/services.yml')) {
      $fs->chmod($root . '/sites/default/', 0755);
      $fs->copy($root . '/sites/default/default.services.yml', $root . '/sites/default/services.yml');
      $fs->chmod($root . '/sites/default/services.yml', 0666);
      $event->getIO()->write("Create a sites/default/services.yml file with chmod 0666");
    }

    // Create the files directory with chmod 0777
    if (!$fs->exists($root . '/sites/default/files')) {
      $oldmask = umask(0);
      $fs->mkdir($root . '/sites/default/files', 0777);
      umask($oldmask);
      $event->getIO()->write("Create a sites/default/files directory with chmod 0777");
    }
  }

  public static function dependencyCleanup() {
    $fs = new Filesystem();
    $root = getcwd();

    $directories = array(
      "bin",
      "web/core",
      "web/libraries",
      "web/modules/contrib",
      "web/profiles/contrib",
      "web/themes/contrib",
      "drush/contrib",
      "vendor",
    );

    $directories = array_map(function ($directory) use ($root) {
      return $root.'/'.$directory;
    }, $directories);

    $fs->remove($directories);

    echo "(!) Now you can run 'composer install' to get the latest dependencies.";

  }

  /**
   * Moves front-end libraries to Drupal's installed directory.
   *
   * @param \Composer\Script\Event $event
   *   The script event.
   */
  public static function deployLibraries(Event $event) {
    $extra = $event->getComposer()->getPackage()->getExtra();
    if (isset($extra['installer-paths'])) {
      foreach ($extra['installer-paths'] as $path => $criteria) {
        if (array_intersect(['drupal-http-apis/contenta_jsonapi', 'type:drupal-profile'], $criteria)) {
          $working_path = $path;
        }
      }
      if (isset($working_path)) {
        // We replace any possible name with the distribution name.
        $working_path = str_replace('{$name}', 'contenta_jsonapi', $working_path);
        $executor = new ProcessExecutor($event->getIO());
        $output = NULL;
        // Execute npm install in the profile folder.
        $executor->execute('npm install', $output, $working_path);
        $event->getIO()->write($output);
      }
    }
  }
}
