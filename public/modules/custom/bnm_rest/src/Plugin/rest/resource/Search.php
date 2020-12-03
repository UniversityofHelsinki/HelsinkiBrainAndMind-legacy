<?php

namespace Drupal\bnm_rest\Plugin\rest\resource;

use Drupal\search_api\Entity\Index;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\rest\Plugin\ResourceBase;
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

    $parse_mode = \Drupal::service('plugin.manager.search_api.parse_mode')
      ->createInstance('direct');
    $parse_mode->setConjunction('AND');
    $query->setParseMode($parse_mode);

    // Execute the search.
    $results = $query->execute();
    $return = [];

    foreach ($results as $item) {
      if($affiliate && $item->field_main_affiliation->target_id != $affiliate){
        continue;
      }

      $data = explode(':', $item->getId());
      $data = explode('/', $data[1]);
      $node = Node::load($data[1]);
      $keywords = $node->field_keywords;
      $main_affiliation = Term::load($node->field_main_affiliation->target_id)->getName();

      $links = [];
      foreach ($node->field_links as $link) {
        $link = [
          'title' => $link->title,
          'url' => $link->uri,
        ];
        $links[] = $link;
      }

      $keywords_list = [];
      foreach ($keywords as $keyword) {
        $keywords_list[] = Term::load($keyword->target_id)->getName();
      }

      $return[$node->id()] = [
        'url' => '',
        'title' => $node->title->value,
        'field_email' => $node->field_email->value ,
        'field_faculty_unit' => $node->field_faculty_unit->value,
        'field_other_affiliations' => $node->field_other_affiliations->value,
        'field_research_group_name' => $node->field_research_group_name->value,
        'field_firstname' => $node->field_firstname->value,
        'field_lastname' => $node->field_lastname->value,
        'field_links' => $links,
        'field_keywords' => $keywords_list,
        'field_main_affiliation' => $main_affiliation,
      ];
    }
    return new JsonResponse($return);
  }

}
