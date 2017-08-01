<?php

namespace  Drupal\Tests\contenta_jsonapi\Functional;

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
     * @var Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var array
     */
    private $extras;
  /**
   * {@inheritdoc}
   */
  protected function setUp() {
      echo "Running";
      $Url = getenv('WEB_HOST');
      $Port = getenv('WEB_PORT');
      $this->baseUrl = "http://$Url:$Port";

    // Set up a HTTP client that accepts relative URLs.
    $this->httpClient = new Client();

    $this->extras = ['http_errors' => false];
  }

  public function testLandingPage() {
      $response = $this->httpClient->request('GET', $this->baseUrl .'/', $this->extras);
      $this->assertEquals(200, $response->getStatusCode());
  }
  public function testKnownResources() {
      $response = $this->httpClient->request('GET', $this->baseUrl . '/api', $this->extras);
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
      array_walk($expected_resources, function ($resource) use ($resources) {
          $this->assertContains($resource, $resources);
      });
  }

}
