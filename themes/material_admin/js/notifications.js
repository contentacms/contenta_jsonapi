/**
 * @file
 * Notification system
 *
 */



(function ($, Drupal) {


  // Max message length to show in the notification prompt
  // @ToDo make this default configurable in theme settings
  var maxMessageLength = '88';

  Drupal.behaviors.material_notification = {
    attach: function (context, settings) {
      var messages = $('div.messages');
      messages.once('material_notification').each(function () {
        messageMax = maxMessageLength;
        messageContent = $(this).find('.message-content');

        messageContent.each(function () {
          if ($(this).closest($('.messages')).hasClass('messages--status')) {
            statusType = 'messages--status';
            statusText = ' Status ';
          }
          if ($(this).closest($('.messages')).hasClass('messages--warning')) {
            statusType = 'messages--warning';
            statusText = ' Warning ';
          }
          if ($(this).closest($('.messages')).hasClass('messages--error')) {
            statusType = 'messages--error';
            statusText = ' Error ';
          }
          thisMessageSize = messageContent.text().length;

          // Check to see if the message is too long for reasonable reading inside a toast notification
          if (thisMessageSize <= messageMax) {
            thisItem = $(this).closest($('.messages'));
            var itemContent = $(this).text();
            Materialize.toast(itemContent, 5000, statusType);
            messageInbox(statusType, thisItem);
          }
          if (thisMessageSize >= messageMax) {
            // If the notification is too long, provide a notice to view in an easier to read format
            thisItem = $(this).closest($('.messages'));
            var messageTrigger = '<a class="modal-trigger" href="#messageContainer">View</a>';
            var messageNotice = 'There is a' + statusText + 'message in your notification console ' + messageTrigger + '';
            messageInbox(statusType, thisItem);
            Materialize.toast(messageNotice, 5000, statusType);
          }
        });
      });
    }
  };
  //Since Toast removes the item after the notice, clone it put them in the message container
  function messageInbox(statusType, thisItem) {
    thisItem.each(function () {
      $(this).appendTo('#messageContainer .region-status').removeClass('messages').addClass('messages-clone').show();
      itemforMessageCenter = thisItem;
      messageCounter(itemforMessageCenter, statusType);

    });
  }
  //add badge for each message type
  function messageCounter(itemforMessageCenter, statusType) {
    var currentValue = parseInt($('.message-trigger span.badge.' + statusType).text(), 10);
    messageCount = currentValue + 1;
    $('.message-trigger span.badge.' + statusType).text(messageCount).show();
    if (messageCount >= 1) {
      $('#notification-wrapper').show();
    }
  }
})(jQuery, Drupal);
