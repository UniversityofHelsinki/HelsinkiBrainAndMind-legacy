<?php

/**
 * @file
 * Contains \Drupal\bnm_migrate_units\Plugin\migrate\process\NameTerm.
 */


namespace Drupal\bnm_migrate_units\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * @MigrateProcessPlugin(
 *   id = "nameterm",
 * )
 */
class NameTerm extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    var_dump($value); exit;
    return $value;
  }
}
