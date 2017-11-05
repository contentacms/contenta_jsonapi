<?php

namespace Drupal\Tests\contenta_jsonapi\Functional;

use Drupal\Component\Serialization\Json;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

/**
 * Tests that installation finished correctly and known resources are available.
 *
 * @group ContentaInstaller
 */
class InstallationTest extends TestCase {

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = ['graphql_core'];
  /**
   * @var Client
   */
  private $httpClient;

  /**
   * @var string
   */
  private $baseUrl;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    $url = getenv('WEB_HOST');
    $port = getenv('WEB_PORT');
    $this->baseUrl = "http://$url:$port";

    // Set up a HTTP client that accepts relative URLs.
    $this->httpClient = new Client(['http_errors' => FALSE]);
  }

  public function testLandingPage() {
    $response = $this->httpClient->request('GET', $this->baseUrl . '/');
    $this->assertEquals(200, $response->getStatusCode());
  }

  public function testKnownResources() {
    $response = $this->httpClient->request('GET', $this->baseUrl . '/api');
    $body = $response->getBody()->getContents();
    $output = Json::decode($body);
    $resources = array_keys($output['links']);
    $expected_resources = [
      'commentTypes',
      'files',
      'imageStyles',
      'images',
      'mediaBundles',
      'articles',
      'pages',
      'recipes',
      'node--tutorial',
      'contentTypes',
      'categories',
      'tags',
      'vocabularies',
      'roles',
      'users',
    ];
    array_walk(
        $expected_resources, function ($resource) use ($resources) {
            $this->assertContains($resource, $resources);
        }
    );
  }

  public function testGraphQLQuery() {
    $query = '{"query":"query{nodeQuery{entities{entityLabel ... on NodeRecipe { fieldIngredients }}}}","variables":null}';
    $response = $this->httpClient->post($this->baseUrl . '/graphql', [
      'body' => $query,
    ]);
    $this->assertEquals(200, $response->getStatusCode());
    $body = $response->getBody()->getContents();
    $output = Json::decode($body);
    $entities = $output['data']['nodeQuery']['entities'];
    $this->assertFalse(empty($entities));
  }

    public function testGraphQLMutationsPermissionError() {
        $query = <<<EOF
{
  "query": "mutation(\$node: NodePageCreateInput!){ createNodePage (input: \$node) { entity { entityLabel } errors violations { code message }}}",
  "variables": {"node": {"title": "Test page"}}
}
EOF;

        $response = $this->httpClient->post($this->baseUrl . '/graphql', [
            'body' => $query,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $body = $response->getBody()->getContents();

        $output = Json::decode($body);
        $this->assertEquals('You do not have the necessary permissions to create entities of this type.', $output['data']['createNodePage']['errors'][0]);
    }
}
