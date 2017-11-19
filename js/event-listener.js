(function() {
  'use strict';
  
  var listeners = {};
  
  function publishEvent(event, params) {
    for (var i = 0; i < listeners[event].length; i++) {
      if (typeof listeners[event][i] === 'function') {
        listeners[event][i](params);
      } else {
        console.warn('"' + listeners[event][i] + '" is not a function');
      }
    }
  }
  
  appModules.eventManager = {
    subscribe: function(event, callback) {
      if (typeof  listeners[event] === 'undefined') {
        listeners[event] = [];
      }
      
      listeners[event].push(callback);
      
      return this;
    },
    publish  : function(event, params) {
      if (typeof  listeners[event] === 'undefined') {
        return this;
      }
      
      publishEvent(event, params);
  
      return this;
    }
  };
  
}());
