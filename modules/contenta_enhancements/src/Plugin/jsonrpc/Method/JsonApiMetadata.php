<?php

namespace Drupal\contenta_enhancements\Plugin\jsonrpc\Method;

use Drupal\Core\Annotation\Translation;
use Drupal\jsonapi\ResourceType\ResourceTypeRepository;
use Drupal\jsonrpc\Annotation\JsonRpcMethod;
use Drupal\jsonrpc\Object\ParameterBag;
use Drupal\jsonrpc\Plugin\JsonRpcMethodBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Get metadata about JSON API.
 *
 * @JsonRpcMethod(
 *   id = "jsonapi.metadata",
 *   usage = @Translation("Get metadata about JSON API."),
 *   access = {"access content"},
 *   params = {}
 * )
 */
class JsonApiMetadata extends JsonRpcMethodBase {

  /**
   * The resource type repository.
   *
   * @var \Drupal\jsonapi\ResourceType\ResourceTypeRepository
   */
  protected $resourceTypeRepository;

  /**
   * JsonApiMetadata constructor.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ResourceTypeRepository $resource_type_repository) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->resourceTypeRepository = $resource_type_repository;
  }

  /**
   * @throws \Drupal\jsonrpc\Exception\JsonRpcException
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $resource_type_repository = $container->get('jsonapi.resource_type.repository');
    return new static($configuration, $plugin_id, $plugin_definition, $resource_type_repository);
  }

  /**
   * {@inheritdoc}
   */
  public function execute(ParameterBag $params) {
    // @TODO: Generate the dowloadable URL for the schema on each resource.
    $schemas = [];
    return [
      'prefix' => $this->resourceTypeRepository->getPathPrefix(),
      'schemas' => $schemas,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function outputSchema() {
    return [
      'type' => 'object',
      'properties' => [
        'prefix' => ['type' => 'string'],
        'schemas' => ['type' => 'array', 'items' => ['type' => 'string', 'format' => 'uri']],
      ],
    ];
  }

}
