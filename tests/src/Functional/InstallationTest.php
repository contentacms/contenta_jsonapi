<?php

namespace Drupal\Tests\contenta_jsonapi\Functional;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;
use GuzzleHttp\Client;

/**
 * Tests that installation finished correctly and known resources are available.
 *
 * @group Contenta
 */
class InstallationTest extends BrowserTestBase {

  public $profile = 'contenta_jsonapi';

  /**
   * @var Client
   */
  private $httpClient;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Set up a HTTP client that accepts relative URLs.
    $this->httpClient = new Client(['http_errors' => FALSE]);
  }

  public function testLandingPage() {
    $url = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();
    $this->getSession()->visit($url);
    $this->assertEquals(200, $this->getSession()->getStatusCode());
  }

  public function testKnownResources() {
    $url = Url::fromRoute('jsonapi.resource_list', [], ['absolute' => TRUE])
      ->toString();
    $response = $this->httpClient->request('GET', $url);
    $body = $response->getBody()->getContents();
    $output = Json::decode($body);
    $resources = array_keys($output['links']);
    $expected_resources = [
      'commentTypes',
      'files',
      'imageStyles',
      'images',
      'articles',
      'pages',
      'recipes',
      'tutorials',
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

  public function testRpcMethod() {
    $url = Url::fromRoute('jsonrpc.handler', [], ['absolute' => TRUE])->toString();
    $response = $this->httpClient->request(
      'GET',
      $url,
      [
        'query' => [
          'query' => '{"jsonrpc":"2.0","method":"jsonapi.metadata","id":"cms-meta"}'
        ],
      ]);
    $body = $response->getBody()->getContents();
    $output = Json::decode($body);
    $this->assertEquals('/api', $output['result']['prefix']);
    $this->assertEquals('/api', $output['result']['openApi']['basePath']);
    $response = $this->httpClient->request(
      'POST',
      $url,
      ['body' => '{"jsonrpc":"2.0","method":"jsonapi.metadata","id":"cms-meta"}']
    );
    $body = $response->getBody()->getContents();
    $output = Json::decode($body);
    $this->assertEquals('/api', $output['result']['prefix']);
    $this->assertEquals('/api', $output['result']['openApi']['basePath']);
  }

  public function testJsonApiEntryPoint() {
    $url = Url::fromRoute('jsonapi.resource_list', [], ['absolute' => TRUE])
      ->toString();
    $response = $this->httpClient->request(
      'GET',
      $url,
      ['headers' => ['Accept' => 'application/vnd.api+json']]
    );
    $this->assertSame(200, $response->getStatusCode());
    $body = $response->getBody()->getContents();
    $output = Json::decode($body);
    $links = $output['links'];
    $this->assertArrayHasKey('href', $links['self']);
    $this->assertArrayHasKey('href', $links['recipes']);
  }

  public function testOpenApi() {
    $url = Url::fromRoute('contenta_enhancements.api', [], ['absolute' => TRUE])->toString();
    $this->getSession()->visit($url);
    $page = $this->getSession()->getPage();
    $open_api_spec = Json::decode(
      html_entity_decode($page->find('css', '#redoc-ui')->getAttribute('spec'))
    );
    $definitions = [
      'comment--comment',
      'file--file',
      'media--image',
      'node--article',
      'node--embeddable',
      'node--page',
      'node--recipe',
      'node--tutorial',
      'taxonomy_term--category',
      'taxonomy_term--ingredients',
      'taxonomy_term--recipe_category',
      'taxonomy_term--recipe_cuisine',
      'taxonomy_term--tags',
      'user--user',
      'menu_link_content--menu_link_content',
    ];
    array_walk($definitions, function ($definition) use ($open_api_spec) {
      $this->assertArrayHasKey($definition, $open_api_spec['definitions']);
    });
    $this->assertEquals('OpenAPI Documentation', $page->find('css', 'h1.page-title')->getText());
  }

}
