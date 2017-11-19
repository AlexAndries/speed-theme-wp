(function() {
  'use strict';

  angular
    .module('speed-app')
    .filter('getNumberPretty', getNumberPretty);

  /** @ngInject */

  function getNumberPretty() {
    return function(input) {
      if (angular.isDefined(input)) {
        
        var parts = input.toString().split('.');
  
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
      }

      return input;
    }
  }
})();
