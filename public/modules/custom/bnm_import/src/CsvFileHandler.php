<?php

namespace Drupal\bnm_import;

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
   * @param \Drupal\file\Entity\File $file
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

      $config = \Drupal::config('bnm_import.group_field_map')->get('field_map');
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

        foreach (array_merge($required_fields, $optional_fields) as $key => $field) {
          if ($this->isRequiredField($key, $required_fields) && !$row[array_search($key, $header)]) {
            $errors[] = "Value for $key on line $i is required and therefore may not be empty";
            continue;
          }

          $validity = $this->validateFieldValue($key, $field, $row, $header, $i);
          if ($validity !== TRUE) {
            $errors[] = $validity . ' ' . $row[array_search(strtolower($key), array_map('strtolower', $header))];
          }
        }

      }
    }

    return $errors;
  }

  /**
   * Create array of nodes to create or update.
   *
   * @param \Drupal\file\Entity\File $file
   *   Csv file.
   * @param string $langcode
   *   Language code from the form state.
   *
   * @return array
   *   Array of nodes to update or create.
   */
  public function createContent($handle) {
    if ($handle) {
      $i = 0;

      $config = \Drupal::config('bnm_import.group_field_map')->get('field_map');
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
          $data_object = $this->createValue($this->getFieldValueByHeaderTitleIndex($row, $key, $header), $field);
          $is_child = FALSE;
          if (!isset($field['type'])) {
            $node_fields[$field['field']] = $data_object->getValue();
          }
          elseif ($field['type'] == 'taxonomy') {

            if (isset($field['child'])) {
              $is_child = TRUE;
              $parent = $row[$this->getCsvHeaderIndexByName('Organisation', $header)];
            }

            $storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');

            foreach ($data_object->getValue() as $term) {
              $existing_terms = $storage->loadByProperties([ 
                'name' => ucfirst(trim($term)),
                'vid' => $field['taxonomy_type'],
              ]);
              if ($existing_terms) {
                if ($is_child) {
                  $node_terms[$field['taxonomy_type'] . '_child'][] = reset($existing_terms);
                }
                else {
                  $node_terms[$field['taxonomy_type']][] = reset($existing_terms);
                }
              }
              else {
                if (!$term) {
                  continue;
                }
                $name = trim($term);
                $term = Term::create([
                  'name' => ucfirst($name),
                  'vid' => $field['taxonomy_type'],
                ]);

                if ($is_child) {
                  $pt = $storage->loadByProperties(['name' => ucfirst(trim($parent)), 'vid' => $field['taxonomy_type']]);
                  if ($pt) {
                    $term->set('parent', ['target_id' => reset($pt)->id()]);
                  }
                }

                $term->save();
                if ($is_child) {
                  $node_terms[$field['taxonomy_type'] . '_child'][] = $term;
                }
                else {
                  $node_terms[$field['taxonomy_type']][] = $term;
                }

                $terms[] = $term;
              }

            }

          }
          elseif ($field['type'] == 'link') {
            if ($data_object->getValue() && isset($data_object->getValue()['uri'])) {
              $node_fields[$field['field']][] = $data_object->getValue();
            }
          }
          else {
            $node_fields[$field['field']] = $data_object->getValue();
          }
        }

        $node = Node::create($node_fields);

        if (isset($node_terms['affiliations'])) {
          // Term exists or newly created.
          if (reset($node_terms['affiliations']) instanceof Term) {
            $node->set('field_main_affiliation', ['target_id' => reset($node_terms['affiliations'])->tid->value]);
          }
          else {
            $node->set('field_main_affiliation', ['target_id' => reset($node_terms['affiliations'])]);
          }
        }

        if (isset($node_terms['affiliations_child'])) {
          if (reset($node_terms['affiliations_child']) instanceof Term) {
            $node->set('field_faculty_unit', reset($node_terms['affiliations_child'])->tid->value);
          }
          else {
            $node->set('field_faculty_unit', ['target_id' => reset($node_terms['affiliations_child'])]);
          }
        }

        if (isset($node_terms['keywords'])) {
          foreach ($node_terms['keywords'] as $tid => $keyword) {
            if ($tid == 0) {
              $node->set('field_keywords', $keyword->tid->value);
            }
            else {
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
      'terms' => $terms,
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
   * @return \Drupal\bnm_import\ImportTypes\ImportTypes\ImportType
   *   Object containing value.
   *
   * @throws \Exception
   */
  public function createValue($data, $field) {
    if ($field['field'] == 'body') {
      return new TextType($data, $field);
    }
    
    switch ($field['type']) {
      case 'string':
      case 'string_long':
        return new TextType($data, $field);

      break;
      case 'link':
        return new LinkType($data, $field);

      break;
      case 'email':
        return new EmailType($data, $field);

      break;
      case 'taxonomy':
        return new TaxonomyType($data, $field);

      break;
      default:
        return FALSE;
    }
  }

  /**
   *
   */
  private function getNodeConstantValues() {
    $user_id = \Drupal::currentUser()->id();
    return [
      'type' => 'research_group',
      'title' => '',
      'uid' => $user_id,
      'langcode' => 'en',
    ];
  }

  /**
   * @param $field_name
   * @param $required_field_names
   * @return bool
   */
  private function isRequiredField($field_name, $required_field_names) {
    return array_key_exists($field_name, $required_field_names);
  }

  /**
   * @param $row
   * @param $header_title
   * @param $header
   * @return string
   */
  private function getFieldValueByHeaderTitleIndex($row, $header_title, $header) {
    $index = $this->getCsvHeaderIndexByName($header_title, $header);
    return trim($row[$index]);
  }

  /**
   * @param $header
   * @param $name
   */
  private function getCsvHeaderIndexByName($header_title, $header) {
    return array_search(strtolower($header_title), array_map('strtolower', $header));
  }

  /**
   *
   */
  private function validateFieldValue($key, $field, $row, $header, $i) {
    try {
      if (!$this->createValue($this->getFieldValueByHeaderTitleIndex($row, $key, $header), $field) instanceof ImportType) {
        return "Something unexpected happened while creating $key on line $i";
      }
    }
    catch (\Exception $exception) {
      return "Invalid value on line $i, on column $key.";
    }
    return TRUE;
  }

}
