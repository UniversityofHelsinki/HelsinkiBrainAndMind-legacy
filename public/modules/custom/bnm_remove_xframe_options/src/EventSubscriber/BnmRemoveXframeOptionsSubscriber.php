<?php

namespace Drupal\bnm_remove_xframe_options\EventSubscriber;

//use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
//use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
//use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * bnm_remove_xframe_options event subscriber.
 */
class BnmRemoveXframeOptionsSubscriber implements EventSubscriberInterface {

  public function BnmRemoveXframeOptions(ResponseEvent $event)
  {
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
      $url = 'https://';
//    else
//      $url = 'http://';
      // Append the host(domain name, ip) to the URL.
      $url .= $_SERVER['HTTP_HOST'];
      $url .= $_SERVER['REQUEST_URI'];
      if (str_contains($url,'?embed=true')) {
        $response = $event->getResponse();
        $response->headers->remove('X-Frame-Options');
        // $response->headers->set('Content-Security-Policy', 'frame-ancestors https://helsinkibrainandmind.fi/research/');
        // $response->headers->set('X-Frame-Options', 'ALLOW-FROM https://helsinkibrainandmind.fi/research/');
      }
    }
  }

//  /**
//   * The messenger.
//   *
//   * @var \Drupal\Core\Messenger\MessengerInterface
//   */
//  protected $messenger;
//
//  /**
//   * Constructs event subscriber.
//   *
//   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
//   *   The messenger.
//   */
//  public function __construct(MessengerInterface $messenger) {
//    $this->messenger = $messenger;
//  }
//
//  /**
//   * Kernel request event handler.
//   *
//   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
//   *   Response event.
//   */
//  public function onKernelRequest(RequestEvent $event) {
//    $this->messenger->addStatus(__FUNCTION__);
//  }
//
//  /**
//   * Kernel response event handler.
//   *
//   * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
//   *   Response event.
//   */
//  public function onKernelResponse(FilterResponseEvent $event) {
//    $this->messenger->addStatus(__FUNCTION__);
//  }
//
  /**
   * {@inheritdoc}
   */
//  public static function getSubscribedEvents() {
//    return [
//      KernelEvents::REQUEST => ['onKernelRequest'],
//      KernelEvents::RESPONSE => ['onKernelResponse'],
//    ];
//  }
  public static function getSubscribedEvents()
  {
    $events[KernelEvents::RESPONSE][] = array('BnmRemoveXframeOptions', -10);
    return $events;
  }

}
