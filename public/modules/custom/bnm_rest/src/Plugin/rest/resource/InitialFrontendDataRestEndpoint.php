<?php

namespace Drupal\bnm_rest\Plugin\rest\resource;

use Drupal\taxonomy\Entity\Term;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\taxonomy\TermStorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Url;

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


}
