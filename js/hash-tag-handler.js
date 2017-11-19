/**
 * @target: data-hash-target
 * @trigger: data-hash-trigger
 */

(function($) {
  'use strict';
  
  var ParseHashTag = function() {
    this.dataTarget = 'data-hash-target';
    this.dataTrigger = 'data-hash-trigger';
  };
  
  ParseHashTag.prototype.getHashTag = function(format, url) {
    var hash = !1;
    
    if (typeof url !== 'undefined') {
      hash = url.substring(url.indexOf('#') + 1);
    }
    
    if (window.location.hash) {
      hash = window.location.hash.substring(1);
    }
    
    if (!hash) {
      return false;
    }
    
    if (format === 'ARRAY') {
      return hash.split('/');
    }
    
    return hash;
  };
  
  ParseHashTag.prototype.setHashTag = function(hash) {
    if (typeof hash === 'object' && hash.length > 0) {
      hash = hash.join('/');
    }
    
    window.location.hash = hash;
  };
  
  ParseHashTag.prototype.scrollToHash = function() {
    var target = $('[' + this.dataTarget + '=' + this.getHashTag() + ']');
    
    if (target.length) {
      setTimeout(function() {
        $('html, body').stop().animate({
          scrollTop: $(target).offset().top - 20
        }, 1000);
      }, 0);
    }
  };
  
  ParseHashTag.prototype.listenToHashChange = function() {
    var ctrl = this;
    
    $(document).on('click', '[' + this.dataTrigger + ']', function() {
      ctrl.setHashTag($(this).attr(ctrl.dataTrigger));
      ctrl.scrollToHash();
    });
    
    this.scrollToHash();
  };
  
  appModules.parseHashTag = ParseHashTag;
}(jQuery));