(function() {
  'use strict';
  
  angular.module('speed-app')
         .controller('FormController', FormController);
  
  /** ngInclude **/
  
  function FormController(FormEntity, SubmitFormCommand) {
    var vm = this;
    
    vm.thisForm = !1;
    vm.data = {};
    vm.FormEntity = FormEntity;
    
    
    vm.submitFormHandler = submitFormHandler;
    
    function submitFormHandler(action) {
      vm.thisForm = !0;
      SubmitFormCommand.execute(action, vm.data, clearCurrentForm);
    }
    
    function clearCurrentForm() {
      if (vm.FormEntity.success) {
        vm.data = {};
      }
    }
  }
}());