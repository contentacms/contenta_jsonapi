<?php

namespace Drupal\Tests\thunder\FunctionalJavascript;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;

/**
 * Trait with support for handling Entity Browser actions.
 *
 * @package Drupal\Tests\thunder\FunctionalJavascript
 */
trait ThunderEntityBrowserTestTrait {

  /**
   * Open modal entity browser and switch into iframe from it.
   *
   * @param \Behat\Mink\Element\DocumentElement $page
   *   Current active page.
   * @param string $drupalSelector
   *   Drupal selector.
   * @param string $entityBrowser
   *   Entity browser name.
   */
  public function openEntityBrowser(DocumentElement $page, $drupalSelector, $entityBrowser) {
    $this->clickButtonDrupalSelector($page, $drupalSelector);

    $this->getSession()
      ->switchToIFrame('entity_browser_iframe_' . $entityBrowser);

    // Wait that iframe is loaded and jQuery is available.
    $this->getSession()->wait(10000, '(typeof jQuery !== "undefined")');

    $this->assertSession()->assertWaitOnAjaxRequest();
  }

  /**
   * Submit changes in modal entity browser.
   *
   * @param \Behat\Mink\Element\DocumentElement $page
   *   Current active page.
   */
  public function submitEntityBrowser(DocumentElement $page) {
    $this->clickButtonDrupalSelector($page, 'edit-use-selected', FALSE);

    $this->getSession()->switchToIFrame();
    $this->assertSession()->assertWaitOnAjaxRequest();
  }

  /**
   * Upload file inside entity browser.
   *
   * NOTE: It will search for first tab with upload widget and file will be
   * uploaded there. Upload is done over input file field and it has to be
   * visible for selenium to work.
   *
   * @param \Behat\Mink\Element\DocumentElement $page
   *   Current active page.
   * @param string $filePath
   *   Path to file that should be uploaded.
   *
   * @throws \Exception
   */
  public function uploadFile(DocumentElement $page, $filePath) {
    // Click all tabs until we find upload Tab.
    $tabLinks = $page->findAll('css', '.eb-tabs a');
    if (empty($tabLinks)) {
      throw new \Exception(
        sprintf(
          'Unable to find tabs in entity browser iframe on page %s',
          $this->getSession()->getCurrentUrl()
        )
      );
    }

    // Click all tabs until input file field for upload is found.
    $fileFieldSelector = "input[type='file'].dz-hidden-input";
    $fileField = NULL;
    foreach ($tabLinks as $tabLink) {
      /* @var \Behat\Mink\Element\NodeElement $tabLink */
      $tabLink->click();
      $this->assertSession()->assertWaitOnAjaxRequest();

      $fileField = $page->find('css', $fileFieldSelector);

      if (!empty($fileField)) {
        break;
      }
    }

    if (empty($fileField)) {
      throw new \Exception(
        sprintf(
          'The drop-down file field was not found on the page %s',
          $this->getSession()->getCurrentUrl()
        )
      );
    }

    // Make file field visible and isolate possible problems with "multiple".
    $this->getSession()
      ->executeScript('jQuery("' . $fileFieldSelector . '").show(0).css("visibility","visible").width(200).height(30).removeAttr("multiple");');

    $fileField->attachFile($filePath);

    $this->assertSession()->assertWaitOnAjaxRequest();

    // Wait up to 10 sec that "Use selected" button is active.
    $this->getSession()->wait(
      10000,
      '(typeof jQuery === "undefined" || !jQuery(\'input[name="op"]\').is(":disabled"))'
    );

    $this->assertSession()->assertWaitOnAjaxRequest();

    // In case of gallery image upload we should wait additionally so that all
    // command for auto selection are executed.
    $this->getSession()->wait(200);
    $this->assertSession()->assertWaitOnAjaxRequest();
  }

  /**
   * Drag element in document with defined offset position.
   *
   * @param \Behat\Mink\Element\NodeElement $element
   *   Element that will be dragged.
   * @param int $offsetX
   *   Vertical offset for element drag in pixels.
   * @param int $offsetY
   *   Horizontal offset for element drag in pixels.
   */
  protected function dragDropElement(NodeElement $element, $offsetX, $offsetY) {
    $elemXpath = $element->getXpath();

    $jsCode = "var fireMouseEvent = function (type, element, x, y) {"
      . "  var event = document.createEvent('MouseEvents');"
      . "  event.initMouseEvent(type, true, (type !== 'mousemove'), window, 0, 0, 0, x, y, false, false, false, false, 0, element);"
      . "  element.dispatchEvent(event); };";

    // XPath provided by getXpath uses single quote (') to encapsulate strings,
    // that's why xpath has to be quited with double quites in javascript code.
    $jsCode .= "(function() {" .
      "  var dragElement = document.evaluate(\"{$elemXpath}\", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;" .
      "  var pos = dragElement.getBoundingClientRect();" .
      "  var centerX = Math.floor((pos.left + pos.right) / 2);" .
      "  var centerY = Math.floor((pos.top + pos.bottom) / 2);" .
      "  fireMouseEvent('mousedown', dragElement, centerX, centerY);" .
      "  fireMouseEvent('mousemove', document, centerX + {$offsetX}, centerY + {$offsetY});" .
      "  fireMouseEvent('mouseup', dragElement, centerX + {$offsetX}, centerY + {$offsetY});" .
      "})();";

    $this->getSession()->executeScript($jsCode);
  }

}
