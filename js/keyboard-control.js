/**
 * @TODO: update this
 */

(function($) {
  var items = ['.owl-carousel.owl-loaded'],
    doubleKey = 0,
    isScrolledIntoView = function(el) {
      var elemTop = el.getBoundingClientRect().top;
      var elemBottom = el.getBoundingClientRect().bottom;
      
      return (elemTop >= 0) && (elemBottom <= window.innerHeight);
    },
    returnVisibleItem = function() {
      var element = undefined;
      for (var i = 0; i < items.length; i++) {
        $(items[i]).each(function() {
          if ($(this).is(':visible') && isScrolledIntoView(this)) {
            element = this;
          }
        })
      }
      
      return element;
    },
    detectDoubleKey = function(currentKey, key) {
      if (doubleKey !== 0 && currentKey === key) {
        return true;
      } else {
        doubleKey = 1;
        setTimeout(function() {
          doubleKey = 0;
        }, 500);
      }
      
      return !1;
    },
    resetTabsClassForBody = function(reset) {
      var body = $('body');
      if (reset) {
        if ($(body).hasClass('tabs-start')) {
          $(body).removeClass('tabs-start')
        }
      } else {
        if (!$(body).hasClass('tabs-start')) {
          $(body).addClass('tabs-start')
        }
      }
      
    };
  
  $(document).keyup(function(e) {
      var item;
      switch (e.which) {
        case 37: // arrow left
          item = returnVisibleItem();
          $(item).find('.owl-prev').click();
          resetTabsClassForBody(true);
          break;
        case 39: // arrow right
          item = returnVisibleItem();
          $(item).find('.owl-next').click();
          resetTabsClassForBody(true);
          break;
        case 27: // escape
          resetTabsClassForBody(true);
          break;
        case 9: //tab
          resetTabsClassForBody();
          break;
        case 16:
          if (detectDoubleKey(e.which, 16)) { //shift key
          
          }
          break;
        case 13: //enter
          $(e.target).trigger('click');
          break;
        default:
          resetTabsClassForBody(true);
          break;
      }
    })
    .on('click', function() {
      resetTabsClassForBody(true);
    });
})(jQuery);
