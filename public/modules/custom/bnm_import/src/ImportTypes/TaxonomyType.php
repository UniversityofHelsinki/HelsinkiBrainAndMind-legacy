<?php

namespace Drupal\bnm_import\ImportTypes;

/**
 *
 */
class TaxonomyType extends ImportType {

  protected $value;
  protected $child;

  /**
   * TaxonomyType constructor.
   *
   * @param $data
   * @param array $field
   *   Array of field mappings from the field map configuration file.
   */
  public function __construct($data, $field = []) {
    if ($field['taxonomy_type'] === 'affiliations') {
      $this->value = [$data];
      $this->child = isset($field['child']) && $field['child'] == TRUE ? $field['child'] : FALSE;
    }
    elseif ($field['taxonomy_type'] === 'keywords') {
      $this->value = explode(',', $data);
    }
  }

  /**
   *
   */
  public function getValue() {
    return $this->value;
  }

}
