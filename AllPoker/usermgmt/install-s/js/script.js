/**
 * Script
 *
 * @author Shahzaib
 */

"use strict";

/**
 * Show Error Message ( Ajax ).
 *
 * @param  object
 * @param  string  message
 * @return void
 */
function showErrorMessage($form, message) {
  var $response = $('.response-message');
  var $alertBody = '';
  var $icon = '<i class="fas fa-times-circle mr-1"></i>';

  $alertBody += '<div class="alert alert-danger">';
  $alertBody += $icon + ' ' + message;
  $alertBody += '</div>';

  $response.html($alertBody);
}

/**
 * Get Spinner Markup
 *
 * @return string HTML
 */
function getSpinnerMarkup() {
  var $markup;

  $markup = '<div class="loadingio-spinner-rolling-icon">';
  $markup += '<div class="ldio-icon-inner">';
  $markup += '<div></div>';
  $markup += '</div>';
  $markup += '</div>';

  return $markup;
}

/**
 * Reset Response Message
 *
 * Use to reset/clear the recent response message.
 *
 * @return void
 */
function resetResponseMessages() {
  $('.response-message').html('');
}

/**
 * JSON Response
 *
 * Use to verify the JSON validity.
 *
 * @param  string response
 * @return mixed
 */
function jsonResponse(response) {
  var status;

  try {
    status = $.parseJSON(response);
  }
  catch (e) {
    status = false;
  }

  return status;
}

/**
 * Form Ajax Request
 *
 * @param  object $form
 * @return void
 */
function formAjaxRequest($form) {
  resetResponseMessages();

  var formInput = $form.serializeArray();
  var submitButton = $form.find('[type="submit"]');
  var action = $form.attr('action');
  var method = $form.attr('method');
  var btnPreText = $(submitButton).html();
  var JSON;

  JSON = {
    url: action,
    method: method,
    data: formInput,
    beforeSend: function () {
      submitButton.attr('disabled', 'disabled');
      submitButton.html(getSpinnerMarkup());
    },
    error: function (response) {
      showErrorMessage($form, response.status + ' - ' + response.statusText);
    },
    success: function (response) {
      var response = jsonResponse(response);

      if (response.status === 'true') {
        window.location = response.value;
        return false;
      }
      else if (response.status === 'false') {
        showErrorMessage($form, response.value);
      }
    }
  };

  $.ajax(JSON).done(function () {
    submitButton.removeAttr('disabled');
    $(submitButton).html(btnPreText);
  });
}


$(function () {

  // Ajax requests handling:
  $(document).on('submit', 'form', function (event) {
    event.preventDefault();
    formAjaxRequest($(this));
  });

});