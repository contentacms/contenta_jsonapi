/**
 * @file
 * Dropbutton override
 *
 */



(function ($, Drupal) {

  $(document).ready(function () {
    //create a random id so that we can multiple dropdowns on the same page
    function uniqueID() {
      var S4 = function () {
        return (((1 + Math.random()) * 0x10000) | 0).toString(5).substring(1);
      };
      return ('dropbutton-' + S4());
    }

    $('ul.dropbutton').each(function (index) {
      var unique = uniqueID();
      $(this).attr('id', unique);
      $(this).siblings('.dropdown-button').attr('data-activates', unique);
    });

    var buttonWidth = $('.dropdown-button').outerWidth();
    $(this).find('.dropdown-content li').css('min-width', buttonWidth);

    $('.dropdown-button').dropdown({
      inDuration: 300,
      outDuration: 225,
      gutter: 0,
      constrain_width: false, // NOTE: this changes to constrainWidth in 0.9 release of materializecss so will need ot update
      belowOrigin: false,
      alignment: 'left',
      stopPropagation: false
    });
  });

})(jQuery, Drupal);
