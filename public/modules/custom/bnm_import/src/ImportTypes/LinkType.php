<?php

namespace Drupal\bnm_import\ImportTypes;

use Drupal\Component\Utility\UrlHelper;

/**
 *
 */
class LinkType extends ImportType {

  protected $value;

  /**
   *
   */
  public function __construct($data, $field = []) {
    if ($data == '') {
      return FALSE;
    }
    if ($data != '' && !$this->isValidUrl($data)) {
      throw new \Exception('Not a valid url');
    }
    $this->value = [
      'uri' => $data,
      'title' => $field['title'],
    ];
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
  private function isValidUrl($data) {
    return UrlHelper::isValid($data);
  }

}
