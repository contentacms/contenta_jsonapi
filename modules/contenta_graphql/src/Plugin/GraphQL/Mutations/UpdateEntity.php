<?php

namespace Drupal\contenta_graphql\Plugin\GraphQL\Mutations;

use Drupal\graphql_core\Plugin\GraphQL\Mutations\Entity\UpdateEntityBase;

/**
 * Create an entity.
 *
 * @GraphQLMutation(
 *   id = "update_entity",
 *   type = "EntityCrudOutput",
 *   secure = true,
 *   nullable = false,
 *   schema_cache_tags = {"entity_types", "entity_bundles"},
 *   deriver = "Drupal\contenta_graphql\Plugin\Deriver\Mutations\UpdateEntityDeriver"
 * )
 */
class UpdateEntity extends UpdateEntityBase {
  use EntityMutationInputTrait;

}
