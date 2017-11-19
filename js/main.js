(function() {
  'use strict';
  
  var slidersConstructors = {};
  
  setTimeout(function() {
    for (var i = 0; i < sliders.length; i++) {
      var currentSlider = sliders[i];
      slidersConstructors[currentSlider.selector] = new appModules.owlSliderHandler();
      slidersConstructors[currentSlider.selector].init(currentSlider.selector, currentSlider.options, currentSlider.type, currentSlider.typeSettings);
    }
  }, 0);
  
  new appModules.modalHandler().init();
  new appModules.parseHashTag().listenToHashChange();
  new appModules.fixedHeaderHandler().init('header', 'scroll-active');
  new appModules.toggleClassHandler().init();
  objectFitImages();
})();