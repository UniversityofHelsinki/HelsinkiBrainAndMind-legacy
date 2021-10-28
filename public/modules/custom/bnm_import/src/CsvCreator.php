<?php

namespace Drupal\bnm_import;

/**
 * Class to handle csv creation logic.
 */
class CsvCreator
{

  /**
   * Constructor.
   */
  public function __construct()
  {
  }

  /**
   * @return array
   */
  public function createCsvData($nodes, $keywords, $organisations, $csv_headers) {
    $data = [];

    $csv_header_names = array_keys($csv_headers);
    $data[] = $csv_header_names;

    $keywords_by_tid = [];
    foreach($keywords as $keyword){
      $keywords_by_tid[$keyword->tid] = $keyword;
    }

    $organisations_by_tid = [];
    foreach($organisations as $organisation){
      $organisations_by_tid[$organisation->depth][$organisation->tid] = $organisation;
    }

    foreach ($nodes as $node) {
      $row = [];
      $keyword_string = '';
      $links_set = false;

      foreach ($csv_headers as $heading => $header) {
        if($heading === 'Key words'){
          foreach($node->{$header['field']}->getValue() as $keyword_tid) {
            $keyword_string .= "{$keywords_by_tid[(int)$keyword_tid['target_id']]->name}, ";
          }
          $row[] = rtrim($keyword_string, ', ');
        }
        else if($heading === 'Organisation') {
          $organisations = [];

          foreach($node->{$header['field']}->getValue() as $organisation) {
            $organisations[] = $organisations_by_tid[$organisation['target_id']]->name;
          }
          $organisations = implode(', ', $organisations);

          $row[] = $organisations;
        }
        else if($heading === 'Faculty') {
          $facultys = [];

          foreach($node->{$header['field']}->getValue() as $faculty) {
            $facultys[] = $organisations_by_tid[$faculty['target_id']]->name;
          }
          $facultys = implode(', ', $facultys);

          $row[] = $facultys;
        }
        else if($heading === 'Title') {
          $values = [];

          foreach($node->{$header['field']}->getValue() as $titles => $title) {
            $values[] = $title["value"];
          }

          if ($values) {
            $row[] = implode('; ', $values);
          }
          else {
            $row[] = '';
          }
        }
        else if( strpos($heading, 'Link') !== false) {

          if($links_set) {
            continue;
          }

          // We want the urls in specific order.
          foreach ($this->getLinkOrder() as $title => $order) {
            $set = false;
            foreach ($node->{$header['field']}->getValue() as $url) {
              if ($url['title'] == $title) {
                $row[] = $url['uri'];
                $set = true;
              }
            }
            if(!$set){
              $row[] = null;
            }
          }
          $links_set = true;
        }
        else {
          if(isset($node->{$header['field']}->getValue()[0])){
            $row[] = $node->{$header['field']}->getValue()[0]['value'];
          } else {
            $row[] = null;
          }
        }
      }

      $data[] = $row;
    }

    return $data;
  }

  /**
   * Create a payload for a response.
   *
   * @param array $input
   * @return bool|string
   */
  public function createCsvFile(array $input) {
    // Write the file.
    $csv = fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'r+');
    foreach ($input as $csv_row) {
      fputcsv($csv, $csv_row, ';', '"', '\\');;
    }
    rewind($csv);
    $output = stream_get_contents($csv);
    fclose($csv);
    return $output;
  }

  /**
   * Find and return the correct mapping for a field.
   *
   * @param $field_name
   * @param $mappings
   * @return bool|int|string
   */
  private function findFieldMapping($field_name, $mappings){
    foreach($mappings as $heading => $mapping){
      if($mapping['field'] === $field_name){
        return $heading;
      }
    }
    return false;
  }

  private function getLinkOrder() {
    $links = [
      'Research group website' => 1,
      'Research portal' => 2,
      'Clinical researcher website' => 3,
      'Other website' => 4,
      'ORCID' => 5
    ];
    return $links;
  }

}
