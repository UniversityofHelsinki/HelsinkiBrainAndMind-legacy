<?php

namespace Drupal\bnm_import\ImportTypes;

/**
 * Import type.
 */
abstract class ImportType {

  /**
   * Value.
   *
   * @var mixed
   */
  protected $value;

  /**
   * Constructor validates and sets the $value. In case of invalid value throw an exception.
   *
   * @param mixed $value
   *   Value.
   *
   * @throws \Exception
   */
  abstract public function __construct($data, $field);

  /**
   * Get value.
   *
   * @return mixed
   *   Value.
   */
  abstract public function getValue();

  /**
   * Tostring.
   *
   * @return string
   *   Value.
   */
  public function __toString() {
    return (string) $this->value;
  }

}
