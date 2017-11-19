/**
 * @TODO: update this
 */

(function($) {
  'use strict';

  var mobileElement = $('.mobile');

  function init() {
    openMenuToggleHandler();
  }

  function openMenuToggleHandler() {
    $('.mobile-icon').on('click', function() {
      $(this).toggleClass('active-menu');
    });
  }

  appModules.openMenu = function() {
    return {
      init: function() {
        init();
      }
    };
  };

}(jQuery));