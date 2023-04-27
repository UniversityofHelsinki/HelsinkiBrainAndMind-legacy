<?php

namespace Drupal\bnm_add_csp_header\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * BNM - Add CSP Header event subscriber.
 */
class BnmAddCSPHeaderSubscriber implements EventSubscriberInterface {

  private string $allowedUrl = 'https://helsinkibrainandmind.fi';

// BELOW CODE IS FOR TESTING PURPOSES

//  public function addCSPHeader(ResponseEvent $event): void {
//    $embed = $event->getRequest()->get('embed', FALSE);
//
//    if ($embed) {
//      if (getenv('APP_ENV') === 'dev') {
//        $this->allowedUrl = 'https://iframe-test-bnm.docker.so';
//      }
//
//      $event->getResponse()->headers->set('Content-Security-Policy', 'frame-ancestors '. $this->allowedUrl);
//    }


  // Only allow https://helsinkibrainandmind.fi to embed this page.

  public function addCSPHeader(ResponseEvent $event): void {
    $embed = $event->getRequest()->get('embed', FALSE);

    if ($embed) {
      $event->getResponse()->headers->set('Content-Security-Policy', 'frame-ancestors '. $this->allowedUrl);
    }
  }


  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::RESPONSE => ['addCSPHeader', -10],
    ];
  }

}
