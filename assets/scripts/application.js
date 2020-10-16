if (!Array.prototype.find) {
  Object.defineProperty(Array.prototype, 'find', {
    enumerable: false,
    configurable: true,
    writable: true,
    value: function(predicate) {
      if (this == null) {
        throw new TypeError('Array.prototype.find called on null or undefined');
      }
      if (typeof predicate !== 'function') {
        throw new TypeError('predicate must be a function');
      }
      var list = Object(this);
      var length = list.length >>> 0;
      var thisArg = arguments[1];
      var value;

      for (var i = 0; i < length; i++) {
        if (i in list) {
          value = list[i];
          if (predicate.call(thisArg, value, i, list)) {
            return value;
          }
        }
      }
      return undefined;
    }
  });
}

//Ajax Based
xhr= {
	XHR_IMG: "assets/images/dyna/5.gif",
	XHR_LOADS: '<img src="assets/images/dyna/5.gif">',
	OPTIONS: {
		method: "POST", type: "JSON", login: true, check: true, error: true, alert: "notice", noticeid: "xhrResponse", noticep: "notice-panel",
		default: {
			method: "POST", type: "JSON", login: true, check: true, error: true, alert: "notice", noticeid: "xhrResponse", noticep: "notice-panel",
		},
		login: {
			method: "GET", type: "HTML", login: false, check: true, error: false, alert: "notice", noticeid: "xhrResponse", noticep: "notice-panel",
		},
		guest: {
			method: "POST", type: "JSON", login: false, check: true, error: true, alert: "notice", noticeid: "xhrResponse", noticep: "notice-panel",
		},
		guestHTML: {
			method: "POST", type: "HTML", login: false, check: true, error: true, alert: "notice", noticeid: "xhrResponse", noticep: "notice-panel",
		},
		user: {
			method: "POST", type: "JSON", login: true, check: true, error: true, alert: "notice", noticeid: "xhrResponse", noticep: "notice-panel",
		},
		userHTML: {
			method: "POST", type: "HTML", login: true, check: true, error: true, alert: "notice", noticeid: "xhrResponse", noticep: "notice-panel",
		},
		silent: {
			method: "POST", type: "HTML", login: false, check: false, error: false, alert: "notice", noticeid: "xhrResponse", noticep: "notice-panel",
		},
		JSON: {
			method: "POST", type: "JSON", login: false, check: true, error: false, alert: "notice", noticeid: "xhrResponse", noticep: "notice-panel",
		}
	},
	toJSON: function(data) {
		try {
			return JSON.parse(data);
		} catch(e1) {
			try {
				return eval('(' + data + ')');
			} catch (e) {
				return {result: 'casterror', msg: data};
			}
		}
	}, 
	validateResult: function(data, options) {
		try {
			var jdata= xhr.toJSON(data);
			if(jdata instanceof Object) {
				if(jdata.result=='casterror' && options.type=="HTML") {
					return data;
				}
				if(!options.check)  {
					return jdata;
				}
				switch(jdata.result) {
					case 'logout':
						xhr.login_form();
						return "logout";
					break;
					case 'error':
					case 'casterror':
						if(options.error) {
							if(options.alert=="alert") {
								application.alert('Error', jdata.msg, 'danger');
							} else {
								application.notice(jdata.msg, "danger", options.noticeid, options.noticep);
							}
						}
						return jdata;
					break;
					case 'norights':
						if(options.error==true) {
							if(options.alert=="alert") {
								application.alert('No Rights', jdata.msg, 'danger');
							} else {
								application.notice(jdata.msg, "danger", options.noticeid, options.noticep);
							}
						}
						return jdata;
					break;
					default:
						return jdata;
					break;
				}
			} else {
				if(options.check) {
					if(options.type=="JSON") {
						if(options.error==true) {
							if(options.alert=="alert") {
								application.alert('Unknown Error', 'Result Data: ' + data, 'danger');
							} else {
								application.notice(jdata.msg, "danger", options.noticeid, options.noticep);
							}
						}
						return {result: 'error', msg: data};
					} else {
						return data;
					}
				} else {
					return options.type=="JSON"?xhr.toJSON(data):data;
				}
			}
		} catch (e) {
			if(options.check) {
				if(options.type=="JSON") {
					if(options.error==true) {
						if(options.alert=="alert") {
							application.alert('Unknown Error', 'Result Data: ' + data, 'danger');
						} else {
							application.notice(jdata.msg, "danger", options.noticeid, options.noticep);
						}
					}
					return {result: 'error', msg: data};
				} else {
					return data;
				}
			} else {
				return options.type=="JSON"?xhr.toJSON(data):data;
			}
		}
	},
	post: function(url, postdata, functions, options) {
		options.method= 'POST';
		xhr.call(url, postdata, functions, options);
	},
	get: function(url, functions, options) {
		options.method= 'GET';
		xhr.call(url, {}, functions, options);
	},
	call: function(url, postdata, functions, options) {
		options= options==undefined?xhr.OPTIONS.default:options;
		xhr.process(url, postdata, functions, options);
	},
	process: function(url, postdata, functions, options) {
		url+= (url.indexOf('?')>0?'&':'?') + "xhr=yes";
		var onComplete= functions.complete==undefined?function(data){}:functions.complete;
		var onCancel= functions.cancel==undefined?function(){}:functions.cancel;
		var fxCall= function(response) {
			var data= options.check?xhr.validateResult(response, options):response;
			if(data instanceof Object) {
				if(data.result=='success') {
					onComplete(data);
				} else {
					onCancel(data);
				}
			} else {
				if(data==="logout") {
					//Nothing to do
				} else {
					onComplete(data);
				}
			}
		};
		if(options.method=='GET') {
			jQuery.get(url, fxCall);
		} else {
			jQuery.post(url, postdata, fxCall);
		}
	},
	login_form: function() {
		application.overlay_close();
		if($("#loginModal").length > 0) {
			$("#loginModal").modal("show");
			return;
		}
		//application.overlay("Session Expired. Loading login window...");
		xhr.post(BASE_PATH+"login.html?viewmode=mini", {}, {
			complete: function(data) {
				application.overlay_close();
				if(data instanceof Object) {
					//ignore
				} else {
					$(data).insertAfter("div:first");
				}
			}
		},
		xhr.OPTIONS.login);
	},
	is_login: function() {
		xhr.post(BASE_PATH+"login.html", {
			action: "loginTest",
			xhr: "yes"
		}, {},
		xhr.OPTIONS.silent);
	},
	lockUnlock: function(src, id, state) {
		xhr.post(BASE_PATH+"rc.html", {
			action: state,
			src: src,
			id: id,
			xhr: "yes"
		}, {},
		xhr.OPTIONS.silent);
	},
	lock: function(src, id) {
		xhr.lockUnlock(src, id, "lock");
	},
	unlock: function(src, id) {
		xhr.lockUnlock(src, id, "unlock");
	}
};

