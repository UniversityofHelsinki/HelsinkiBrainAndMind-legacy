<?php

/**
 * @file
 * Contains \Drupal\bnm_migrate_units\Plugin\migrate\process\ParentTerm.
 */


namespace Drupal\bnm_migrate_units\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * @MigrateProcessPlugin(
 *   id = "parentterm",
 * )
 */
class ParentTerm extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $levels = $this->configuration['source'];
    $parent = 0;
    var_dump($value,$levels); exit;
    foreach ($levels as $level => $name) {

    }
  }
}
