<?php

namespace Drupal\bnm_import;

use Drupal;
use Drupal\bnm_import\ImportTypes\EmailType;
use Drupal\bnm_import\ImportTypes\LinkType;
use Drupal\bnm_import\ImportTypes\TextType;
use Drupal\bnm_import\ImportTypes\TaxonomyType;
use Drupal\bnm_import\ImportTypes\ImportType;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * Class to handle csv upload logic.
 */
class CsvFileHandler {

  /**
   * Constructor.
   */
  public function __construct() {
  }

  /**
   * Validate data entered to csv file.
   *
   * @param Drupal\file\Entity\File $file
   *   Csv file.
   *
   * @return array
   *   Array of error messages.
   */
  public function validateImportData($handle) {
    $errors = [];

    if ($handle) {
      $i = 0;
      $header = [];

      $config = Drupal::config('bnm_import.group_field_map')->get('field_map');
      $required_fields = $config['required'];
      $optional_fields = $config['optional'];
      // Each row represents old or new apartment node.
      while (($row = fgetcsv($handle, 0, ';', '"', '\\')) !== FALSE) {
        if ($i == 0) {
          $i++;
          $header = $row;
          continue;
        }
        $i++;

        foreach(array_merge($required_fields, $optional_fields) as $key => $field) {
          if($this->isRequiredField($key, $required_fields) && !$row[array_search($key, $header)]){
            $errors[] = "Value for $key on line $i is required and therefore may not be empty";
            continue;
          }

          $validity = $this->validateFieldValue($key, $field, $row, $header, $i);
          if($validity !== TRUE ){
            $errors[] = $validity.' '.$row[array_search(strtolower($key), array_map('strtolower',$header))];
          }
        }

      }
    }

    return $errors;
  }

  /**
   * Create array of nodes to create or update.
   *
   * @param Drupal\file\Entity\File $file
   *   Csv file.
   * @param string $langcode
   *   Language code from the form state.
   *
   * @return array
   *   Array of nodes to update or create.
   */
  public function createContent($handle)
  {
    if ($handle) {
      $i = 0;

      $config = Drupal::config('bnm_import.group_field_map')->get('field_map');
      $fields = array_merge($config['required'], $config['optional']);

      $nodes = [];
      $terms = [];

      // Each row represents old or new apartment node.
      while (($row = fgetcsv($handle, 0, ';', '"', '\\')) !== FALSE) {
        // Get header for fields machine names.
        if ($i == 0) {
          $header = $row;
          $i++;
          continue;
        }
        $i++;

        $node_terms = [];
        $node_fields = $this->getNodeConstantValues();

        foreach ($fields as $key => $field) {
          $data_object = $this->createValue(trim($row[array_search(strtolower($key), array_map('strtolower',$header))]), $field);
          if ($field['type'] == 'taxonomy') {
            foreach ($data_object->getValue() as $term) {
              if($existing_terms = taxonomy_term_load_multiple_by_name(ucfirst($term), $field['taxonomy_type'])){
                $node_terms[$field['taxonomy_type']][] = reset($existing_terms);
              } else {
                if(!$term){
                  continue;
                }
                $name = trim($term);
                $term = Term::create([
                  'name' => ucfirst($name),
                  'vid' => $field['taxonomy_type'],
                ]);
                $term->save();
                $node_terms[$field['taxonomy_type']][] = $term;
                $terms[] = $term;
              }
            }
          } else if($field['type'] == 'link'){
            if($data_object->getValue()['uri']){
              $node_fields[$field['field']][] = $data_object->getValue();
            }
          }
          else {
            $node_fields[$field['field']] = $data_object->getValue();
          }
        }

        $node = Node::create($node_fields);

        if ($node_terms['affiliations']) {
          if(reset($node_terms['affiliations']) instanceof Term){
            $node->set('field_main_affiliation', ['target_id' => reset($node_terms['affiliations'])->tid->value]);
          } else {
            $node->set('field_main_affiliation', ['target_id' => reset($node_terms['affiliations'])]);
          }
        }

        if ($node_terms['keywords']) {
          foreach ($node_terms['keywords'] as $tid => $keyword) {
            if ($tid == 0) {
              $node->set('field_keywords', $keyword->tid->value);
            } else {
              $node->get('field_keywords')->appendItem([
                'target_id' => $keyword->tid->value,
              ]);
            }
          }
        }

        $nodes[] = $node;
      }

    }

    foreach ($nodes as $node) {
      $node->save();
    }

    return [
      'nodes' => $nodes,
      'terms' => $terms
    ];

  }

  /**
   * Create and validate different data types.
   *
   * @param mixed $data
   *   Data which will be turned into value object.
   * @param string $type
   *   Field type.
   *
   * @return ImportTypes\ImportType
   *   Object containing value.
   *
   * @throws \Exception
   */
  public function createValue($data, $field) {
    switch ($field['type']) {
      case 'string':
      case 'string_long':
        return new TextType($data,$field);
      break;
      case 'link':
        return new LinkType($data,$field);
      break;
      case 'email':
        return new EmailType($data,$field);
      break;
      case 'taxonomy':
        return new TaxonomyType($data,$field);
        break;
      default:
        return FALSE;
    }
  }

  private function getNodeConstantValues(){
    $user_id = \Drupal::currentUser()->id();
    return [
      'type' => 'research_group',
      'title' => '',
      'uid' => $user_id,
      'langcode' => 'en',
    ];
  }

  private function isRequiredField($field_name, $required_field_names){
    return array_key_exists($field_name, $required_field_names);
  }

  private function validateFieldValue($key, $field, $row, $header, $i){
    try {
      if(!$this->createValue(trim($row[array_search(strtolower($key), array_map('strtolower',$header))]), $field) instanceof ImportType){
        return "Something unexpected happened while creating $key on line $i";
      }
    }
    catch(\Exception $exception){
      return "Invalid value on line $i, on column $key.";
    }
    return TRUE;
  }

}
