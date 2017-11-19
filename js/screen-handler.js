/**
 * @TODO: update this
 */

(function($) {
  appModules.screenHandler = function() {
    var entity = {
      top: undefined
    };
    
    entity.freezeScreen = function() {
      entity.top = document.documentElement.scrollTop || $(document).scrollTop();
      entity.checkIfScreenHasScroll();
      $('body').css({
        position : 'fixed',
        width    : '100%',
        marginTop: -1 * entity.top
      });
    };
    
    entity.releaseScreen = function() {
      $('body').css({
        position : '',
        width    : '',
        marginTop: '',
        overflowY: '',
        overflowX: ''
      });
  
      document.documentElement.scrollTop = entity.top;
    };
    
    entity.checkIfScreenHasScroll = function() {
      var container = $('body');
      if ($(container).height() > $(window).height()) {
        $(container).css({
          overflowY: 'scroll'
        })
      }
      
      if ($(container).width() > $(window).width()) {
        $(container).css({
          overflowX: 'scroll'
        })
      }
    };
    
    return entity;
  };
})(jQuery);
