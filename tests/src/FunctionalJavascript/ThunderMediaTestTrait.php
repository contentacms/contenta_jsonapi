<?php

namespace Drupal\Tests\thunder\FunctionalJavascript;

/**
 * Trait for handling of Media related test actions.
 *
 * @package Drupal\Tests\thunder\FunctionalJavascript
 */
trait ThunderMediaTestTrait {

  /**
   * Select Medias for field.
   *
   * @param string $fieldName
   *   Field name.
   * @param string $entityBrowser
   *   Entity browser identifier.
   * @param array $medias
   *   List of media identifiers.
   */
  public function selectMedia($fieldName, $entityBrowser, array $medias) {

    /** @var \Behat\Mink\Element\DocumentElement $page */
    $page = $this->getSession()->getPage();

    $this->assertSession()->assertWaitOnAjaxRequest();

    $buttonName = $fieldName . '_entity_browser_entity_browser';
    $this->scrollElementInView("[name=\"{$buttonName}\"]");
    $page->pressButton($buttonName);

    $this->assertSession()->assertWaitOnAjaxRequest();

    $this->getSession()
      ->switchToIFrame('entity_browser_iframe_' . $entityBrowser);
    $this->assertSession()->assertWaitOnAjaxRequest();

    foreach ($medias as $media) {
      $page->find('xpath', "//div[contains(@class, 'views-row') and .//*[@name='entity_browser_select[$media]']]")->click();
    }
    $this->assertSession()->assertWaitOnAjaxRequest();

    $element = 'img';
    if ($entityBrowser == 'multiple_image_browser') {
      $this->getSession()->wait(200);
      $this->assertSession()->assertWaitOnAjaxRequest();

      $page->pressButton('Use selected');
    }
    elseif ($entityBrowser == 'image_browser') {
      $page->pressButton('Select image');
    }
    elseif ($entityBrowser == 'video_browser') {
      $page->pressButton('Select video');
      $element = 'iframe';
    }

    $this->getSession()->switchToIFrame();
    $this->assertSession()->assertWaitOnAjaxRequest();

    $this->waitUntilVisible('div[data-drupal-selector="edit-' . str_replace('_', '-', $fieldName) . '-wrapper"] ' . $element);
  }

  /**
   * Create gallery with selected medias.
   *
   * @param string $name
   *   Name of gallery.
   * @param string $fieldName
   *   Field name.
   * @param array $medias
   *   List of media identifiers.
   */
  public function createGallery($name, $fieldName, array $medias) {

    $page = $this->getSession()->getPage();

    $selector = "input[data-drupal-selector='edit-" . str_replace('_', '-', $fieldName) . "-0-inline-entity-form-name-0-value']";
    $this->assertSession()->elementExists('css', $selector);

    $nameField = $page->find('css', $selector);
    $nameField->setValue($name);

    $this->selectMedia("{$fieldName}_0_inline_entity_form_field_media_images", 'multiple_image_browser', $medias);
  }

}
