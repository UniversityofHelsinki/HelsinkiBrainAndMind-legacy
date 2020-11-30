<?php

namespace Drupal\bnm_import;

use Drupal;
use Drupal\bnm_import\ImportTypes\EmailType;
use Drupal\bnm_import\ImportTypes\LinkType;
use Drupal\bnm_import\ImportTypes\TextType;
use Drupal\bnm_import\ImportTypes\TaxonomyType;
use Drupal\bnm_import\ImportTypes\ImportType;
use Drupal\node\Entity\Node;

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
          $header = array_flip($row);
          continue;
        }
        $i++;

        foreach($required_fields as $key => $field) {
          if(!$row[$header[$key]]){
            $errors[] = "Value for $key on line $i is required and therefore may not be empty";
            continue;
          }
          try {
            if($this->createValue($row[$header[$key]], $field) instanceof ImportType){
              continue;
            } else {
              $errors[] = "Something unexpected happened while creating $key on line $i";
              continue;
            }
          }
          catch(\Exception $exception){
            $errors[] = "Invalid value on line $i, on column $key.";
            continue;
          }

        }

        foreach($optional_fields as $key => $field){
          try {
            if($this->createValue($row[$header[$key]], $field) instanceof ImportType){
              continue;
            } else {
              $errors[] = "Something unexpected happened while creating $key on line $i";
              continue;
            }
          }
          catch(\Exception $exception){
            $errors[] = "Invalid value on line $i, on column $key.";
            continue;
          }
        }
      }
      fclose($handle);
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
          $header = array_flip($row);
          $i++;
          continue;
        }

        $node_terms = [];
        $node_fields = $this->getNodeConstantValues();

        foreach ($fields as $key => $field) {
          $data_object = $this->createValue($row[$header[$key]], $field);

          if ($field['type'] == 'taxonomy') {
            foreach ($data_object as $term) {
              if(!$existing_terms = taxonomy_term_load_multiple_by_name(ucfirst($term), $field['taxonomy_type'])){
                $node_terms[$field['taxonomy_type']] = $existing_terms;
              } else {
                $term = Term::create([
                  'name' => ucfirst($term),
                  'vid' => $field['taxonomy_type'],
                ])->save();
                $node_terms[$field['taxonomy_type']][] = $term;
                $terms[] = $term;
              }

            }
          } else {
            $node_fields[$field['field']] = $data_object->getValue();
          }

        }

        $node = Node::create($node_fields);

        if ($terms['affiliations']) {
          $node->set('field_article_images', ['target_id' => $terms['affiliations'][0]->id()]);
        }

        if ($terms['keywords']) {
          foreach ($terms['keywords'] as $index => $keyword) {
            if ($index == 0) {
              $node->set('field_keywords', $keyword->id());
            } else {
              $node->get('field_keywords')->appendItem([
                'target_id' => $keyword->id(),
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

  private function createTerms()
  {
    $terms = [];

    #Drupal\taxonomy\Entity\Term::

    return $terms;
  }


  /**
   * Get field definitions by field machine names in csv header.
   *
   * @param array $header
   *   Array of machine names.
   * @param array $field_definitions
   *   Content type field definitions.
   *
   * @return array
   *   Array of field types by machine name.
   */
  private function getFieldTypes(array $header, array $field_definitions) {
    $field_types = [];
    foreach ($header as $key => $title) {
      if (isset($field_definitions[$title])) {
        $field_types[] = $field_definitions[$title]->getType();
      }
    }
    return $field_types;
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

}
