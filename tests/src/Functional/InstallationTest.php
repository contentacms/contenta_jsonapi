<?php

use Drupal\Component\Serialization\Json;
use Drupal\Core\Url;
use Drupal\user\Entity\Role;
use Drupal\user\RoleInterface;
use Drupal\Tests\BrowserTestBase;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

/**
 * Tests the interactive installer installing the standard profile.
 *
 * @group ContentaInstaller
 */
class InstallationTest extends BrowserTestBase {

  /**
   * Test paths in the Standard profile.
   */
  protected $profile = 'contenta_jsonapi';

  /**
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    // Set up a HTTP client that accepts relative URLs.
    $this->httpClient = $this->container->get('http_client_factory')
      ->fromOptions(['base_uri' => $this->baseUrl]);
    $this->grantPermissions(Role::load(RoleInterface::ANONYMOUS_ID), [
      'access jsonapi resource list',
    ]);
  }

  public function testKnownResources() {
    $response = $this->request('GET', Url::fromRoute('<front>'), []);
    $this->assertEquals(200, $response->getStatusCode());
    $response = $this->request('GET', Url::fromRoute('jsonapi.resource_list'), []);
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
      'node',
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

  /**
   * Performs a HTTP request. Wraps the Guzzle HTTP client.
   *
   * Why wrap the Guzzle HTTP client? Because any error response is returned via
   * an exception, which would make the tests unnecessarily complex to read.
   *
   * @see \GuzzleHttp\ClientInterface::request()
   *
   * @param string $method
   *   HTTP method.
   * @param \Drupal\Core\Url $url
   *   URL to request.
   * @param array $request_options
   *   Request options to apply.
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  protected function request($method, Url $url, array $request_options) {
    try {
      $response = $this->httpClient->request($method, $url->toString(), $request_options);
    }
    catch (ClientException $e) {
      $response = $e->getResponse();
    }
    catch (ServerException $e) {
      $response = $e->getResponse();
    }

    return $response;
  }

}
