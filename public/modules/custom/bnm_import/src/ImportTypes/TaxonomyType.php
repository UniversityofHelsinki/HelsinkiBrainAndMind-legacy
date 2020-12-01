<?php

namespace Drupal\bnm_import\ImportTypes;

class TaxonomyType extends ImportType {

  protected $value;

  public function __construct($data, $field = [])
  {
    if($field['taxonomy_type'] === 'affiliations'){
      $this->value = [$data];
    }
    else if($field['taxonomy_type'] === 'keywords'){
      $this->value = explode(',', $data);
    }
  }

  public function getValue()
  {
    return $this->value;
  }

  public function __toString()
  {
    return FALSE;
    #return parent::__toString();
  }

}
