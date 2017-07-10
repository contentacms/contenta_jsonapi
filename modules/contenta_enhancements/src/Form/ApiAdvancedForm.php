<?php

namespace Drupal\contenta_enhancements\Form;

use \Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class ApiAdvancedForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'contenta_enhancements_api_advanced';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['intro'] = [
      '#markup' => '<p>Drupal offers you three different ways for accessing & manipulating data. You can enable as many or as few as you like.</p>',
    ];

    $jsonapi_description = <<<HTML
<p>
Exposes all data <em>(entities)</em> stored in Drupal according to the opinionated, RESTful <a href="http://jsonapi.org/">jsonapi.org</a> standard.
Great standardized relationship and collection handling.

<ul>
    <li>easy to filter/sort/paginate collections</li>
    <li>easy to follow relationships</li>
    <li>fewer HTTP requests thanks to the above</li>
    <li>once enabled, all data (entities) in Drupal instantly available (Entity Access API provides access control)</li>
</ul>
</p>
HTML;
    $form['jsonapi'] = [
      '#type' => 'fieldset',
    ];
    $form['jsonapi']['description'] = [
      'title' => [
        '#markup' => '<h2>JSON API</h2>',
      ],
      'logo' => [
        '#type' => 'html_tag',
        '#tag' => 'img',
        '#attributes' => [
          'alt' => $this->t('JSON API logo'),
          'height' => 100,
          'src' => file_create_url(drupal_get_path('module', 'contenta_enhancements') . '/img/jsonapi.png'),
          'style' => 'float:right',
        ]
      ],
      'description' => [
        '#markup' => $jsonapi_description,
      ],
    ];
    $form['jsonapi']['actions']['#type'] = 'actions';
    $form['jsonapi']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('JSON API already enabled!'),
      '#disabled' => TRUE,
      '#button_type' => 'primary',
    ];

    $graphql_description = <<<HTML
<p>
Exposes all data <em>(entities)</em> stored in Drupal according to the opinionated, non-RESTful <a href="https://graphql.org/">graphql.org</a> standard.
Great standardized relationship and collection handling.

<ul>
    <li>non-RESTful, which has both advantages (simplicity) and disadvantages (requires custom client-side caching logic)</li>
    <li>easy to filter/sort/paginate collections</li>
    <li>easy to follow relationships</li>
    <li>even fewer HTTP requests thanks to the above</li>
    <li>once enabled, all data (entities) in Drupal instantly available (Entity Access API provides access control)</li>
</ul>
</p>
HTML;
    $form['graphql'] = [
      '#type' => 'fieldset',
    ];
    $form['graphql']['description'] = [
      'title' => [
        '#markup' => '<h2>GraphQL</h2>',
      ],
      'logo' => [
        '#type' => 'html_tag',
        '#tag' => 'img',
        '#attributes' => [
          'alt' => $this->t('GraphQL logo'),
          'height' => 100,
          'src' => file_create_url(drupal_get_path('module', 'contenta_enhancements') . '/img/graphql.svg'),
          'style' => 'float:right',
        ]
      ],
      'description' => [
        '#markup' => $graphql_description,
      ],
    ];
    $form['graphql']['actions']['#type'] = 'actions';
    $form['graphql']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Enable GraphQL'),
      '#button_type' => 'primary',
    ];

    $rest_description = <<<HTML
<p>
Classical <a href="https://en.wikipedia.org/wiki/REST">RESTful web services</a>: resource-centric, collections by creating <q>Views</q>.

<ul>
    <li>support for multiple formats: JSON, HAL+JSON … — more can be added</li>
    <li>can expose non-entity data, by writing plugins</li>
    <li>some formats may allow the inclusion of data from a relationship (e.g. HAL+JSON)</li>
    <li>… but requires every type of data (entity type) to be enabled explicitly</li>
</ul>
</p>
HTML;
    $form['rest'] = [
      '#type' => 'fieldset',
    ];
    $form['rest']['description'] = [
      'title' => [
        '#markup' => '<h2>REST</h2>',
      ],
      'description' => [
        '#markup' => $rest_description,
      ]
    ];
    $form['rest']['actions']['#type'] = 'actions';
    $form['rest']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('I know Drupal well enough, enable REST'),
      '#button_type' => 'primary',
    ];

    // By default, render the form using system-config-form.html.twig.
    $form['#theme'] = 'system_config_form';

    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ((string) $form_state->getValues()['op'] === 'Enable GraphQL') {
      drupal_set_message($this->t('GraphQL is still in development, sorry!'), 'warning');
    }
    else {
      drupal_set_message($this->t('Since using the REST module requires Drupal knowledge anyway, please enable the REST module and any related modules you might need yourself.'), 'warning');
    }
  }

}
