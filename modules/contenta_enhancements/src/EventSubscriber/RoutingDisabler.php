<?php

namespace Drupal\contenta_enhancements\EventSubscriber;

use Drupal\Core\Routing\RouteBuildEvent;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RoutingDisabler implements EventSubscriberInterface {

  /**
   * A list of disabled routes.
   */
  protected static $disabledRoutes = [
    'user.page',
  ];

  /**
   * Hides routes by denying access to them.
   *
   * @param \Drupal\Core\Routing\RouteBuildEvent $event
   */
  public function onRouteBuildAlter(RouteBuildEvent $event) {
    $collection = $event->getRouteCollection();
    array_walk(static::$disabledRoutes, function ($disabledRouteName) use ($collection) {
      if ($route = $collection->get($disabledRouteName)) {
        $route->setRequirement('_access', 'FALSE');
      }
    });
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[RoutingEvents::ALTER] = ['onRouteBuildAlter'];
    return $events;
  }

}
