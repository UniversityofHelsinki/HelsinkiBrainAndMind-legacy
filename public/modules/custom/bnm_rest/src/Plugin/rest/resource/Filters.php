<?php

namespace Drupal\bnm_rest\Plugin\rest\resource;

use Drupal\taxonomy\Entity\Term;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\taxonomy\TermStorage;
use Drupal\taxonomy\TermStorageInterface;
use Drush\Log\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a resource to get available filters.
 *
 * @RestResource(
 *   id = "filter_rest_resource",
 *   label = @Translation("Filters"),
 *   uri_paths = {
 *     "canonical" = "/filters",
 *     "https://www.drupal.org/link-relations/create" = "/filters"
 *   }
 * )
 */
final class Filters extends ResourceBase {

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
    $affiliates = $this->manager->loadTree('Affiliations', 0, NULL, TRUE);

    if (!$affiliates) {
      return new JsonResponse([]);
    }

    $items = [];

    /** @var Term $term */
    foreach($affiliates as $term) {
      $children = $this->manager->loadChildren($term->id());
      $item = [
        'id' => $term->id(),
        'name' => $term->getName()
      ];
      $item['children'] = empty($children) ? null : array_values(array_map(function($term) { return $term->id(); }, $children));

      $items[] = $item;
    }
    return new JsonResponse($items);
  }


}
