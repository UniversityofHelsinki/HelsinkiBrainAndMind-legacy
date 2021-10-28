<?php

namespace Drupal\bnm_rest\Plugin\rest\resource;

use Drupal\search_api\Entity\Index;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\taxonomy\TermStorage;
use Drupal\taxonomy\TermStorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a resource to get available filters.
 *
 * @RestResource(
 *   id = "researchgroup_search_rest_resource",
 *   label = @Translation("Search"),
 *   uri_paths = {
 *     "canonical" = "/researchgroup_search",
 *     "https://www.drupal.org/link-relations/create" = "/researchgroup_search"
 *   }
 * )
 */
final class Search extends ResourceBase {

  /**
   * Constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
  }

  /**
   * Create.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      \Drupal::logger('bnm')
    );
  }

  /**
   * Responds to GET requests.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The HTTP response object.
   */
  public function get(Request $request) {
    $q = $request->get('q');
    $affiliate = $request->get('affiliate');
    $index = Index::load('research_group');
    $parentid = NULL;
    $affiliate_depth = 1;

    if ($affiliate && $affiliate != 0) {
      $affiliate_depth = taxonomy_term_depth_get_by_tid($affiliate);
      $children = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('affiliations', $affiliate, 3, true);
      $children_ids = array_map(function ($item) {
        return $item->id();
      }, $children);
    }

    /** @var \Drupal\search_api\Query\Query $query */
    $query = $index->query();

    $query->keys(str_replace(',',' ', $q));

    $query->getParseMode()->setConjunction('AND');

    $results = $query->execute();
    $return = [];

    foreach ($results as $item) {
      $data = explode(':', $item->getId());
      $data = explode('/', $data[1]);
      $node = Node::load($data[1]);

      if(!$affiliate || $affiliate == 0){
      } else {
        if($children && $affiliate_depth == '1') {
          $is_child = in_array($node->field_faculty_unit->target_id, $children_ids, false) ? true : false;
          if(!$is_child){
            continue;
          }
        } else {
          //is a child affiliation, show only if get parameter id == node field_faculty_unit value
          if ($affiliate_depth == '3') {
            if($node->field_unit->target_id != $affiliate){
              continue;
            }
          }
          else {
            if($node->field_faculty_unit->target_id != $affiliate){
              continue;
            }
          }
        }
      }

      $links = $this->getLinks($node->field_links);
      $keywords_list = $this->getKeywords($node->field_keywords);
      $affiliationstemp = [];
      $units = [];

      foreach($node->field_affiliations as $key => $affiliation) {
        // Get term object.
        $term = Term::load($affiliation->target_id);
        // Term name.
        $term_name = $term->getName();

        // Check that term is deeper than level 1.
        if ($term->depth_level->first()->getValue()['value'] > '1') {
          // Get parent term.
          $parent = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadParents($term->id());
          $parent = reset($parent);
          // Parent term name.
          $parent_name = $parent->getName();

          // Check if term third level term.
          if ($parent->depth_level->first()->getValue()['value'] == '2' ) {
            // Get root term.
            $rootparent = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadParents($parent->id());
            $rootparent = reset($rootparent);
            // Root term name.
            $parent_name = $rootparent->getName();
            // Deepest term is level third so set unit value.
            $units[$key] = $term->getName();
            // Deepest term is level third so term name is parent value.
            $term_name = $parent->getName();
          }

          $affiliationstemp[$key] = "$parent_name, $term_name";
        }
      }

      // Convert affiliations array to string.
      $affiliations = implode(', ', $affiliationstemp);
      // Convert units array to string.
      $units = implode(', ', $units);

      // "Old way" to map affiliations values if shs field is empty.
      if (empty($affiliations)) {
        $faculty_affiliations = [];
        foreach($node->field_faculty_unit as $faculty_affiliation) {
          $faculty_affiliations[] = Term::load($faculty_affiliation->target_id)->getName();
        }

        $main_affiliations = [];
        foreach($node->field_main_affiliation as $main_affiliation) {
          $main_affiliations[] = Term::load($main_affiliation->target_id)->getName();
        }

        $affiliations = '';
        $main_last_key = end(array_keys($main_affiliations));
        $faculty_count = 0;

        foreach($main_affiliations as $key => $main) {
          $affiliations .= "$main_affiliations[$key]";

          if (isset($faculty_affiliations[$key])) {
            $affiliations .= ", $faculty_affiliations[$key]";
          }

          if ($key != $main_last_key) {
            $affiliations .= ", ";
          }

          $faculty_count++;
        }

        if (count($faculty_affiliations) > $faculty_count) {
          for ($x = $faculty_count; $x <= count($faculty_affiliations); $x++) {
            $affiliations += ", $faculty_affiliations[$x]";
          }
        }
      }

      $titles = [];

      if (!$node->field_title->isEmpty()) {
        foreach ($node->field_title->getValue() as $value) {
          $titles[] = $value['value'];
        }
      }

      if (count($titles) > 0) {
        $titles = implode(', ', $titles);
      }
      else {
        $titles = '';
      }

      $return[] = [
        'id' => $node->id(),
        'url' => '',
        'title' => $node->title->value,
        'body' => $node->body->value,
        'field_email' => $node->field_email->value ,
        'field_other_affiliations' => $node->field_other_affiliations->value,
        'field_research_group_name' => $node->field_research_group_name->value,
        'field_firstname' => $node->field_firstname->value,
        'field_lastname' => $node->field_lastname->value,
        'field_links' => $links,
        'field_keywords' => $keywords_list,
        'field_unit' => $units,
        'field_affiliations' => $affiliations,
        'field_industrial_collaboration' => $node->field_industrial_collaboration->value,
        'field_title' => $titles
      ];
    }

    usort($return, function($a, $b) {
      // Sort by lastname.
      $sorted = strnatcmp($a['field_lastname'], $b['field_lastname']);
      if ($sorted) return $sorted;

      // If last names are identical, sort by firstname.
      return strnatcmp($a['field_firstname'], $b['field_firstname']);
    });

    return new JsonResponse($return);
  }

  private function getLinks($field) {
    $links = [];
    foreach ($field as $link) {
      $link = [
        'title' => $link->title,
        'url' => $link->uri,
      ];
      $links[] = $link;
    }
    return $links;
  }

  private function getKeywords($keywords){
    $keywords_list = [];

    foreach ($keywords as $keyword) {
      $target_id = $keyword->target_id;
      if (Term::load($target_id)) $keywords_list[] = Term::load($target_id)->getName();
    }

    return $keywords_list;
  }

}
