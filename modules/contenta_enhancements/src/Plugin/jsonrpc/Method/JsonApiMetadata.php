<?php

namespace Drupal\contenta_enhancements\Plugin\jsonrpc\Method;

use Drupal\contenta_enhancements\Controller\OpenApiDocs;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\jsonapi\ResourceType\ResourceType;
use Drupal\jsonapi\ResourceType\ResourceTypeRepository;
use Drupal\jsonapi_extras\ResourceType\ConfigurableResourceType;
use Drupal\jsonrpc\Annotation\JsonRpcMethod;
use Drupal\jsonrpc\Object\ParameterBag;
use Drupal\jsonrpc\Object\Response;
use Drupal\jsonrpc\Plugin\JsonRpcMethodBase;
use Drupal\openapi\Plugin\openapi\OpenApiGeneratorManager;
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
   * The resource type repository.
   *
   * @var \Drupal\openapi\Plugin\openapi\OpenApiGeneratorManage
   */
  protected $openApiManager;

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * JsonApiMetadata constructor.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ResourceTypeRepository $resource_type_repository, OpenApiGeneratorManager $open_api_plugin_manager, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->resourceTypeRepository = $resource_type_repository;
    $this->openApiManager = $open_api_plugin_manager;
    $this->configFactory = $config_factory;
  }

  /**
   * @throws \Drupal\jsonrpc\Exception\JsonRpcException
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $resource_type_repository = $container->get('jsonapi.resource_type.repository');
    $open_api_plugin_manager = $container->get('plugin.manager.openapi.generator');
    $config_factory = $container->get('config.factory');
    return new static($configuration, $plugin_id, $plugin_definition, $resource_type_repository, $open_api_plugin_manager, $config_factory);
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   *   If the generator plugin cannot be found.
   */
  public function execute(ParameterBag $params) {
    // @TODO: Generate the dowloadable URL for the schema on each resource.
    /** @var \Drupal\openapi\Plugin\openapi\OpenApiGenerator\JsonApiGenerator $generator */
    $generator = $this->openApiManager->createInstance('jsonapi');
    $disabled = OpenApiDocs::listDisabledResources($this->resourceTypeRepository);
    $generator->setOptions(['exclude' => $disabled]);
    $response = new Response('2.0', $this->currentRequest()->id(), [
      'prefix' => $this->resourceTypeRepository->getPathPrefix(),
      'openApi' => $generator->getSpecification(),
    ]);
    // Add some cacheability metatada.
    $jsonapi_config = $this->configFactory->get('jsonapi_extras.settings');
    $response->addCacheableDependency($jsonapi_config);
    // Load all the configurable resource types and add them as a cache
    // dependency.
    $resource_types = array_filter(
      $this->resourceTypeRepository->all(),
      function (ResourceType $resource_type) {
        return $resource_type instanceof ConfigurableResourceType
          && $resource_type->getJsonapiResourceConfig()->id();
      }
    );
    array_reduce(
      $resource_types,
      function (Response $response, ConfigurableResourceType $resource_type) {
        $resource_config = $resource_type->getJsonapiResourceConfig();
        $response->addCacheableDependency($resource_config);
        return $response;
      },
      $response
    );
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public static function outputSchema() {
    return [
      'type' => 'object',
      'properties' => [
        'prefix' => ['type' => 'string'],
        // TODO: Get the Open API specification schema and add it here.
        'openApi' => ['type' => 'object'],
      ],
    ];
  }

}
