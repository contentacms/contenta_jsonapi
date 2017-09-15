<?php

namespace Drupal\contenta_enhancements\Controller;

use Drupal\Core\Url;
use Drupal\simple_oauth\Entity\Oauth2Client;
use Drupal\user\Entity\User;

class ApiAuthInfo {

  public function info() {
    $build = [];

    $request = \Drupal::request();
    $host = $request->getSchemeAndHttpHost() . $request->getBaseUrl();

    $users_url = Url::fromRoute('entity.user.collection')->toString();
    $clients_url = Url::fromRoute('entity.consumer.collection')->toString();

    $demo_user = User::load(2);
    $demo_client = Oauth2Client::load(1);

    if ($demo_user && $demo_client) {
      $client_id = $demo_client->uuid();
      $client_secret = 'foobar';
      $username = 'demo-user';
      $password = 'demo-user';
      $html = <<<HTML
<p>To get started, this is how you can retrieve an access token for the demo user &amp; client:</p>
<pre><code>
curl -X POST -d "grant_type=password&client_id=$client_id&client_secret=$client_secret&username=$username&password=$password" $host/oauth/token
</code></pre>
<br>
<p><small>This uses an OAuth2 password grant to retrieve an access token and a refresh token. Use your favorite library's OAuth2 support, or learn how OAuth2 works.</small></p>
HTML;
      drupal_set_message(t('You still have the demo user & client. Delete them before going into production! Add <a href=":users">users</a> and <a href=":clients">clients</a>, and take the <a href=":tour">access control tour</a>.', [':users' => $users_url, ':clients' => $clients_url, ':tour' => Url::fromRoute('entity.user.collection')->setOption('query', ['tour' => TRUE])->toString()]), 'warning');
    }
    else {
      $client_id = '{client ID}';
      $client_secret = '{client secret}';
      $username = '{username}';
      $password = '{password}';
      $html = <<<HTML
<p>To get started, this is how you can retrieve an access token for a <a href="$clients_url">client</a> on behalf of a <a href="$users_url">user</a>:</p>
<pre><code>
curl -X POST -d "grant_type=password&client_id=$client_id&client_secret=$client_secret&username=$username&password=$password" $host/oauth/token
</code></pre>
HTML;
    }

    $build['info'] = [
      '#markup' => $html,
      '#suffix' => '<br><p><small>This uses an OAuth2 password grant to retrieve an access token and a refresh token. Use your favorite library\'s OAuth2 support, or learn how OAuth2 works.</small></p>',
    ];

    if (\Drupal::request()->getScheme() !== 'https') {
      drupal_set_message(t('This Contenta instance is not using HTTPS, this is insecure. Do not do this in production!'), 'error');
    }

    return $build;
  }

}
