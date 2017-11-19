/**
 * @TODO: update this
 */

/**
 * @trigger: data-modal-trigger
 * @handler: data-modal-handler
 */
(function($) {
  'use strict';
  
  var screenHandler = new appModules.screenHandler(),
    youtubeApi = new appModules.youtubeVideoApi();
  
  function centerModalIfNeedIt(modal) {
    var container = $(modal).find('.modal__container'),
      windowHeight = $(window).height(),
      maxImgSize,
      sizes = {
        padding  : 50,
        title    : $(container).find('.modal__title').height() + 15 || 0,
        paragraph: $(container).find('.modal__paragraph').height() || 0,
        share    : $(container).find('.gallery-item__share').height() || 0,
        image    : 15
      };
    
    maxImgSize = windowHeight - (sizes.padding + sizes.title + sizes.paragraph + sizes.share + sizes.image + sizes.thumbs);
    $(container).css('max-height', windowHeight - 20)
                .find('.modal__image img')
                .css({
                  'width'    : 'auto',
                  'maxHeight': maxImgSize
                });
    $('.modal__scroll-container').css('max-height', windowHeight - 20);
    $(container).find('.owl-stage-outer').css('max-height', maxImgSize - 20)
                .find('img').css('max-height', maxImgSize - 20);
    
    $(window).on('resize', function() {
      setTimeout(function() {
        centerModalIfNeedIt(modal);
      }, 0);
    });
  }
  
  function openModalHandler(modal) {
    screenHandler.freezeScreen();
    $(modal).fadeIn(500);
    $('body').addClass('modal-open');
  
    appModules.eventManager
              .publish('open-modal', $(modal).attr('data-modal-handler'))
              .publish('open-modal-' + $(modal).attr('data-modal-handler'));
    
    setTimeout(function() {
      centerModalIfNeedIt(modal);
    }, 0);
  }
  
  function closeModalHandler(modal) {
    youtubeApi.stopVideo();
    $(modal).fadeOut(500);
    screenHandler.releaseScreen();
    
    $('body').removeClass('modal-open');
    
    appModules.eventManager
              .publish('close-modal', $(modal).attr('data-modal-handler'))
              .publish('close-modal-' + $(modal).attr('data-modal-handler'));
  }
  
  function getModalHandler(modalType) {
    return '[data-modal-handler=' + modalType + ']';
  }
  
  function getCurrentOpenedModal() {
    var modal = false;
    
    $('[data-modal-handler]').each(function() {
      if (this.style.display) {
        modal = this;
      }
    });
    
    return modal;
  }
  
  function initHandler() {
    $(document).on('click', '[data-modal-trigger]', function() {
      var modalType = $(this).attr('data-modal-trigger'),
        modal = getModalHandler(modalType),
        trigger = this;
      switch (modalType) {
        case 'youtube-video':
          youtubeApi.init($(trigger).attr('data-video'), 'modal-player');
          openModalHandler(modal);
          
          break;
        
        default:
          openModalHandler(modal);
      }
    });
    
    $('.modal__close, .modal__overlay, .modal__close-button, [data-modal-close]').on('click', function() {
      closeModalHandler($(this).parents('.modal'));
    });
  }
  
  appModules.eventManager
            .subscribe('close-modal', function() {
              if ($('body').hasClass('modal-open')) {
                var modal = getCurrentOpenedModal();
                closeModalHandler(modal);
              }
            });
  
  appModules.modalHandler = function() {
    return {
      init: function() {
        if (typeof appModules.screenHandler === 'function') {
          initHandler();
        } else {
          console.error('appModules.screenHandler module not found');
        }
      }
    };
  };
  
}(jQuery));
