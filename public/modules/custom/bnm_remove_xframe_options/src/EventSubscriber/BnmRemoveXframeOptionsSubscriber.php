<?php

namespace Drupal\bnm_remove_xframe_options\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * bnm_remove_xframe_options event subscriber.
 */
class BnmRemoveXframeOptionsSubscriber implements EventSubscriberInterface {

  public function BnmRemoveXframeOptions(ResponseEvent $event)
  {
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
      $url = 'https://';
      // Append the host(domain name) to the URL.
      $url .= $_SERVER['HTTP_HOST'];
      $url .= $_SERVER['REQUEST_URI'];
      if (str_contains($url,'?embed=true')) {
        $response = $event->getResponse();
        // Only allow https://helsinkibrainandmind.fi to embed this page
        $response->headers->set('Content-Security-Policy', "frame-ancestors https://helsinkibrainandmind.fi");
      }
    }
  }

  /**
   * {@inheritdoc}
   */

  public static function getSubscribedEvents()
  {
    $events[KernelEvents::RESPONSE][] = array('BnmRemoveXframeOptions', -10);
    return $events;
  }
}
