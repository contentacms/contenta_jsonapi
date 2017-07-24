<?php

namespace  Drupal\Tests\contenta_jsonapi\Functional;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Url;
use Drupal\user\Entity\Role;
use Drupal\user\RoleInterface;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the interactive installer installing the standard profile.
 *
 * @group Contenta
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

      $extras = ['http_errors' => false];
      $response = $this->httpClient->request('GET', Url::fromRoute('<front>'), $extras);
      $this->assertEquals(200, $response->getStatusCode());
      $response = $this->httpClient->request('GET', Url::fromRoute('jsonapi.resource_list'), $extras);
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
}
