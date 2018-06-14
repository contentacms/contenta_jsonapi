<?php

namespace Drupal\contenta_redis\Plugin\jsonrpc\Method;

use Drupal\Core\Annotation\Translation;
use Drupal\jsonrpc\Annotation\JsonRpcMethod;
use Drupal\jsonrpc\Object\ParameterBag;
use Drupal\jsonrpc\Plugin\JsonRpcMethodBase;
use Drupal\redis\Cache\CacheBackendFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Get metadata about Redis.
 *
 * @JsonRpcMethod(
 *   id = "redis.metadata",
 *   usage = @Translation("Get metadata about the Redis installation."),
 *   access = {"access content"},
 *   params = {}
 * )
 */
class RedisMetadata extends JsonRpcMethodBase {

  /**
   * The resource type repository.
   *
   * @var \Drupal\redis\Cache\CacheBackendFactory
   */
  protected $factory;

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * JsonApiMetadata constructor.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CacheBackendFactory $factory, Request $request) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->factory = $factory;
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $factory = $container->get('cache.backend.redis');
    $request = $container->get('request_stack')->getCurrentRequest();
    return new static($configuration, $plugin_id, $plugin_definition, $factory, $request);
  }

  /**
   * {@inheritdoc}
   */
  public function execute(ParameterBag $params) {
    // We only really care about page cache.
    $cache_backend = $this->factory->get('page');
    // Get an empty key to calculate the prefix. Remove the 'page' bin from it.
    $prefix = preg_replace('/:page$/', ':', $cache_backend->getKey());
    return [
      'prefix' => $prefix,
      'cidTemplate' => '{bin}:{cid}',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function outputSchema() {
    return [
      'type' => 'object',
      'properties' => [
        'cidTemplate' => ['type' => 'string'],
      ],
    ];
  }

}
