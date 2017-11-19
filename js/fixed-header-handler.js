(function($) {
  'use strict';
  
  var FixedHeaderHandler = function() {
    this.element = undefined;
    this.scrollClass = undefined;
  };
  
  FixedHeaderHandler.prototype.setClass = function() {
    $(this.element).addClass(this.scrollClass);
  };
  
  FixedHeaderHandler.prototype.removeClass = function() {
    $(this.element).removeClass(this.scrollClass);
  };
  
  FixedHeaderHandler.prototype.init = function(element, scrollClass) {
    this.element = element;
    this.scrollClass = scrollClass;
    
    this.listenForScroll();
  };
  
  FixedHeaderHandler.prototype.listenForScroll = function() {
    var ctrl = this;
    
    $(window).on('scroll', function() {
      if (document.documentElement.scrollTop || $(document).scrollTop() || $('body').hasClass('modal-open')) {
        ctrl.setClass();
      } else {
        ctrl.removeClass();
      }
    });
  };
  
  appModules.fixedHeaderHandler = FixedHeaderHandler;
}(jQuery));