(function() {
  'use strict';
  
  angular.module('speed-app')
         .service('SubmitFormCommand', SubmitFormCommand);
  
  /** ngInclude **/
  
  function SubmitFormCommand(APIService, FormEntity) {
    return {
      execute: function(action, data, clearForm) {
        FormEntity.loading = true;
        FormEntity.clearErrors();
        APIService
          .action(action)
          .post(FormEntity.prepareDataToSent(data))
          .$promise
          .then(function(response) {
            FormEntity.loading = !1;
            
            if (response.code === 'success') {
              FormEntity.setSuccess();
            }
            
            if (angular.isFunction(clearForm)) {
              clearForm(response);
            }
          })
          .catch(function(error) {
            FormEntity.loading = !1;
            FormEntity.setError(error.data);
          });
      }
    };
  }
}());
