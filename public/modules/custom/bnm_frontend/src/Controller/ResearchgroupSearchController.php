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
    $build = [
      '#markup' => '<div id="app" data-environment="'.$host.'"></div>',
      '#cache' => ['max-age' => 0],
      '#attached' => [
        'library' => [
          'bnm_frontend/researchgroup-search'
        ],
      ],
    ];
    return $build;
  }

}
