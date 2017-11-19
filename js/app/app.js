(function() {
  'use strict';
  
  angular.module('speed-app', ['ngResource', 'ngSanitize'])
         .factory('APIService', APIService)
         .controller('AppController', AppController);
  
  /** ngInclude **/
  
  function APIService($resource) {
    var defaultParams = {};
    return {
      action: function(path) {
        return $resource(restUrl + path, defaultParams, {
          get : {
            method : 'GET',
            headers: {
              Authorization: 'Basic ' + hash
            },
            params : {}
          },
          post: {
            method : 'POST',
            headers: {
              Authorization: 'Basic ' + hash
            },
            params : {}
          }
        });
      }
    };
  }
  
  function AppController() {
    var vm = this;
    vm.optionIndex = 1;
  }
}());
