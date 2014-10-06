/*!
 * jPushMenu.js
 * 1.1.1
 * @author: takien
 * http://takien.com
 * Original version (pure JS) is created by Mary Lou http://tympanus.net/
 */

(function($) {
		
	$.fn.jPushMenu = function(customOptions) {
		var o = $.extend({}, $.fn.jPushMenu.defaultOptions, customOptions);
		
		/* add class to the body.*/
		
		$('body').addClass(o.bodyClass);
		$(this).addClass('jPushMenuBtn');
		$(this).click(function() {
			var target         = '',
			push_direction     = '';
			
		
			if($(this).is('.'+o.showLeftClass)) {
				target         = '.cbp-spmenu-left';
				push_direction = 'toright';
			}
			else if($(this).is('.'+o.showRightClass)) {
				target         = '.cbp-spmenu-right';
				push_direction = 'toleft';
			}
            else if($(this).is('.'+o.showRightMyInfosClass)) {
                target         = '.cbp-spmenu-right-myinfos';
                push_direction = 'toleft';
            }
            else if($(this).is('.'+o.showRightConfidentialityClass)) {
                target         = '.cbp-spmenu-right-confidentiality';
                push_direction = 'toleft';
            }
            else if($(this).is('.'+o.showRightAddArticleClass)) {
                target         = '.cbp-spmenu-right-article';
                push_direction = 'toleft';
            }
            else if($(this).is('.'+o.showRightNotifsClass)) {
                target         = '.cbp-spmenu-right-notifs';
                push_direction = 'toleft';
            }
            else if($(this).is('.'+o.showRightAmisClass)) {
                target         = '.cbp-spmenu-right-amis';
                push_direction = 'toleft';
            }
            else if($(this).is('.'+o.showRightChatClass)) {
                target         = '.cbp-spmenu-right-chat';
                push_direction = 'toleft';
            }
            else if($(this).is('.'+o.showLeftUploadAvatarClass)) {
                target         = '.cbp-spmenu-left-uploadavatar';
                push_direction = 'toleft';
            }
            else if($(this).is('.'+o.showLeftEditAvatarClass)) {
                target         = '.cbp-spmenu-left-editavatar';
                push_direction = 'toleft';
            }
            else if($(this).is('.'+o.showLeftEditNameClass)) {
                target         = '.cbp-spmenu-left-editname';
                push_direction = 'toleft';
            }
            else if($(this).is('.'+o.showLeftListAvatarsClass)) {
                target         = '.cbp-spmenu-left-listavatars';
                push_direction = 'toleft';
            }
			else if($(this).is('.'+o.showTopClass)) {
				target         = '.cbp-spmenu-top';
			}
			else if($(this).is('.'+o.showBottomClass)) {
				target         = '.cbp-spmenu-bottom';
			}
			

			$(this).toggleClass(o.activeClass);
			$(target).toggleClass(o.menuOpenClass);
			
			if($(this).is('.'+o.pushBodyClass)) {
				$('body').toggleClass( 'cbp-spmenu-push-'+push_direction );
			}
			
			/* disable all other button*/
			$('.jPushMenuBtn').not($(this)).toggleClass('disabled');
			
			return false;
		});
		var jPushMenu = {
			close: function (o) {
				$('.jPushMenuBtn,body,.cbp-spmenu').removeClass('disabled active cbp-spmenu-open cbp-spmenu-push-toleft cbp-spmenu-push-toright');
			}
		}

    if(o.closeOnClickInside) {
       $(document).click(function() {
          jPushMenu.close();
        });

       $('.cbp-spmenu,.toggle-menu').click(function(e){
         e.stopPropagation();
       });
    }
		
		if(o.closeOnClickOutside) {
			 $(document).click(function() { 
				jPushMenu.close();
			 }); 

			 $('.cbp-spmenu,.toggle-menu').click(function(e){ 
				 e.stopPropagation(); 
			 });
		 }

        // On Click Link
        if(o.closeOnClickLink) {
            $('.button_close').on('click',function(){
                jPushMenu.close();
            });
        }
	};
 
   /* in case you want to customize class name,
   *  do not directly edit here, use function parameter when call jPushMenu.
   */
	$.fn.jPushMenu.defaultOptions = {
		bodyClass       : 'cbp-spmenu-push',
		activeClass     : 'menu-active',
		showLeftClass   : 'menu-left',
		showRightClass  : 'menu-right',
		showTopClass    : 'menu-top',
		showBottomClass : 'menu-bottom',
		menuOpenClass   : 'cbp-spmenu-open',
		pushBodyClass   : 'push-body',
        showRightMyInfosClass  : 'menu-right-myinfos',
        showRightConfidentialityClass  : 'menu-right-confidentiality',
        showRightAddArticleClass  : 'menu-right-article',
        showRightNotifsClass  : 'menu-right-notifs',
        showRightAmisClass  : 'menu-right-amis',
        showRightChatClass  : 'menu-right-chat',
        showLeftUploadAvatarClass  : 'menu-left-uploadavatar',
        showLeftEditAvatarClass  : 'menu-left-editavatar',
        showLeftEditNameClass  : 'menu-left-editname',
        showLeftListAvatarsClass  : 'menu-left-listavatars',
		closeOnClickOutside: true,
		closeOnClickInside: true,
		closeOnClickLink: true
	};
})(jQuery);
