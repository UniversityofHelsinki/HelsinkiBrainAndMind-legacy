<?php

namespace Drupal\bnm_rest\Plugin\rest\resource;

use Drupal\Core\Menu\MenuTreeParameters;
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
   * @var \Drupal\taxonomy\Entity\TermStorageInterface
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
   * @param \Drupal\taxonomy\Entity\TermStorageInterface $manager
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

  /**
   *
   */
  private function getAffialites() {
    $affiliates = $this->manager->loadTree('Affiliations', 0, NULL, TRUE);

    if (!$affiliates) {
      return [];
    }

    $stack = [];

    /** @var \Drupal\taxonomy\Entity\Term $term */
    foreach ($affiliates as $term) {
      $children = $this->manager->loadChildren($term->id());
      $depth = taxonomy_term_depth_get_by_tid($term->id());
      $name = $term->getName();

      if ($depth === '2') {
        $name = "-- $name";
      }
      elseif ($depth === '3') {
        $name = "---- $name";
      }

      $item = [
        'id' => $term->id(),
        'name' => $name,
        // 'depth' => $depth,
      ];
      $item['children'] = empty($children) ? NULL : array_values(array_map(function ($term) {
        return $term->id();
      }, $children));
      $stack[] = $item;
    }

    return $stack;
  }

  /**
   *
   */
  private function getMenuTreeLinks($menu) {
    $tree = \Drupal::menuTree()->load($menu, new MenuTreeParameters());
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

  /**
   *
   */
  private function getInitialSearchResults($request, $amount = NULL) {
    $q = $request->get('q');
    $affiliate = $request->get('affiliate');
    $index = Index::load('research_group');

    /** @var \Drupal\search_api\Query\Query $query */
    $query = $index->query();

    $query->keys(str_replace(',', ' ', $q));

    $query->getParseMode()->setConjunction('AND');

    // Execute the search.
    $results = $query->execute();
    $stack = [];

    $target_results = array_slice($results->getResultItems(), 0, $amount ?? $results->getResultCount());

    foreach ($target_results as $item) {
      $data = explode(':', $item->getId());
      $data = explode('/', $data[1]);
      $node = Node::load($data[1]);

      if ($affiliate != 0 && isset($node->field_main_affiliation) && ($node->field_main_affiliation->target_id != (string) $affiliate)) {
        continue;
      }

      $links = $this->getLinks($node->field_links);
      $keywords_list = $this->getKeywords($node->field_keywords);
      $affiliationstemp = [];

      foreach ($node->field_affiliations as $key => $affiliation) {
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
          // Set empty unit.
          $unit_name = NULL;

          // Check if term third level term.
          if ($parent->depth_level->first()->getValue()['value'] == '2') {
            // Get root term.
            $rootparent = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadParents($parent->id());
            $rootparent = reset($rootparent);
            // Root term name.
            $parent_name = $rootparent->getName();
            // Deepest term is level third so set unit value.
            $unit_name = $term->getName();
            // Deepest term is level third so term name is parent value.
            $term_name = $parent->getName();
          }

          if (!empty($unit_name)) {
            $affiliationstemp[$key] = "$parent_name, $term_name, $unit_name";
          }
          else {
            $affiliationstemp[$key] = "$parent_name, $term_name";
          }
        }
      }

      // Convert affiliations array to string.
      $affiliations = $affiliationstemp;

      // "Old way" to map affiliations values if shs field is empty.
      if (empty($affiliations)) {
        $faculty_affiliations = [];
        foreach ($node->field_faculty_unit as $faculty_affiliation) {
          $faculty_affiliations[] = Term::load($faculty_affiliation->target_id)->getName();
        }

        $main_affiliations = [];
        foreach ($node->field_main_affiliation as $main_affiliation) {
          $main_affiliations[] = Term::load($main_affiliation->target_id)->getName();
        }

        $affiliations = [];

        foreach ($main_affiliations as $key => $main) {
          if (isset($faculty_affiliations[$key])) {
            $affiliations[] = "$main_affiliations[$key], $faculty_affiliations[$key]";
          }
          else {
            $affiliations[] = "$main_affiliations[$key]";
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

      $stack[] = [
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
        'field_affiliations' => $affiliations,
        'field_industrial_collaboration' => $node->field_industrial_collaboration->value,
        'field_title' => $titles,
      ];
    }

    usort($stack, function ($a, $b) {
      // Sort by lastname.
      $sorted = strnatcmp($a['field_lastname'], $b['field_lastname']);
      if ($sorted) {
        return $sorted;
      }

      // If last names are identical, sort by firstname.
      return strnatcmp($a['field_firstname'], $b['field_firstname']);
    });

    return $stack;
  }

  /**
   *
   */
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

  /**
   *
   */
  private function getKeywords($keywords) {
    $keywords_list = [];

    foreach ($keywords as $keyword) {
      $target_id = $keyword->target_id;
      if (Term::load($target_id)) {
        $keywords_list[] = Term::load($target_id)->getName();
      }
    }

    return $keywords_list;
  }

}
