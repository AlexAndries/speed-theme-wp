(function($) {
  'use strict';
  
  var ToggleClassHandler = function() {
    this.triggerOpen = 'data-open-trigger';
    this.triggerClose = 'data-close-trigger';
    this.target = 'data-open-target';
  };
  
  function generateSelector(attr, value) {
    if (typeof value === 'undefined') {
      return '[' + attr + ']';
    }
    
    return '[' + attr + '=' + value + ']';
  }
  
  ToggleClassHandler.prototype.listenForClick = function() {
    var ctrl = this;
    
    $(document).on('click', generateSelector(ctrl.triggerOpen), function() {
      var classValue = $(this).attr(ctrl.triggerOpen);
      
      $(generateSelector(ctrl.target, classValue)).addClass(classValue);
    });
    
    $(document).on('click', generateSelector(ctrl.triggerClose), function() {
      var classValue = $(this).attr(ctrl.triggerClose);
      $(generateSelector(ctrl.target, classValue)).removeClass(classValue);
    });
  };
  
  ToggleClassHandler.prototype.init = function() {
    this.listenForClick();
  };
  
  appModules.toggleClassHandler = ToggleClassHandler;
  
}(jQuery));