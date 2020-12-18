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


        // if is parent affiliation, check if node is one of children
        $children = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('affiliations', $affiliate, 2, true);
        if($children) {
          $children_ids = array_map(function ($item) {
            return $item->id();
          }, $children);

          $is_child = in_array($node->field_faculty_unit->target_id, $children_ids, false) ? true : false;
          if(!$is_child){
            continue;
          }
        } else {
          //is a child affiliation, show only if get parameter id == node field_faculty_unit value
          if($node->field_faculty_unit->target_id != $affiliate){
            continue;
          }
        }
      }


      $links = $this->getLinks($node->field_links);

      $keywords_list = $this->getKeywords($node->field_keywords);

      $faculty_field_entity = $node->field_faculty_unit->entity;
      $faculty = $faculty_field_entity ? $faculty_field_entity->getName() : NULL;

      $main_affiliation_entity = $node->field_main_affiliation->entity;


      $return[] = [
        'id' => $node->id(),
        'url' => '',
        'title' => $node->title->value,
        'body' => $node->body->value,
        'field_email' => $node->field_email->value ,
        'field_faculty_unit' => $faculty,
        'field_other_affiliations' => $node->field_other_affiliations->value,
        'field_research_group_name' => $node->field_research_group_name->value,
        'field_firstname' => $node->field_firstname->value,
        'field_lastname' => $node->field_lastname->value,
        'field_links' => $links,
        'field_keywords' => $keywords_list,
        'field_main_affiliation' => $main_affiliation_entity->getName(),
        'field_industrial_collaboration' => $node->field_industrial_collaboration->value
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
      $keywords_list[] = Term::load($keyword->target_id)->getName();
    }
    return $keywords_list;
  }

}
