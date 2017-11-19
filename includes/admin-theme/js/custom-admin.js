jQuery(function($) {
	'use strict';
	var CUSTOM_SETTINGS = window.CUSTOM_SETTINGS || {};
	
	CUSTOM_SETTINGS.menuResizer = function() {
		var menu = $("#adminmenuwrap"),
			menuWidth = $(menu).width();
		if($(menu).is(":hidden")) {
			$("body").addClass("menu-hidden")
			         .removeClass("menu-expanded")
			         .removeClass("menu-collapsed");
		}
		else if(menuWidth > 46) {
			$("body").addClass("menu-expanded")
			         .removeClass("menu-hidden")
			         .removeClass("menu-collapsed");
		} else {
			$("body").addClass("menu-collapsed")
			         .removeClass("menu-expanded")
			         .removeClass("menu-hidden");
		}
	};
	CUSTOM_SETTINGS.menuClickResize = function() {
		$('#collapse-menu, #wp-admin-bar-menu-toggle').click(function(e) {
			
			CUSTOM_SETTINGS.menuResizer();
		});
	};
	
	$(document).ready(function() {
		CUSTOM_SETTINGS.menuResizer();
		CUSTOM_SETTINGS.menuClickResize();
	});
	$(window).resize(function() {
		CUSTOM_SETTINGS.menuResizer();
		CUSTOM_SETTINGS.menuClickResize();
	});
	$(window).load(function() {
		CUSTOM_SETTINGS.menuResizer();
		CUSTOM_SETTINGS.menuClickResize();
	});
	
	Pace.on('start', function(){
	});
	Pace.on('hide', function(){
		$("#wpwrap").addClass("loaded");
	});
});