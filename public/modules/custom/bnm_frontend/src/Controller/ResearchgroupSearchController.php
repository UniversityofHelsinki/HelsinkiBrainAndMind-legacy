<?php

namespace Drupal\bnm_frontend\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * An bnm_frontend controller.
 */
class ResearchgroupSearchController extends ControllerBase {

  /**
   * Render Vue application.
   */
  public function content() {
    $host = \Drupal::request()->getSchemeAndHttpHost();
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
