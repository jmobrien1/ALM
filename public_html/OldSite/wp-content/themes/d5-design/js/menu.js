jQuery(document).ready(function(){ jQuery("#design-main-menu ul ul").css({display: "none"}); jQuery('#design-main-menu ul li').hover( function() { jQuery(this).find('ul:first').slideDown(200).css('visibility', 'visible'); jQuery(this).addClass('selected'); }, function() { jQuery(this).find('ul:first').slideUp(200); jQuery(this).removeClass('selected'); }); });



jQuery(document).ready(function(){ jQuery("ul.lboxd ul").css({display: "none"}); jQuery('ul.lboxd li').hover( function() { jQuery(this).find('ul:first').slideDown(200).css('visibility', 'visible'); jQuery(this).addClass('selected'); }, function() { jQuery(this).find('ul:first').slideUp(200); jQuery(this).removeClass('selected'); }); });