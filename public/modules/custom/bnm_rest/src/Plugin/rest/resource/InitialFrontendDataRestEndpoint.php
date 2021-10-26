<?php

namespace Drupal\bnm_rest\Plugin\rest\resource;

use Drupal\node\Entity\Node;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\search_api\Entity\Index;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\TermStorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a resource to get all initially available front-end data.
 *
 * @RestResource(
 *   id = "initial_frontend_data_rest_resource",
 *   label = @Translation("Initial Frontend Data"),
 *   uri_paths = {
 *     "canonical" = "/initial-frontend-data",
 *     "https://www.drupal.org/link-relations/create" = "/initial-frontend-data"
 *   }
 * )
 */
final class InitialFrontendDataRestEndpoint extends ResourceBase {

  /**
   * Term storage.
   *
   * @var TermStorageInterface
   */
  private $manager;

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
   * @param TermStorageInterface $manager
   *   The term manager.
   */

  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, TermStorageInterface $manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->manager = $manager;
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
      \Drupal::logger('bnm'),
      $container->get('entity_type.manager')->getStorage('taxonomy_term')
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
    $items = [];

    $items['footer_menu'] = $this->getMenuTreeLinks('footer');
    $items['affiliates'] = $this->getAffialites();
    $items['initial_search_results'] = $this->getInitialSearchResults($request);

    return new JsonResponse($items);
  }

  private function getAffialites() {
    $affiliates = $this->manager->loadTree('Affiliations', 0, NULL, TRUE);

    if (!$affiliates) return [];

    $stack = [];

    /** @var Term $term */
    foreach($affiliates as $term) {
      $children = $this->manager->loadChildren($term->id());
      $item = [
        'id' => $term->id(),
        'name' => $term->getName()
      ];
      $item['children'] = empty($children) ? NULL : array_values(array_map(function($term) { return $term->id(); }, $children));
      $stack[] = $item;
    }

    return $stack;
  }

  private function getMenuTreeLinks($menu) {
    $tree = \Drupal::menuTree()->load($menu, new \Drupal\Core\Menu\MenuTreeParameters());
    $links = [];

    $host = \Drupal::request()->getSchemeAndHttpHost();

    $allowedFileExtensions = ['docx', 'pdf'];

    foreach ($tree as $item) {
      $title = $item->link->getTitle();
      $link = $item->link->getUrlObject()->toString();
      $weight = $item->link->getWeight();
      $isExternal = $item->link->getUrlObject()->isExternal();
      $linkFileExtension = pathinfo($link, PATHINFO_EXTENSION);
      $isFile = in_array($linkFileExtension, $allowedFileExtensions);

      // Add base url, if link is internal.
      $link = !$isExternal ? ($host . $link) : $link;

      if ($item->link->isEnabled()) {
        $links[] = ['title' => $title, 'link' => $link, 'weight' => $weight, 'external' => $isExternal, 'downloadable' => $isFile];
      }
    }

    usort($links, function ($item1, $item2) {
      return $item1['weight'] <=> $item2['weight'];
    });

    foreach ($links as $key => $link) {
      unset($links[$key]['weight']);
    }

    return $links;
  }

  private function getInitialSearchResults($request, $amount = NULL) {
    $q = $request->get('q');
    $affiliate = $request->get('affiliate');
    $index = Index::load('research_group');

    /** @var \Drupal\search_api\Query\Query $query */
    $query = $index->query();

    $query->keys(str_replace(',',' ', $q));

    $query->getParseMode()->setConjunction('AND');

    // Execute the search.
    $results = $query->execute();
    $stack = [];

    $target_results = array_slice($results->getResultItems(), 0, $amount ?? $results->getResultCount());

    foreach ($target_results as $item) {
      $data = explode(':', $item->getId());
      $data = explode('/', $data[1]);
      $node = Node::load($data[1]);

      if ($affiliate != 0 && isset($node->field_main_affiliation) && ($node->field_main_affiliation->target_id != (string)$affiliate)) {
        continue;
      }

      $main_affiliation = Term::load($node->field_main_affiliation->target_id)->getName();

      $links = $this->getLinks($node->field_links);

      $keywords_list = $this->getKeywords($node->field_keywords);

      $faculty_field_entity = $node->field_faculty_unit->entity;
      $faculty = $faculty_field_entity ? $faculty_field_entity->getName() : NULL;

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

      $stack[] = [
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
        'field_main_affiliation' => $main_affiliation,
        'field_industrial_collaboration' => $node->field_industrial_collaboration->value,
        'field_title' => $titles
      ];
    }

    usort($stack, function($a, $b) {
      // Sort by lastname.
      $sorted = strnatcmp($a['field_lastname'], $b['field_lastname']);
      if ($sorted) return $sorted;

      // If last names are identical, sort by firstname.
      return strnatcmp($a['field_firstname'], $b['field_firstname']);
    });

    return $stack;
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
