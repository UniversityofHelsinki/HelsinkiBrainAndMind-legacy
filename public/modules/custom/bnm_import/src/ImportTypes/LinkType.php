<?php

namespace Drupal\bnm_import\ImportTypes;

class LinkType extends ImportType {

  protected $value;

  public function __construct($data, $field = [])
  {
    if(!$this->isValidUrl($data) && $data != ''){
      throw new \Exception('Not a valid url');
    }
    $this->value = [
      'uri' => $data,
      'title' => $field['title']
    ];
  }

  public function getValue()
  {
    return $this->value;
  }

  public function __toString()
  {
    return parent::__toString();
  }

  private function isValidUrl($data){
    return filter_var($data, FILTER_VALIDATE_URL);
  }

}
