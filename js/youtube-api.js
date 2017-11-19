/**
 * @TODO: update this
 */

(function($) {
  'use strict';
  
  appModules.youtubeVideoApi = function() {
    var entity = {
      player   : undefined,
      running  : false,
      container: undefined
    };
    
    function getVideoBestWidth() {
      return (window.innerWidth * 90 / 100).toFixed(0);
    }
    
    function getVideoBestHeight() {
      return (window.innerHeight * 70 / 100).toFixed(0);
    }
    
    function onPlayerReady(event) {
      entity.running = true;
      $(entity.container).addClass('active');
      
      event.target.playVideo();
    }
    
    entity.init = function(videoId, targetId) {
      entity.container = '#' + targetId;
      entity.player = new YT.Player(targetId, {
        height : getVideoBestHeight(),
        width  : getVideoBestWidth(),
        videoId: videoId,
        events : {
          'onReady': onPlayerReady
        }
      });
    };
    
    entity.stopVideo = function() {
      if (entity.running) {
        setTimeout(function() {
          entity.player.stopVideo();
          entity.player.destroy();
          entity.running = false;
          $(entity.container).removeClass('active');
        }, 500);
      }
    };
    
    return entity;
  };
}(jQuery));
