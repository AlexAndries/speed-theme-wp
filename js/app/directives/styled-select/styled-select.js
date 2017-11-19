(function() {
  'use strict';
  
  angular.module('speed-app')
         .directive('styledSelect', styledSelect);
  
  function styledSelect() {
    return {
      restrict    : 'E',
      templateUrl : themeUrl + 'js/app/directives/styled-select/styled-select.html',
      scope       : {
        values           : '=',
        onChangeHandler  : '&',
        selectedValue    : '=',
        extraClass       : '@',
        allowNull        : '=',
        hasAutoComplete  : '=',
        selectPlaceholder: '@'
      },
      link        : function() {
      },
      controllerAs: 'ss',
      controller  : function($scope) {
        var ss = this;
        
        ss.values = $scope.values;
        ss.selectedValue = $scope.selectedValue;
        ss.open = !1;
        ss.currentFilter = undefined;
        ss.autoComplete = '';
        
        ss.openDropDownClickHandler = openDropDownClickHandler;
        ss.changeFilterClickHandler = changeFilterClickHandler;
        ss.removeFilterClickHandler = removeFilterClickHandler;
        ss.filterAutoComplete = filterAutoComplete;
        
        function openDropDownClickHandler(type) {
          ss.open = type;
        }
        
        function changeFilterClickHandler(item) {
          ss.selectedValue = undefined;
          ss.open = !1;
          ss.currentFilter = angular.copy(item);
          
          if ($scope.hasAutoComplete) {
            ss.autoComplete = ss.currentFilter.label;
          }
          
          if (angular.isFunction($scope.onChangeHandler)) {
            $scope.onChangeHandler({value: item.value});
          }
        }
        
        function removeFilterClickHandler() {
          ss.currentFilter = ss.values[0];
          ss.selectedValue = undefined;
          
          if ($scope.hasAutoComplete) {
            ss.currentFilter = undefined;
            ss.autoComplete = '';
          }
          
          if (angular.isFunction($scope.onChangeHandler)) {
            $scope.onChangeHandler();
          }
        }
        
        function filterAutoComplete(item) {
          return item.label.toLowerCase().indexOf(ss.autoComplete.toLowerCase()) > -1;
        }
        
        function getSelectedValueHandler() {
          for (var i = 0; i < ss.values.length; i++) {
            if (ss.values[i].value === ss.selectedValue) {
              ss.currentFilter = angular.copy(ss.values[i]);
  
              if ($scope.hasAutoComplete) {
                ss.autoComplete = ss.currentFilter.label;
              }
              
              return false;
            }
          }
          ss.currentFilter = ss.values[0];
          
          if ($scope.hasAutoComplete) {
            ss.currentFilter = undefined;
          }
        }
        
        getSelectedValueHandler();
        
        $scope.$watch('selectedValue', function(value) {
          if (typeof value !== 'undefined') {
            ss.selectedValue = value;
            getSelectedValueHandler();
          }
        });
      }
    };
  }
}());