//Date Time Based
dt= {
	Ymd2Dmy: function(val, sep) {
		sep= ((sep==undefined) || (sep==""))?"/":sep;
		if(val=="") {
			return "00"+sep+"00"+sep+"0000";
		}
		return dt.revDate(val, sep);
	},
	Dmy2Ymd: function(val, sep) {
		sep= ((sep==undefined) || (sep==""))?"-":sep;
		if(val=="") {
			return "0000"+sep+"00"+sep+"00";
		}
		return dt.revDate(val, sep);
	},
	revDate: function(val, sep) {
		sep= ((sep==undefined) || (sep==""))?"-":sep;
		if(val=="") {
			return "0000"+sep+"00"+sep+"00";
		}
		if(sep=="/") {
			val= val.replace(/-/gi,"/");
		} else {
			val= val.replace(/\//gi,"-");
		}
		val= val.split(sep);
		val= val[2]+sep+val[1]+sep+val[0];
		return val;
	},
	sec2time: function(duration) {
		var iTmp= 0;
		var Result= '';
		//Hour
		iTmp= parseInt(duration / 3600);
		if (iTmp > 0 && iTmp > 9) { Result= (iTmp) + ':'; }
		else if (iTmp > 0) { Result= '0' + (iTmp) + ':'; }
		//if (iTmp > 9) { Result= (iTmp) + ':'; }  //for 00:00:00 format [comment the above two lines and enable these two]
	  	//else { Result= '0' + (iTmp) + ':'; }
		//Min
		iTmp= parseInt((duration % 3600) / 60);
		if (iTmp > 9) { Result= Result + (iTmp) + ':'; }
		else { Result= Result + '0' + (iTmp) + ':'; }
		//Sec
		iTmp= parseInt((duration % 3600) % 60);
		if (iTmp > 9) { Result= Result + (iTmp); }
		else { Result= Result + '0' + (iTmp); }

		if(Result == '') { Result= '00'; }
		return Result;
	}
};
DateUtils= {
	month_names_short: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
	month_names_long: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	getMonth: function(month, useLongName) {
		useLongName= useLongName==undefined?false:useLongName;
		return useLongName?DateUtils.month_names_long[month]:DateUtils.month_names_short[month];
	},
	zeroPadded: function(input, zeroPadding) {
		zeroPadding= zeroPadding==undefined?false:zeroPadding;
		if(zeroPadding) {
			return (input > 9 ? "" : "0") + input;
		} else {
			return input;
		}
	},
	getDateTime: function(input, asDMY, showYear, hour24, showSeconds, zeroPadding, useLongName){
		return DateUtils.getDate(input, showYear, asDMY, zeroPadding, useLongName) + " " +
				DateUtils.getTime(input, showSeconds, zeroPadding, hour24);
	},
	getDate: function(input, showYear, asDMY, zeroPadding, useLongName){
		format= (asDMY==undefined?true:asDMY)?"dmy":"text";
		return DateUtils._getDate(input, format, showYear, zeroPadding, useLongName);
	},
	getDateDB: function(input){
		return DateUtils._getDate(input, "ymd", true, true, true);
	},
	getTime: function(input, showSeconds, zeroPadding, hour24){
		format= (hour24==undefined?false:hour24)?"hms":"hmsa";
		return DateUtils._getTime(input, format, showSeconds, zeroPadding);
	},
	getTimeDB: function(input){
		return DateUtils._getTime(input, "hms", true, true);
	},
	_getDate: function(input, format, showYear, zeroPadding, useLongName){
		try {
			format= format==undefined?"text":format;
			showYear= showYear==undefined?false:showYear;
			useLongName= useLongName==undefined?false:useLongName;
			zeroPadding= zeroPadding==undefined?false:zeroPadding;
			
			var date= input==undefined?new Date():new Date(Date.parse(input.replace(/-/g, "/")));
			if(date==undefined || date=="Invalid Date" || date.getDate()==NaN) {
				return "";
			}
			
			var day= date.getDate();
				day= DateUtils.zeroPadded(day, zeroPadding);
			var month= date.getMonth();
				month= format=="text" ? DateUtils.getMonth(month, useLongName) : DateUtils.zeroPadded(month + 1, zeroPadding);
			var year= date.getFullYear();
			year= useLongName ? year : "'" + (year+"").substr(2,2);
			
		    var dateStr= "";
		    switch(format) {
		    	case "dmy":
					dateStr= day + "/" + month + (showYear ? "/" + year : "");
				break;
				case "ymd":
					dateStr= (showYear ? year + "/" : "") + month + "/" + day;
				break;
				case "text":
				default:
					dateStr = day + " " + month + (showYear ? ", " + year : "");
				break;
			}
		    return dateStr;  
		} catch(e) {
			console.log(e);
			return "";
		}
	},
	_getTime: function(input, format, showSeconds, zeroPadding){
		try {
			format= format==undefined?"hma":format;
			showSeconds= showSeconds==undefined?false:showSeconds;
			zeroPadding= zeroPadding==undefined?false:zeroPadding;
			
			var date= input==undefined?new Date():new Date(Date.parse(input.replace(/-/g, "/")));
			if(date==undefined || date=="Invalid Date" || date.getHours()==NaN) {
				return "";
			}
			
			var hour24= DateUtils.zeroPadded(date.getHours(), zeroPadding);
			var minute= DateUtils.zeroPadded(date.getMinutes(), true);
			var second= DateUtils.zeroPadded(date.getSeconds(), true);
			var ampm= hour24 >= 12 ? "PM" : "AM";
			var hour12= hour24 > 12 ? hour24 - 12 : hour24;
			
		    var timeStr= "";
		    switch(format) {
				case "hms":
					timeStr= hour24 + ":" + minute + (showSeconds ? ":" + second : "");
				break;
		    	case "hmsa":
				default:
					timeStr= hour12 + ":" + minute + (showSeconds ? ":" + second : "") + " " + ampm;
				break;
			}
		    return timeStr;  
		} catch(e) {
			console.log(e);
			return "";
		}
	}
};
//Application and Document Related
application= {
	currentPage: 'index',
	//dyna doc tit txt
	title: function(t) {
		if(t!=undefined) {
			var oldT= jQuery('title').html();
			oldT= oldT.substr(oldT.indexOf('|') - 1);
			jQuery('title').html(t + oldT);
		}
		var newT= jQuery('title').html();
		newT= newT.substr(newT.indexOf('|') - 1);
		return newT;
	},
	alert_markup: ''+
'<div class="modal fade" id="modal_dialog_alert" tabindex="-1" role="dialog" aria-labelledby="modal_dialog_alert_label" aria-hidden="true">'+
	'<div class="modal-dialog">'+
		'<div class="modal-content">'+
        	'<div class="modal-header img-rounded">'+
				'<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'+
				'<h4 class="modal-title" id="modal_dialog_alert_label">{title}</h4>'+
			'</div>'+
			'<div class="modal-body" id="modal_dialog_alert_content">{body}</div>'+
            '<div class="modal-footer">'+
            	'<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
			'</div>'+
		'</div>'+
	'</div>'+
'</div>',
	alert_close: function() {
		application.alert('close');
	},
	alert: function(title, message, type){
		if(title=='close' && message==undefined && type==undefined) {
			if($("#modal_dialog_alert").length>0) {
				$("#modal_dialog_alert").modal("hide");
			}
			return;
		}
		type= type==undefined?"danger":type;
		if($("#modal_dialog_alert").length>0) {
			$("#modal_dialog_alert").modal("hide");
		}
		$("body").append(application.alert_markup);
		$("#modal_dialog_alert_label").html(title);
		$("#modal_dialog_alert_content").html(message);
		$("#modal_dialog_alert .modal-header").addClass("bg-"+type);
		$("#modal_dialog_alert").modal({
			//backdrop: 'static',keyboard: false, show: true,
		});
		$("#modal_dialog_confirm").on('hidden.bs.modal', function () {
			$("#modal_dialog_alert").remove();
	    });
		$("#modal_dialog_alert").keypress(function(event){
			var keyCode= event.keyCode?event.keyCode:event.which;
			if(keyCode=='13') {
				$("#modal_dialog_alert").modal("hide");
			}
		});
	},
	dialog_markup: ''+
'<div class="modal" id="modal_dialog_confirm" tabindex="-1" role="dialog" aria-labelledby="modal_dialog_title" aria-hidden="true">'+
    '<div class="modal-dialog">'+
        '<div class="modal-content">'+
            '<div class="modal-header bg-danger img-rounded">'+
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'+
                '<h4 class="modal-title" id="modal_dialog_title"></h4>'+
            '</div>'+
            '<div class="modal-body" id="modal_dialog_content"></div>'+
            '<div class="modal-footer">'+
                '<button type="button" class="btn btn-warning" data-dismiss="modal" id="modal_dialog_no"><i class="fa"></i> <span>No</span></button>'+
                '<button type="button" class="btn btn-danger" id="modal_dialog_yes"><i class="fa"></i> <span>Yes, Delete</span></button>'+
            '</div>'+
        '</div>'+
    '</div>'+
'</div>',
	dialog: function(title, message, yesFunction, buttons) {
		if(title=='close' && message==undefined && yesFunction==undefined && buttons==undefined) {
			if($("#modal_dialog_confirm").length>0) {
				$("#modal_dialog_confirm").modal("hide");
			}
			return;
		}
		if($("#modal_dialog_confirm").length>0) {
			$("#modal_dialog_confirm").modal("hide");
		}
		buttons= buttons==undefined?{"yes": "Yes, Delete", "yesICON": "fa-trash-o", "no": "No", "noICON": "fa-ban"}:buttons;
		yesFunction= yesFunction==undefined?function(event){}:yesFunction;
		$("body").append(application.dialog_markup);
		$("#modal_dialog_title").html(title);
		$("#modal_dialog_content").html(message);
		$("#modal_dialog_no span").html(buttons.no);
		$("#modal_dialog_no i").addClass(buttons.noICON);
		$("#modal_dialog_yes span").html(buttons.yes);
		$("#modal_dialog_yes i").addClass(buttons.yesICON);
		$("#modal_dialog_confirm").modal({
			//backdrop: 'static',keyboard: false, show: true,
		});
		$("#modal_dialog_confirm").on('hidden.bs.modal', function () {
			$("#modal_dialog_confirm").remove();
	    });
	    $("#modal_dialog_yes").click(function(event){
	    	yesFunction(event);
	    	$("#modal_dialog_confirm").modal("hide");
	    });
		$("#modal_dialog_confirm").keypress(function(event){
			var keyCode= event.keyCode?event.keyCode:event.which;
			if(keyCode=='13') {
				$("#modal_dialog_yes").click();
			}
		});
	},
	notice: function(message, type, id, parent, url, dismiss) {
		if(message=='close' && id==undefined && parent==undefined && url==undefined && dismiss==undefined) {
			id= type==undefined||type==""?"notice_board":type;
			if($("#"+id).length>0) {
				$("#"+id).remove();
			}
			return;
		}
		id= id==undefined||id==""?"notice_board":id;
		if($("#"+id).length>0) {
			$("#"+id).remove();
		}
		dismiss= dismiss==undefined?true:dismiss;
		type= type==undefined?"info":type;
		type= type=="notice"?"info":type;
		type= type=="message"?"success":type;
		type= type=="error"?"danger":type;
		type= type=="warning"?"warning":type;
		id= id==undefined||id==""?"":' id="'+id+'" ';
		var mrkp= '<div class="alert alert-'+type+' alert-dismissable" '+id+'>';
		mrkp+= dismiss?'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>':'';
		mrkp+= message;
		mrkp+= url==undefined||url==""?'':'<a href="'+url+'" class="alert-link">More</a>';
        mrkp+= '</div>';
		$("#" + (parent==undefined||parent==""?"notice-panel":parent)).append($(mrkp));
		$('html, body').animate({
	        scrollTop: $("#" + (parent==undefined||parent==""?"notice-panel":parent)).offset().top
	    }, 500);
	},
	overlay_markup: '' +
'<div class="modal fade" id="modal_dialog_xhr_overlay" tabindex="-1" role="dialog" aria-hidden="true" >'+
    '<div class="modal-dialog">'+
        '<div class="modal-content">'+
            '<div class="modal-body">'+
            	'<div id="xhr_overlay_icon" class="text-center"><i class="loading_big"></i></div>'+
            	'<div id="xhr_overlay_content" class="text-center"></div>'+
            '</div>'+
        '</div>'+
    '</div>'+
'</div>',
	overlay_timeout: undefined,
	overlay: function(message, icon, cancel, timeout) {
		$("body").css("padding-right", "0px");
		if($("#modal_dialog_xhr_overlay").length>0) {
			clearTimeout(application.overlay_timeout);
			if($("#modal_dialog_xhr_overlay").data()==undefined || 
				$("#modal_dialog_xhr_overlay").data()["bs.modal"]==undefined || 
				$("#modal_dialog_xhr_overlay").data()["bs.modal"].isShown==null) {
				$("#modal_dialog_xhr_overlay").remove();
			} else {
				$("#modal_dialog_xhr_overlay").on('hidden.bs.modal', function () {
					$("#modal_dialog_xhr_overlay").remove();
					application.overlay(message, icon, cancel, timeout);
			    });
				$("#modal_dialog_xhr_overlay").modal("hide");
				return;
			}
		}
		message= message==undefined?"please wait...":message;
		icon= icon==undefined?true:icon;
		cancel= cancel==undefined?false:cancel;
		
		var modal_dialog_xhr_overlay= $(application.overlay_markup);
		$("#xhr_overlay_content", modal_dialog_xhr_overlay).html(message);
		if(!icon) { 
			$("#xhr_overlay_icon", modal_dialog_xhr_overlay).remove();
		}
		
		$("body").append(modal_dialog_xhr_overlay);
		$("#modal_dialog_xhr_overlay").modal({
			backdrop: cancel?true:'static',
			keyboard: cancel?true:false, 
			show: true,
		});
		
		$("#modal_dialog_xhr_overlay").on('hidden.bs.modal', function () {
			$("#modal_dialog_xhr_overlay").remove();
			$("body").css("padding-right", "0px");
	    });
		if(cancel) {
			$("#modal_dialog_xhr_overlay").keypress(function(event){
				var keyCode= event.keyCode?event.keyCode:event.which;
				if(keyCode=='13') {
					$("#modal_dialog_xhr_overlay").modal("hide");
				}
			});
		}
		if(timeout!=undefined && timeout > 0) {
			application.overlay_timeout= setTimeout(function(){
				application.overlay_close();
			}, timeout);
		}
	},
	overlay_close: function() {
		$("body").css("padding-right", "0px");
		if($("#modal_dialog_xhr_overlay").length>0) {
			clearTimeout(application.overlay_timeout);
			if($("#modal_dialog_xhr_overlay").data()==undefined || 
				$("#modal_dialog_xhr_overlay").data()["bs.modal"]==undefined || 
				$("#modal_dialog_xhr_overlay").data()["bs.modal"].isShown==null) {
				$("#modal_dialog_xhr_overlay").remove();
			} else {
				$("#modal_dialog_xhr_overlay").on('hidden.bs.modal', function () {
					$("#modal_dialog_xhr_overlay").remove();
			    });
				$("#modal_dialog_xhr_overlay").modal("hide");
			}
		}
	},
	overlay_update: function(message) {
		$("body").css("padding-right", "0px");
		if($("#modal_dialog_xhr_overlay").length==0) {
			application.overlay(message, false, true, 0);
		} else {
			$("#modal_dialog_xhr_overlay #xhr_overlay_content").text(message);
		}
	},
};

(function($) {
	$(document).ready(function(){
		
	});
})(jQuery);

function addDate(dateStr, what, howmuch) {
	what= what==undefined?"DAY":what;
	howmuch= (howmuch==undefined?1:howmuch) * 1;
	var dt= new Date(dateStr);
	switch(what) {
		case "DAY":
			dt.setDate(dt.getDate()+howmuch);
		break;
		case "MONTH":
			dt.setMonth(dt.getMonth()+howmuch);
		break;
		case "MONTH":
			dt.setFullYear(dt.getFullYear()+howmuch);
		break;
	}
	/*var m= dt.getMonth() + 1;//0 - 11 - so add 1
	var d= dt.getDate();
	dt= new Date(dt.getFullYear() + "-" + (m<10?"0":"") + m + "-" + (d<10?"0":"") + d);*/
	var m= dt.getMonth() + 1;
	var d= dt.getDate();
	return dt.getFullYear() + "-" + (m<10?"0":"") + m + "-" + (d<10?"0":"") + d;
}
function dateRange(from, to, sufix) {
	if(from==""||from==undefined||to==""||to==undefined) {
		return "";
	}
	sufix= sufix==undefined||sufix==""?"":sufix;
	dateList= from + sufix;
	while(from!==to) {
		from= addDate(from, 1);
		dateList+= (dateList==""?"":"\n") + from + sufix;
	}
	return dateList;
}
function durationStr(duration, inMillis) {
	duration= inMillis?duration/1000:duration;
	
	var iTmp= 0;
	var Result= '';
	
	//Hour
	iTmp= parseInt(duration / 3600);
	if (iTmp > 0) { Result= (iTmp) + 'h '; }
	
	//Min
	iTmp= parseInt((duration % 3600) / 60);
	if (iTmp > 0) { Result= Result + (iTmp) + 'm '; }
	//Sec
	iTmp= parseInt((duration % 3600) % 60);
	if (iTmp > 0) { Result= Result + (iTmp) + 's'; }

	if(Result == '') { Result= '0'; }
	return Result;
}
function Ymd2Dmy(inDate) {
	return inDate.replace(/-/g, "/").split("/").reverse().join("/");
}
function my_date_format(input){
	try {
	    var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	    
		var d = new Date(Date.parse(input.replace(/-/g, "/")));
		if(month[d.getMonth()]==undefined || d.getDate()==NaN) {
			return "";
		}
	    var date = d.getDate() + " " + month[d.getMonth()] + ", " + d.getFullYear();
	    var time = d.toLocaleTimeString().toLowerCase().replace(/([\d]+:[\d]+):[\d]+(\s\w+)/g, "$1$2");
	    return (date + " " + time);  
	} catch(e) {
		return "";
	}
};

function formatAFloat(vl) {
	vl= parseFloat(vl);
	var fmt= '2 ';
	var dec= fmt.substring(0, fmt.length-1);
	var sep= fmt[fmt.length-1].trim();
	var exp= new RegExp(sep, 'gi');
	var actualValue= String(vl).replace(exp, '');
	//validations
	
	actualValue= actualValue.split('.');
	if(actualValue.length==1) {
		actualValue.push('');
	}
	var tmp= actualValue[0]==''?'0':actualValue[0];
	actualValue[0]= '';
	var even = 0;
	for(var I=tmp.length-1;I>=0;I--) {
		even++;
		actualValue[0]= tmp[I] + actualValue[0];
		if(even==3) {
			if(I>0) {
				actualValue[0]= sep + actualValue[0];
			}
			even= 0;
		}
	}
	actualValue[1]= actualValue[1] + '000000000';
	if(dec > 0) {
		var c= actualValue[1][parseInt(dec)];
		if(c >= 5) {
			var fact= actualValue[1].substring(0, dec) + '.' + c;
			//alert(vl + ":fact:" + fact);
			fact= Math.round(fact);
			if(fact==100) {
				actualValue[0]= parseInt(actualValue[0]) + 1;
				actualValue[1]= "00";
			} else {
				actualValue[1]= ((actualValue[1][0]=="0")&&(fact<10)?"0":"") + fact;
			}
		} else {
			actualValue[1]= actualValue[1].substring(0, dec);
			//alert(vl + ":c:" + c + ":act:" + actualValue[1]);
		}
	} else { actualValue[1]= actualValue[1].substring(0, dec); }
	actualValue= actualValue.join('.');
	return actualValue;
}

$.fn.asInteger= function() {
	return parseInt(this.val());
}

$.fn.asFloat= function() {
	return parseFloat(this.val());
}

$.fn.formatFloat= function() {
	this.val(formatAFloat(this.val()));
}

$.fn.returnResult= function(condition, text, combo) {
	if(condition) {
		this.parent().addClass('has-error');
		this.focus(function(){
			$(this).parent().removeClass('has-error');
		});
		return text;
	} else {
		$(this).parent().removeClass('has-error');
		return '';
	}
}

$.fn.testEmpty= function(text) {
	return this.returnResult((this.val() == ""), text + ' Cannot be empty.');
}

$.fn.testCbo= function(text) {
	return this.returnResult(((this.val() == null) || (this.val() == "") || (this.val() == "0")), text + ' not selected.', true);
}

$.fn.testText= function(text) {
	var mch= /^[a-zA-Z][a-zA-Z. ]*$/gi;
	return this.returnResult((!this.val().match(mch)), text + ' must only contain characters.');
}

$.fn.testTextMust= function(text) {
	var rtn= this.testEmpty(text);
	if (rtn == "") {
		rtn= this.testText(text);
	}
	return rtn;
}

$.fn.testInt= function(text) {
	var mch= /^\s*(\+|-)?\d+\s*$/gi;
	return this.returnResult(!this.val().match(mch), text + ' not valid.');
}

$.fn.testFloat= function(text) {
	mch=/^\s*(\+|-)?((\d+(\.\d+)?)|(\.\d+))\s*$/gi;
	return this.returnResult(!this.val().match(mch), text + ' not valid.');
}

$.fn.testMail= function(text) {
	mch=/^([0-9a-zA-Z]+([_.-]?[0-9a-zA-Z]+)*@[0-9a-zA-Z]+[0-9,a-z,A-Z.-]*[.]{1}[a-zA-Z]{2,4})+$/gi;
	return this.returnResult(((this.val()!=="") && (!this.val().match(mch))), 'Invalid E-Mail ID for ' + text);
}

function withError(result) {
	if(result=='') {
		return '';
	} else {
		return '<i class="fa fa-warning"></i> ' + result + '<br />';
	}
}

function copyToClipboard(toCopy) {
	var temp= $("<input>");
	temp.appendTo($("body"));
	temp.val(toCopy);
	temp.select();
	document.execCommand("Copy");
	temp.remove();
}
function copy(toCopy) {
	copyToClipboard(toCopy);
}

function gst_validation(gst) {
    var factor = 2, sum = 0, checkCodePoint = 0, i, j, digit, mod, codePoint, cpChars, inputChars;
    cpChars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    inputChars = gst.trim().toUpperCase();

	var digitsStr= "";
    mod = cpChars.length;
    for (i = inputChars.length - 1; i >= 0; i = i - 1) {
        codePoint = -1;
        for (j = 0; j < cpChars.length; j = j + 1) {
            if (cpChars[j] === inputChars[i]) {
                codePoint = j;
            }
        }

        digit = factor * codePoint;
        factor = (factor === 2) ? 1 : 2;
        digit = (digit / mod) + (digit % mod);
        sum += Math.floor(digit);
		
		digitsStr+= codePoint + "=" + digit + "\n";
    }
    checkCodePoint = ((mod - (sum % mod)) % mod);
    return gst + cpChars[checkCodePoint];
}
function checkGst(gst) {
	var gst_new= gst_validation(gst.substring(0,14));
	return gst_new==gst;
}

function isAMobile() {
	return /Mobi|Android/i.test(navigator.userAgent);
}