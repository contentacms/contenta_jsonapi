<?php

namespace Drupal\Tests\thunder\FunctionalJavascript;

use Behat\Mink\Element\DocumentElement;

/**
 * Trait for manipulation of meta tag configuration and meta tags on page.
 *
 * @package Drupal\Tests\thunder\FunctionalJavascript
 */
trait ThunderMetaTagTrait {

  /**
   * Set meta tag for field name.
   *
   * @param \Behat\Mink\Element\DocumentElement $page
   *   Current active page.
   * @param string $fieldName
   *   Field name.
   * @param string $value
   *   Value for meta tag.
   */
  public function setFieldValue(DocumentElement $page, $fieldName, $value) {
    // If field is checkbox list, then use custom functionality to set values.
    $checkboxes = $page->findAll('xpath', "//input[@type=\"checkbox\" and starts-with(@name, \"{$fieldName}[\")]");
    if (!empty($checkboxes)) {
      $this->setCheckboxMetaTag($page, $fieldName, $value);

      return;
    }

    // If field is date/time field, then set value directly to field.
    $dateTimeFields = $page->findAll('xpath', "//input[(@type=\"date\" or @type=\"time\") and @name=\"{$fieldName}\"]");
    if (!empty($dateTimeFields)) {
      $this->setRawFieldValue($fieldName, $value);

      return;
    }

    // Clear Text Area - if field is "textarea".
    $field = $page->findField($fieldName);
    if ($field->getTagName() === 'textarea') {
      $this->getSession()->evaluateScript("jQuery('[name=\"{$fieldName}\"]').val('');");
    }

    $this->scrollElementInView('[name="' . $fieldName . '"]');
    $page->fillField($fieldName, $value);

    $this->assertSession()->assertWaitOnAjaxRequest();
  }

  /**
   * Set meta tag value for group of checkboxes.
   *
   * Existing selection will be cleared before new values are applied.
   *
   * @param \Behat\Mink\Element\DocumentElement $page
   *   Current active page.
   * @param string $fieldName
   *   Field name.
   * @param string $value
   *   Comma separated values for meta tag checkboxes.
   */
  protected function setCheckboxMetaTag(DocumentElement $page, $fieldName, $value) {
    // UnCheck all checkboxes and check defined.
    $this->getSession()
      ->executeScript("jQuery('input[name*=\"{$fieldName}\"]').prop('checked', false);");

    $checkNames = explode(',', $value);
    foreach ($checkNames as $checkName) {
      $checkBoxName = $fieldName . '[' . trim($checkName) . ']';

      $this->scrollElementInView('[name="' . $checkBoxName . '"]');
      $page->checkField($checkBoxName);
    }
  }

  /**
   * Get field name for meta tag.
   *
   * @param string $metaTagName
   *   Meta tag name.
   * @param string $groupName
   *   Group name where meta tag belongs (fe. basic, advanced, open_graph, ..)
   * @param string $fieldNamePrefix
   *   Field name prefix (fe. field_meta_tags[0])
   *
   * @return string
   *   Full meta tag field name that can be used to set value for it.
   */
  protected function getMetaTagFieldName($metaTagName, $groupName = '', $fieldNamePrefix = '') {
    // Based on examples, this way of forming field name works properly.
    $fieldName = str_replace(['.', ':'], '_', $metaTagName);

    if (empty($groupName) && empty($fieldNamePrefix)) {
      return $fieldName;
    }

    return $fieldNamePrefix . '[' . $groupName . '][' . $fieldName . ']';
  }

  /**
   * Verify that meta tag values defined in configuration are properly set.
   *
   * @param array $metaTagConfiguration
   *   Meta tag configuration.
   */
  public function checkMetaTags(array $metaTagConfiguration) {
    // Check on article are custom meta tags properly populated.
    foreach ($metaTagConfiguration as $metaTagName => $value) {
      $metaTag = explode(' ', $metaTagName);

      $this->checkMetaTag($metaTag[1], $value);
    }
  }

  /**
   * Check single meta tag on page.
   *
   * @param string $name
   *   Meta tag name.
   * @param string $value
   *   Meta tag value.
   */
  protected function checkMetaTag($name, $value) {
    $htmlValue = htmlentities($value);

    $checkXPath = "@content='{$htmlValue}'";
    if (strpos($value, 'LIKE:') === 0) {
      $valueToCheck = substr($htmlValue, strlen('LIKE:'));

      $checkXPath = "contains(@content, '{$valueToCheck}')";
    }

    $this->assertSession()
      ->elementExists('xpath', "//head/meta[(@name='{$name}' or @property='{$name}') and {$checkXPath}]");
  }

  /**
   * Generate meta tag configuration.
   *
   * @param array $configuration
   *   Meta tag configuration.
   *
   * @return array
   *   Generated meta tag configuration.
   */
  public function generateMetaTagConfiguration(array $configuration) {
    $metaTagConfigs = [];

    foreach ($configuration as $config) {
      $metaTagConfigs = array_merge($metaTagConfigs, $config);
    }

    foreach ($metaTagConfigs as $metaTagName => $metaTagValue) {
      if ($metaTagValue === '[random]') {
        $metaTagConfigs[$metaTagName] = $this->getRandomGenerator()->word(10);
      }
    }

    return $metaTagConfigs;
  }

  /**
   * Set meta tag configuration for page.
   *
   * @param \Behat\Mink\Element\DocumentElement $page
   *   Current active page.
   * @param array $fieldValues
   *   Meta tag configuration.
   */
  public function setFieldValues(DocumentElement $page, array $fieldValues) {
    foreach ($fieldValues as $fieldName => $value) {
      $this->setFieldValue($page, $fieldName, $value);
    }
  }

  /**
   * Generate field name and field value mappings for meta tag configuration.
   *
   * @param array $configuration
   *   Meta tag configuration.
   * @param string $fieldNamePrefix
   *   Field name prefix (fe. field_meta_tags[0])
   *
   * @return array
   *   List with field names and values for it.
   */
  public function generateMetaTagFieldValues(array $configuration, $fieldNamePrefix = '') {
    $fieldValues = [];

    foreach ($configuration as $metaTagName => $metaTagValue) {
      $metaTag = explode(' ', $metaTagName);

      if (!empty($fieldNamePrefix)) {
        $fieldValues[$this->getMetaTagFieldName($metaTag[1], $metaTag[0], $fieldNamePrefix)] = $metaTagValue;
      }
      else {
        $fieldValues[$this->getMetaTagFieldName($metaTag[1])] = $metaTagValue;
      }
    }

    return $fieldValues;
  }

  /**
   * Replace tokens inside meta tag configuration.
   *
   * @param array $configuration
   *   Meta tag configuration.
   * @param array $tokens
   *   Tokens that should be replaced in configuration.
   *
   * @return array
   *   Returns meta tag configuration with replace tokens.
   */
  public function replaceTokens(array $configuration, array $tokens) {
    foreach ($configuration as $metaTagName => $metaTagValue) {
      foreach ($tokens as $tokenName => $tokenValue) {
        if (strpos($metaTagValue, $tokenName) !== FALSE) {
          $configuration[$metaTagName] = str_replace($tokenName, $tokenValue, $metaTagValue);
        }
      }
    }

    return $configuration;
  }

}
