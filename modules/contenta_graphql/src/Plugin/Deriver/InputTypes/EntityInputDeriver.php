<?php

namespace Drupal\contenta_graphql\Plugin\Deriver\InputTypes;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\graphql\Utility\StringHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EntityInputDeriver extends DeriverBase implements ContainerDeriverInterface {
  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * The entity field manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $basePluginId) {
    return new static(
      $container->get('entity_type.bundle.info'),
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(
    EntityTypeBundleInfoInterface $entityTypeBundleInfo,
    EntityTypeManagerInterface $entityTypeManager,
    EntityFieldManagerInterface $entityFieldManager
  ) {
    $this->entityTypeBundleInfo = $entityTypeBundleInfo;
    $this->entityTypeManager = $entityTypeManager;
    $this->entityFieldManager = $entityFieldManager;
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($basePluginDefinition) {
    foreach ($this->entityTypeManager->getDefinitions() as $entityTypeId => $type) {
      if (!($type instanceof ContentEntityTypeInterface)) {
        continue;
      }

      foreach ($this->entityTypeBundleInfo->getBundleInfo($entityTypeId) as $bundleName => $bundle) {

        $createFields = [];
        $updateFields = [];
        foreach ($this->entityFieldManager->getFieldDefinitions($entityTypeId, $bundleName) as $fieldName => $field) {
          if ($field->isReadOnly() || $field->isComputed()) {
            continue;
          }

          $type = ucfirst($entityTypeId) . ucfirst($fieldName) . 'FieldInput';
          $fieldStorage = $field->getFieldStorageDefinition();
          $propertyDefinitions = $fieldStorage->getPropertyDefinitions();

          // Skip this field input type if it's a single value field.
          if (count($propertyDefinitions) == 1 && array_keys($propertyDefinitions)[0] === $fieldStorage->getMainPropertyName()) {
            $type = 'String';
          }

          $fieldKey = StringHelper::propCase($fieldName);
          $fieldDefinition = [
            'type' => $type,
            'multi' => $field->getFieldStorageDefinition()->isMultiple(),
            'field_name' => $fieldName,
          ];

          $createFields[$fieldKey] = $fieldDefinition + [
            'nullable' => !$field->isRequired(),
          ];

          $updateFields[$fieldKey] = $fieldDefinition + [
            'nullable' => TRUE,
          ];
        }

        $this->derivatives["$entityTypeId:$bundleName:create"] = [
          //@todo: Check StringHelper::camelCase.
          'name' => ucfirst($entityTypeId) . ucfirst($bundleName) . 'CreateInput',
          'fields' => $createFields,
          'entity_type' => $entityTypeId,
          'entity_bundle' => $bundleName,
          'data_type' => implode(':', ['entity', $entityTypeId, $bundleName]),
        ] + $basePluginDefinition;

        $this->derivatives["$entityTypeId:$bundleName:update"] = [
          'name' => ucfirst($entityTypeId) . ucfirst($bundleName) . 'UpdateInput',
          'fields' => $updateFields,
          'entity_type' => $entityTypeId,
          'entity_bundle' => $bundleName,
          'data_type' => implode(':', ['entity', $entityTypeId, $bundleName]),
        ] + $basePluginDefinition;
      }
    }

    return parent::getDerivativeDefinitions($basePluginDefinition);
  }

}
