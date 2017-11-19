/**
 * @TODO: update this
 */

(function($) {
  'use strict';
  
  appModules.owlSliderHandler = function() {
    var entity = {
      selector     : undefined,
      options      : {},
      type         : undefined,
      typeSettings : {},
      init         : function(selector, options, type, typeSettings) {
        this.selector = selector;
        this.options = options;
        this.type = type;
        this.typeSettings = typeSettings;
        if (this.options.forceInit) {
          initSlider();
        } else {
          initSlider();
          
          if (this.options.autoHeight) {
            autoHeightOptionHandler();
          }
        }
        
        if (this.options.loadingBar) {
          this.options.onInitialized = startProgressBar;
          this.options.onTranslate = resetProgressBar;
          this.options.onTranslated = translatedProgressBar;
        }
      },
      refreshSlider: function() {
        refreshSlider();
      },
      destroySlider: function() {
        $(entity.selector).trigger('destroy.owl.carousel');
      }
    };
    
    function translatedProgressBar() {
      $(this.options.loadingBar).css({
        'width'     : '100%',
        'transition': 'width 3.5s ease-in-out 0s'
      });
    }
    
    function startProgressBar() {
      $(this.options.loadingBar).css({
        'width'     : '100%',
        'transition': 'width 5s ease-in-out 0s'
      });
    }
    
    function resetProgressBar() {
      $(this.options.loadingBar).css({
        'width'     : 0,
        'transition': 'width 0s'
      });
    }
    
    function autoHeightOptionHandler() {
      var images = $(entity.selector).find('img'),
        loadingImage = new Image();
      if (!images) {
        return;
      }
      
      images = $(images).first();
      
      if (!images || !$(images).attr('src')) {
        return;
      }
      
      loadingImage.onload = function() {
        setTimeout(function() {
          refreshSlider();
        }, 300);
      };
      
      loadingImage.src = $(images).attr('src');
    }
    
    function initSlider() {
      switch (entity.type) {
        case 'thumb-slider':
          thumbSliderHandler();
          
          refreshSlider();
          break;
        
        case 'main-slider':
          mainSliderHandler();
          
          refreshSlider();
          break;
        
        default:
          refreshSlider();
          break;
      }
    }
    
    function thumbSliderHandler() {
      var slider = $(entity.selector),
        mainSlider = $(entity.typeSettings.slider),
        index = 0;
      
      $(document).on('click', '[data-owl-thumbnail]', function() {
        index = +$(this).attr('data-owl-thumbnail');
        $(slider).find('[data-owl-thumbnail]').removeClass('active');
        $(this).addClass('active');
        
        setSliderIndex(mainSlider, index);
      });
      
      slider.on('changed.owl.carousel', function(event) {
        index = getRealNumberToSlide(event.item.index);
        $(slider).find('[data-owl-thumbnail]').removeClass('active')
                 .eq(index).addClass('active');
        setSliderIndex(mainSlider, index);
      });
    }
    
    function setSliderIndex(slider, index) {
      slider.trigger('to.owl.carousel', getRealNumberToSlide(index));
    }
    
    function getRealNumberToSlide(current) {
      return parseInt(current / (entity.typeSettings.slideBy || 1));
    }
    
    function mainSliderHandler() {
      var slider = $(entity.selector),
        thumbSlider = $(entity.typeSettings.slider),
        index = 0,
        lastVisibleThumbSlider = 0,
        firstVisibleThumbSlider = 0;
      
      slider.on('changed.owl.carousel', function(event) {
        index = getRealNumberToSlide(event.item.index);
        $(thumbSlider).find('[data-owl-thumbnail]').removeClass('active')
                      .eq(index).addClass('active');
        
        lastVisibleThumbSlider = $(thumbSlider).find('.owl-item.active').find('[data-owl-thumbnail]');
        firstVisibleThumbSlider = +$(lastVisibleThumbSlider[0]).attr('data-owl-thumbnail');
        lastVisibleThumbSlider = +$(lastVisibleThumbSlider[lastVisibleThumbSlider.length - 1]).attr('data-owl-thumbnail');
        
        if (lastVisibleThumbSlider && index >= lastVisibleThumbSlider) {
          thumbSlider.trigger('next.owl.carousel');
        }
        
        if (firstVisibleThumbSlider && index < firstVisibleThumbSlider) {
          thumbSlider.trigger('prev.owl.carousel');
        }
        
      });
    }
    
    function refreshSlider() {
      $(entity.selector).trigger('destroy.owl.carousel');
      $(entity.selector).owlCarousel(entity.options);
      $(entity.selector).trigger('refresh.owl.carousel');
    }
    
    return entity;
  };
}(jQuery));
