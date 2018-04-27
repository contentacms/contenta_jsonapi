<?php

namespace Drupal\contenta_enhancements\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\jsonapi\ResourceType\ResourceType;
use Drupal\jsonapi\ResourceType\ResourceTypeRepository;
use Drupal\jsonapi\ResourceType\ResourceTypeRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for generating documentation pages.
 */
class OpenApiDocs extends ControllerBase {

  protected $resourceTypeRepository;

  /**
   * OpenApiDocs constructor.
   *
   * @param \Drupal\jsonapi\ResourceType\ResourceTypeRepositoryInterface $resourceTypeRepository
   *   The resource type repository.
   */
  public function __construct(ResourceTypeRepositoryInterface $resourceTypeRepository) {
    $this->resourceTypeRepository = $resourceTypeRepository;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('jsonapi.resource_type.repository')
    );
  }

  /**
   * Generating documentation for node bundles.
   *
   * @param string $entity_type_id
   *   The entity type.
   * @param string $node_type
   *   The entity bundle.
   *
   * @return array
   *   The generated documentation.
   */
  public function generateBundleDocs($entity_type_id, $node_type) {
    $query = [
      'options[bundle_name]' => $node_type,
      'options[entity_type_id]' => $entity_type_id,
    ];
    return $this->generateDocsFromQuery($query);
  }

  /**
   * Generate the default docs.
   *
   * @return array
   *   The generated documentation.
   */
  public function generateDocs($entity_mode) {
    $options = [
      'entity_mode' => $entity_mode,
    ];
    $extract_resource_type_id = function (ResourceType $resource_type) {
      return $resource_type->getEntityTypeId();
    };
    $filter_disabled = function (ResourceType $resourceType) {
      // If there is an isInternal method and the resource is marked as internal
      // then consider it disabled. If not, then it's enabled.
      return method_exists($resourceType, 'isInternal') && $resourceType->isInternal();
    };
    $all = $this->resourceTypeRepository->all();
    $disabled_resources = array_filter($all, $filter_disabled);
    $disabled = array_map($extract_resource_type_id, $disabled_resources);
    $options['exclude'] = $disabled;
    return $this->generateDocsFromQuery(['options' => $options]);
  }

  /**
   * Generates the doc for query options.
   *
   * @param array $query
   *   The query options for the OpenAPI generation.
   *
   * @return array
   *   The generated documentation.
   */
  protected function generateDocsFromQuery(array $query) {
    $route_options = [
      'query' => [
          '_format' => 'json',
        ] + $query,
    ];
    $build = [
      '#theme' => 'redoc',
      '#attributes' => [
        'no-auto-auth' => TRUE,
        'scroll-y-offset' => 150,
      ],
      '#openapi_url' => Url::fromRoute('openapi.jsonapi', [], $route_options)
        ->setAbsolute()
        ->toString(),
    ];
    return $build;
  }

}
