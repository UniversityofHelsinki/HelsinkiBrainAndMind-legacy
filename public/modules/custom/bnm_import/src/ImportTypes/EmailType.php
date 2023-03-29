<?php

namespace Drupal\bnm_import\ImportTypes;

/**
 *
 */
class EmailType extends ImportType {

  protected $value;

  /**
   *
   */
  public function __construct($data, $field = []) {
    if (!$this->isValidEmail($data)) {
      throw new \Exception('Not a valid email address');
    }
    $this->value = $data;
  }

  /**
   *
   */
  public function getValue() {
    return $this->value;
  }

  /**
   *
   */
  private function isValidEmail($data) {
    return filter_var($data, FILTER_VALIDATE_EMAIL);
  }

}
