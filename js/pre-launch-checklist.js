/**
 * @TODO: update this
 */

(function($) {
  var Tests = {
    testH1      : {
      testDescription: 'Detect if there is only one H1 element on page',
      launchTest     : function() {
        var h1Length = $('h1').length;
        if (h1Length === 0) {
          console.warn('There is no h1 element on this page');
          
          return false;
        }
        if (h1Length > 1) {
          console.warn('There are more than one h1 element on this page');
          return false;
        }
        console.info('Test Passed');
        console.log(' ');
      }
    },
    testH2      : {
      testDescription: 'Detect if there are H2 elements on page',
      launchTest     : function() {
        var h2Length = $('h2').length;
        if (h2Length === 0) {
          console.warn('There is no h2 element on this page');
          
          return false;
        }
        console.info('Test Passed');
        console.log(' ');
      }
    },
    testImageAlt: {
      testDescription: 'Detect if there are img elements without alt attribute',
      launchTest     : function() {
        var imagesFail = [];
        
        $('img').each(function() {
          if ($(this).attr('alt') === '') {
            imagesFail.push(this);
          }
        });
        
        if (imagesFail.length > 0) {
          console.warn('Images without alt attribute:');
          console.log(imagesFail);
          return false;
        }
        
        console.info('Test Passed');
        console.log(' ');
      }
    },
    testATitle  : {
      testDescription: 'Detect if there are a elements without title attribute',
      launchTest     : function() {
        var aFail = [];
        
        $('a').each(function() {
          if ($(this).attr('title') === '') {
            aFail.push(this);
          }
        });
        
        if (aFail.length > 0) {
          console.error('a without title attribute:');
          console.log(aFail);
          return false;
        }
        
        console.info('Test Passed');
        console.log(' ');
      }
    }
  };
  
  appModules.runTests = function() {
    for (var prop in Tests) {
      var test = Tests[prop];
      
      if (typeof test.launchTest === 'function') {
        console.info(test.testDescription);
        test.launchTest();
      }
      
    }
  };
})(jQuery);