<?php

namespace Drupal\bnm_import\ImportTypes;

class TextType extends ImportType {

  protected $value;

  public function __construct($data, $field = [])
  {
    $this->value = $data;
  }

  public function getValue()
  {
    return $this->value;
  }

  public function __toString()
  {
    return parent::__toString();
  }

}
