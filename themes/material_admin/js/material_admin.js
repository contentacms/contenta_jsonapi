/**
 * @file
 * Generic functions DF Admin
 *
 */

(function ($, Drupal) {

  Drupal.behaviors.material_checkbox = {
    attach: function (context) {
      // limitation of drupal placing <label> before checkbox, which is bad idea and doesnt work with materialize checkboxes
      $(context).find(':checkbox:not(.item-switch), select').once('material_checkbox').each(function (k, v) {
        var label = $('label[for="' + this.id + '"]');
        $(this).insertBefore(label);
      });
    }
  };

  //trigger select boxes to be replaced with li for better styling
  // (not intended for cardinality select boxes)
  Drupal.behaviors.material_select_box = {
    attach: function (context) {
      $(context).find('select').once('material_select_box').material_select();
    }
  };

  Drupal.behaviors.material_tooltip = {
    attach: function (context) {
      $(context).find('.tooltipped').once('material_tooltip').tooltip({ delay: 150 });
    }
  };

  Drupal.behaviors.material_textfields = {
    attach: function (context, settings) {
      $(document).ready(function () {
        //account for field prefix, move the absolute label over to be positioned in the box.
        $(context).find('.input-field').once('material_textfields').each(function () {
          if ($(this).find(' > span.field-prefix').length) {
            var prefixWidth = $(this).find(' > span.field-prefix').outerWidth();
            $(this).find(' > label').css('left', prefixWidth + 10);
          }
          Materialize.updateTextFields();
        });
      });
    }
  };

  //without a module, I dont have a method to get the current page title on certain non-node pages, this is a temp workaround.
  // @ToDO Titles in core need to be better descriptive of the actual page.
  $(document).ready(function () {
    var url = window.location.href;
    //remove paramaters from the URL (like ?destination=) to avoid a misleading breadcrumb
    if (url.indexOf("?") >= 0) {
      url = url.substring(0, url.indexOf('?'));
    }
    if (url.indexOf("#") >= 0) {
      url = url.substring(0, url.indexOf('#'));
    }
    var currentPageBeadcrumb = $('.breadcrumb-nav li.current span');
    var currentPageUrlSegment = url.substr(url.lastIndexOf('/') + 1);
    var urlSegmentAsTitle = currentPageUrlSegment.replace(/[_-]/g, " ");
    // In some administartion pages, the title is the same for multiple pages (I.E. content-types management)
    // This is not very helpful, so get see if that last 2 items match and replace it with last URL semgent for better wayfinding.
    var lastLinkItem = $('.breadcrumb-nav li:nth-last-of-type(2)').text();
    if (currentPageBeadcrumb.is(':empty') || (currentPageBeadcrumb.text() === lastLinkItem)) {
      currentPageBeadcrumb.text(urlSegmentAsTitle).addClass('url-segement-title');
    }
  });

  Drupal.behaviors.material_modal = {
    attach: function (context, settings) {
      $(context).find('.modal').once('material_modal').modal({
        dismissible: true,
        opacity: 0.5,
        in_duration: 200,
        out_duration: 200,
      });
    }
  };

  Drupal.behaviors.material_admin_node_actions = {
    attach: function (context, settings) {
      if (drupalSettings && drupalSettings.material_admin && drupalSettings.material_admin.material_admin_node_actions) {
        var actionsSize = $('.sticky-node-actions').outerHeight();
        $(context).find('body.material_admin').once('material_admin_node_actions').css('padding-bottom', actionsSize);
      }
    }
  };

})(jQuery, Drupal);
