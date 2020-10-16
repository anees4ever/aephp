/*!
 * Start Bootstrap - SB Admin 2 v3.3.7+1 (http://startbootstrap.com/template-overviews/sb-admin-2)
 * Copyright 2013-2016 Start Bootstrap
 * Licensed under MIT (https://github.com/BlackrockDigital/startbootstrap/blob/gh-pages/LICENSE)
 */
$(function() {
	if($('#side-menu').length>0) {
		try {
			$('#side-menu').metisMenu();
		}  catch(e) {
			
		}
	}
});
	//Loads the correct sidebar on window load,
	//collapses the sidebar on window resize.
	// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        try {
	        var topOffset = 50;
	        var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
	        if (width < 768) {
	            $('div.navbar-collapse').addClass('collapse');
	            topOffset = 100; // 2-row-menu
	        } else {
	            $('div.navbar-collapse').removeClass('collapse');
	        }

	        var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
	        height = height - topOffset;
	        if (height < 1) height = 1;
	        if (height > topOffset) {
	            $("#page-wrapper").css("min-height", (height) + "px");
	        }
		} catch(e) {
			
		}
        resetHeightOfIframe();
    });

    var url = window.location.toString();
	url= url.indexOf("?")>0?url.split("?")[0]:url;
	url= url.indexOf("#")>0?url.split("#")[0]:url;
	
    // var element = $('ul.nav a').filter(function() {
    //     return this.href == url;
    // }).addClass('active').parent().parent().addClass('in').parent();
    var element = $('ul.nav a').filter(function() {
        return this.href == url;
    }).addClass('active').parent();

    while (true) {
        if (element.is('li')) {
            element = element.addClass('active').parent().addClass('in').parent();
        } else {
            break;
        }
    }
    
    $('ul#main-menu li').filter(function() {
        return $("a", this).attr("href") == url;
    }).addClass('active');
    
    resetHeightOfIframe();
});

function resetHeightOfIframe() {
	try {
		if(window.top!=window.self || window.frameElement!=null) {
	    	//console.log("iFrame found");
			if(window.frameElement!=null) {
				//console.log("iFrame not null");
				var height= $(window.frameElement).contents().outerHeight();
				$(window.frameElement).height(height<600?600:height);
				//console.log("set height");
			}
		} else {
	    	//console.log("iFrame not found");
		}
	} catch(e) {
		//console.log(e);
	}
}
