(function() {
  'use strict';
  
  angular.module('speed-app')
         .directive('ngModelFormatter', ngModelFormatter);
  
  function ngModelFormatter($filter) {
    return {
      restrict: 'A',
      require : 'ngModel',
      scope   : {
        format: '@',
        place : '@'
      },
      link    : function(scope, element, attrs, ngModelController) {
        var calculatorFormat = function(data) {
            if (angular.isUndefined(data)) {
              return data;
            }
            var returnData;
    
            returnData = $filter('getNumberPretty')(data);
            
            if (scope.place === 'before') {
              returnData = scope.format + returnData;
            } else {
              returnData = returnData + scope.format;
            }
            
            if (reverseFilter(returnData) !== data) {
              ngModelController.$setViewValue(returnData);
            }
            
            return returnData;
          },
          reverseFilter = function(data) {
            if (angular.isUndefined(data)) {
              return data;
            }
            
            var returnData,
              regex;
            
            if (scope.format === '$') {
              regex = new RegExp('\\' + scope.format);
            } else {
              regex = new RegExp(scope.format);
            }
  
            returnData = data.replace(/,/g, '');
            
            returnData = returnData.replace(regex, '');
            
            returnData = +returnData;
            
            if (isNaN(returnData)){
              returnData = 0;
            }
            
            ngModelController.$render();
            
            return returnData;
          };
        
        ngModelController.$parsers.push(reverseFilter);
        ngModelController.$formatters.push(calculatorFormat);
        element.bind('blur', function() {
          element.val(calculatorFormat(reverseFilter(element.val())));
        });
      }
    };
  }
}());
