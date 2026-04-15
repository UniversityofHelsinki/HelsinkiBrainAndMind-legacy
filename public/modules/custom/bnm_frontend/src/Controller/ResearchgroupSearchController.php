<?php

namespace Drupal\bnm_frontend\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * An bnm_frontend controller.
 */
class ResearchgroupSearchController extends ControllerBase {

  /**
   * Constructor.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The http request stack.
   */
  public function __construct(
    private readonly RequestStack $requestStack
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('request_stack'),
    );
  }

  /**
   * Render Vue application.
   */
  public function content() {
    $host = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();
    $production_url = 'https://research.helsinkibrainandmind.fi/';
    $host = $host === 'https://hbm-prod-20.it.helsinki.fi/initial-frontend-data' ? $production_url : $host;

    $build = [
      '#markup' => '<div id="app" data-environment="' . $host . '"></div>',
      '#cache' => ['max-age' => 0],
      '#attached' => [
        'library' => [
          'bnm_frontend/researchgroup-search',
        ],
      ],
    ];
    return $build;
  }

}
