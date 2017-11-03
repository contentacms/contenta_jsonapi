<?php

namespace Drupal\contenta_graphql\Plugin\GraphQL\InputTypes;

use Drupal\graphql\Plugin\GraphQL\InputTypes\InputTypePluginBase;

/**
 * Creates input types for entity mutations.
 *
 * @GraphQLInputType(
 *   id = "entity_input",
 *   deriver = "Drupal\contenta_graphql\Plugin\Deriver\InputTypes\EntityInputDeriver"
 * )
 */
class EntityInput extends InputTypePluginBase {

}
