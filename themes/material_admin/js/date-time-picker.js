/**
 * @file
 * Date And Time Picker
 *
 */



(function ($, Drupal) {

  Drupal.behaviors.material_pickadate = {
    attach: function (context, settings) {
      $('.form-date').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        formatSubmit: 'yyyy-mm-dd',
        hiddenName: true
      });
    }
  };

  Drupal.behaviors.material_pickatime = {
    attach: function (context, settings) {
      $('.form-time').pickatime({
        autoclose: true,
        twelvehour: false,
        closeOnSelect: true,
        formatSubmit:'h:i A'
      });
    }
  };

})(jQuery, Drupal);
