<?php

namespace Drupal\bnm_rest\Plugin\rest\resource;

use Drupal\search_api\Entity\Index;
use Drupal\search_api_autocomplete\Entity\Search;
use Drupal\rest\Plugin\ResourceBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a resource to get available suggestions.
 *
 * @RestResource(
 *   id = "search_suggestions_rest_resource",
 *   label = @Translation("Suggestions"),
 *   uri_paths = {
 *     "canonical" = "/search_suggestions",
 *     "https://www.drupal.org/link-relations/create" = "/search_suggestions"
 *   }
 * )
 */
final class SearchSuggestions extends ResourceBase {

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

    if(!$q){
      return new JsonResponse([]);
    }

    /** @var \Drupal\search_api_autocomplete\Utility\PluginHelper $plugin_helper */
    $plugin_helper = \Drupal::service('search_api_autocomplete.plugin_helper');
    $search = Search::create(Search::getDefaultOptions());

    /** @var \Drupal\search_api_autocomplete\Plugin\search_api_autocomplete\suggester\Server $suggester */
    $suggester = $plugin_helper->createSuggesterPlugin($search, 'server', []);

    $index = Index::load('research_group');
    $query = $index->query([]);

    $handle_underscore = $this->underscoreProcessorIsUsed($index->getProcessors());

    if ($handle_underscore) {
      $incomplete_key = str_replace(' ', 'XXX', $q);
      $user_input =str_replace(' ', 'XXX', $q);
    } else {
      $incomplete_key = $q;
      $user_input = $q;
    }

    $incomplete_key = $q;
    $user_input = $q;

    $suggestions = $suggester->getAutocompleteSuggestions($query, $incomplete_key, $user_input);

    $suggest = [];
    foreach($suggestions as $suggestion){
      if($handle_underscore){
        $suggest[] = $this->underscoreProcessorHandler($suggestion);
      } else {
        $suggest[] = $suggestion;
      }

      if(count($suggest) > 5){
        break;
      }
    }
    return new JsonResponse($suggest);
  }

  /**
   * Check if custom underscore processor used.
   * @param $processors
   * @return bool
   */
  private function underscoreProcessorIsUsed($processors){
    foreach($processors as $processor){
      if($processor->getPluginId() === 'bnm_underscore_processor'){
        return true;
      }
    }
    return false;
  }

  /**
   * Clean up the indexed keywords altered by custom Underscore -processor.
   *
   * @param $suggestion
   * @param $processors
   */
  private function underscoreProcessorHandler($suggestion){
    // TODO: on underscore processor, search api automatically gets rid of underscore.
    // XXX should be just a temporary workaround
    $suffix = str_replace('xxx', ' ', $suggestion->getSuggestionSuffix());
    return rtrim("{$suggestion->getUserInput()}$suffix", 'x');
  }

}
