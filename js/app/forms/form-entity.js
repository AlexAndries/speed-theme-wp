(function() {
  'use strict';
  
  angular.module('speed-app')
         .factory('FormEntity', FormEntity);
  
  function FormEntity() {
    return {
      action     : undefined,
      loading    : !1,
      error      : !1,
      errorTarget: [],
      success    : !1,
      message    : undefined,
      
      prepareDataToSent: function(data) {
        if (!data || data.length === 0) {
          return [];
        }
        
        var keys = Object.keys(data),
          i;
        
        for (i = 0; i < keys.length; i++) {
          data[keys[i]] = data[keys[i]] || undefined;
        }
        
        return data;
        
      },
      
      showErrorMessage: function(target) {
        return this.errorTarget.indexOf(target) > -1;
      },
      
      setError   : function(error) {
        this.success = !1;
        this.error = !0;
        switch (error.code) {
          case 'rest_missing_callback_param':
            this.errorTarget = error.data.params;
            break;
          case 'rest_invalid_param':
            this.errorTarget = Object.keys(error.data.params);
            break;
        }
      },
      setSuccess : function() {
        this.success = true;
      },
      clearErrors: function() {
        this.errorTarget = [];
        this.error = !1;
      }
    };
  }
}());
