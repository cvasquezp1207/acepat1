var chat = function() {
	var _SOCKET = false,
		_USERNAME = false,
		_CONNECTED = false,
		_TYPING = false,
		_FADE_TIME = 150,
		_TYPING_TIMER_LENGTH = 400,
		_PLACEHOLDER = "",
		_COLORS = [
			'#e21400', '#91580f', '#f8a700', '#f78b00'
			,'#58dc00', '#287b00', '#a8f07a', '#4ae8c4'
			,'#3b88eb', '#3824aa', '#a700ff', '#d300e7'
		];
	
	var default_user = {
		key: false // user unique id
		,fullname: false
		,shortname: false
		,icon: false // user image icon 
		,nick: "" // some text from user
		,online: false
	};
	
	var defaults = {
		showTyping: false // show animation when user is writting
		
		// toolbar options
		,toolbarTitle: false
		,toolbarIcon: "fa fa-wechat"
		,toolbarIconColor: false
		,toolbarIconSize: false
		,toolbarSelector: "ul.nav.navbar-right"
		
		// sidebar options, right panel userlist
		,sidebarIcon: "fa fa-group"
		,sidebarTitle: "Usuarios"
		
		// text input placeholder attribute
		,inputSearch: "Buscar"
		,inputMessage: "Escribir mensaje"
		
		,users: [] // userlist
	};
	
	function isConnect() {
		return (_SOCKET !== false);
	}
	
	function connect(url) {
		if(isConnect() === false && typeof io != "undefined" && url.startsWith("http"))
			_SOCKET = io.connect(url);
		if(isConnect() === true)
			addEvents();
		return isConnect();
	}
	
	function addEvents() {
		_SOCKET.on('login', function(data) {
			_CONNECTED = true;
			_USERNAME = data.username;
			if(data.users.length > 0) {
				$.each(data.users, function(i, v) {
					activeUser(v);
				});
			}
		});
		
		_SOCKET.on('user joined', function(username) {
			activeUser(username);
		});
		
		_SOCKET.on('user left', function(username) {
			$(".avatar-status[pkey='"+username+"']").removeClass("online");
			$("#dropdown-chat>li[pkey='"+username+"']").data("online", false);
		});
		
		_SOCKET.on('add message', function(data) {
			if(data.from_username == _USERNAME) {
				data.username = data.to_username
				data.type = "right";
				data.user = "YO";
				data.active = false;
				addChatMessage(data);
			}
		});
		
		_SOCKET.on('new message', function(data) {
			showAnimationLoad(data.username, false);
			if(data.rows.length > 0) {
				$.each(data.rows, function(i, v) {
					if(v.from_username == _USERNAME) {
						v.username = v.to_username
						v.type = "right";
						v.user = "YO";
						v.active = false;
					}
					else {
						v.type = "left";
						v.user = $("#dropdown-chat>li[pkey='"+v.username+"']").data("shortname");
					}
					addChatMessage(v);
				});
			}
			if(typeof data.more !== "undefined") {
				$(".chat_compose_wrapper[pkey='"+data.username+"']").data("more", data.more);
			}
		});
	}
	
	function createHtml(options) {
		var props = $.extend({}, defaults, options);
		_PLACEHOLDER = props.inputMessage;
		
		// panel users
		$("body").append('<div id="right-chat"><div class="sidebar-container"></div></div>');
		// panel users title
		$("#right-chat .sidebar-container").append('<div class="sidebar-title" style="padding:5px 15px;"><h3>'+
			'<i class="'+props.sidebarIcon+'"></i> '+props.sidebarTitle+'</h3></div>');
		// panel users input search
		$("#right-chat .sidebar-container").append('<div class="setings-item"><div class="form-chat"></div></div>');
		// input search
		var input = $('<input type="text" placeholder="'+props.inputSearch+'" class="form-control input-xs" />');
		$("#right-chat .sidebar-container>.setings-item>.form-chat").append(input);
		// div users
		$("#right-chat .sidebar-container").append('<div class="setings-item dlu"><ul class="dropdown-messages" id="dropdown-chat"></ul></div>');
		
		input.on("keyup", function(e) {
			if(e.which == 13)
				e.preventDefault();
			
			$("#dropdown-chat li").css('display','none');
			
			var buscar = $.trim( cleanInput($(this).val()) );
			
			if(buscar != '') {
				$("#dropdown-chat li:contains('" + buscar + "')").css('display','block');
				$("#dropdown-chat li:contains('" + buscar.toUpperCase() + "')").css('display','block');
			} else
				$("#dropdown-chat li").css('display','block');
		});
		
		if($.isArray(props.users) && props.users.length > 0) {
			$.each(props.users, function(i, v) {
				item = $.extend({}, default_user, v);
				if(item.shortname === false && item.fullname !== false) {
					p = String(item.fullname).indexOf(" ");
					item.shortname = String(item.fullname).substr(0, p);
				}
				
				html = '<div class="dropdown-messages-box" style="margin-bottom:5px;position:relative;"><a class="pull-left">';
				if(item.icon !== false)
					html += '<img alt="image" class="img-circle-chat" src="'+item.icon+'">';
				html += '<span class="avatar-status" pkey="'+item.key+'"><i class="fa fa-circle"></i></span></a>'+
					'<div class="media-body" style="margin-left:25px;"><strong>'+item.fullname+'</strong>'+
					'<div><small class="text-muted">'+item.nick+'</small></div></div></div>';
				
				li = $('<li pkey="'+item.key+'"></li>');
				li.data(item).append(html);
				$("#right-chat .sidebar-container #dropdown-chat").append(li);
			});
			
			$("#right-chat .sidebar-container #dropdown-chat li").on("click", function() {
				var self = $(this);
				var data = self.data();
				createPanelChat(data);
				
				if( ! $(".chat_compose_wrapper[pkey='"+data.key+"']").hasClass("sidebar-open")) {
					$(".chat_compose_wrapper.sidebar-open").removeClass("sidebar-open");
					$(".chat_compose_wrapper[pkey='"+data.key+"']").addClass("sidebar-open");
				}
				
				$(".chat_compose_wrapper[pkey='"+data.key+"'] :input:first").focus();
				
				if($(".alert-messages", self).length) {
					$(".alert-messages", self).removeClass("shake").addClass("fadeOutRight");
					setTimeout(function() {
						$(".alert-messages", self).remove();
						showAlertToolbar();
					}, 2000);
				}
			});
		}
		
		// create icon toolbar
		if($(props.toolbarSelector).length) {
			$(props.toolbarSelector).append('<li class="tooltip-demo"></li>');
			
			var style = '';
			if(props.toolbarIconColor !== false)
				style += 'color:'+props.toolbarIconColor+';';
			if(props.toolbarIconSize !== false)
				style += 'font-size:'+props.toolbarIconSize+';';
			
			var a = $('<a class="right-chat-toggle count-info"></a>');
			a.html('<i class="'+props.toolbarIcon+'" style="'+style+'"></i>');
			
			$('li.tooltip-demo:last', props.toolbarSelector).append(a);
			
			if(props.toolbarTitle !== false) {
				a.attr("title", props.toolbarTitle);
				a.tooltip({placement: "left", container: "body"});
			}
		}
	}
	
	function createPanelChat(options) {
		var props = $.extend({}, default_user, options);
		if($(".chat_compose_wrapper[pkey='"+props.key+"']").length > 0) {
			return;
		}
		
		$("body").append('<div class="chat_compose_wrapper" pkey="'+props.key+'"></div>');
		$(".chat_compose_wrapper[pkey='"+props.key+"']").data(props);
		
		var cls = ($("#dropdown-chat>li[pkey='"+props.key+"']").data("online") == true) ? "online" : "";
		
		var html = '<div class="sidebar-title dropdown-messages-box" style="padding:15px 12px 11px 10px;">'+
			'<a class="pull-left">';
		if(props.icon !== false) {
			html += '<img alt="image" class="img-circle-chat" src="'+props.icon+'" />';
		}
		html += '<span class="avatar-status '+cls+'" pkey="'+props.key+'"><i class="fa fa-circle"></i></span></a>'+
			'<div class="media-body" style="margin-left:25px;">'+
			'<strong style="font-size:11px;">'+props.shortname+'</strong>'+
			'<div><small class="text-muted">'+props.nick+'</small></div>'+
			'</div><div class="full-height-scroll">'+
			'<div class="lg-chat-box"><div class="content"></div></div>'+
			'<div class="form-chat"></div></div>';
		$(".chat_compose_wrapper[pkey='"+props.key+"']").append(html);
		
		var a = $('<a class="pull-right"><i class="fa fa-close"></i></a>');
		var input = $('<input type="text" class="form-control" placeholder="'+_PLACEHOLDER+'">');
		
		$(".chat_compose_wrapper[pkey='"+props.key+"'] .media-body").prepend(a);
		$(".chat_compose_wrapper[pkey='"+props.key+"'] .form-chat").append(input);
		
		$(".chat_compose_wrapper[pkey='"+props.key+"'] .lg-chat-box .content").slimScroll({
			height: '485px',
			railOpacity: 0.4,
			start: 'bottom'
		}).on("slimscroll", function(e, pos) {
			var d = $(this).closest(".chat_compose_wrapper");
			if(pos == "top" && d.data("more")) {
				getMessages(d.data("key"), $(".message-box:first", d).data("id"));
			}
			if(pos == "bottom") {
				showMessageBottom(d.data("key"), false);
			}
		});
		
		a.on("click", function(e) {
			e.preventDefault();
			$(this).closest(".chat_compose_wrapper").removeClass("sidebar-open");
		});
		
		input.on("keypress", function(e) {
			if(e.which == 13) {
				e.preventDefault();
				if($.trim($(this).val()) != "") {
					sendMessage($(this).closest(".chat_compose_wrapper").data("key"), $.trim($(this).val()));
					$(this).val("");
				}
			}
		});
		
		getMessages(props.key);
	}
	
	function cleanInput(str) {
		return $('<div/>').text(str).text();
	}
	
	function addUser(username) {
		if(isConnect() === false)
			return;
		_SOCKET.emit('add user', username);
	}
	
	function sendMessage(toUsername, str) {
		if(isConnect() === false)
			return;
		_SOCKET.emit('new message', {username:toUsername, message:cleanInput(str)});
	}
	
	function getMessages(fromUsername, startId) {
		if(isConnect() === false)
			return;
		startId = startId || false;
		showAnimationLoad(fromUsername);
		_SOCKET.emit('get message', {username:fromUsername, id:startId});
	}
	
	function showAnimationLoad(username, show) {
		if(typeof show == "undefined")
			show = true;
		if($(".chat_compose_wrapper[pkey='"+username+"']").length) {
			if(show) {
				if($(".chat_compose_wrapper[pkey='"+username+"'] .animation-load").length <= 0) {
					var h = '<div class="animation-load sk-spinner sk-spinner-three-bounce">'+
						'<div class="sk-bounce1"></div>'+
						'<div class="sk-bounce2"></div>'+
						'<div class="sk-bounce3"></div>'+
						'</div>';
					$(".chat_compose_wrapper[pkey='"+username+"'] .lg-chat-box div.content").prepend(h);
				}
			}
			else {
				$(".chat_compose_wrapper[pkey='"+username+"'] .animation-load").remove();
			}
		}
	}
	
	function showMessageBottom(username, show) {
		if(typeof show == "undefined")
			show = true;
		if($(".chat_compose_wrapper[pkey='"+username+"']").length) {
			if(show) {
				if($(".chat_compose_wrapper[pkey='"+username+"'] .goto-bottom").length <= 0) {
					var a = $('<a class="goto-bottom"/>').html('<i class="fa fa-arrow-down"></i> Ir al &uacute;ltimo mensaje');
					$(".chat_compose_wrapper[pkey='"+username+"'] .lg-chat-box div.content").append(a);
					a.on("click", function(e) {
						e.preventDefault();
						var el = $(this).closest("div.content");
						el[0].scrollTop = el[0].scrollHeight;
						$(this).remove();
					});
				}
			}
			else {
				$(".chat_compose_wrapper[pkey='"+username+"'] .goto-bottom").remove();
			}
		}
	}
	
	function activeUser(username) {
		$(".avatar-status[pkey='"+username+"']").addClass("online");
		$("#dropdown-chat>li[pkey='"+username+"']").data("online", true);
	}
	
	function addChatMessage(options) {
		if(typeof options.show_alert === 'undefined') {
			options.show_alert = true;
		}
		
		if($(".chat_compose_wrapper[pkey='"+options.username+"']").length) {
			if(typeof options.active === 'undefined') {
				options.active = true;
			}
			if(typeof options.prepend === 'undefined') {
				options.prepend = false;
			}
			
			var cls = options.active ? "active" : "";
			
			var usernameDiv = $('<div class="author-name"/>')
				.html(options.user+' <small class="chat-date">'+formatDate(options.date_time)+'</small>');
			var messageDiv = $('<div class="chat-message '+cls+'"/>')
				.text(options.message);
			var div = $('<div class="message-box '+options.type+'"/>')
				.data("id", options.id).append(usernameDiv, messageDiv);
			
			var elemContainer = $(".chat_compose_wrapper[pkey='"+options.username+"'] .lg-chat-box div.content");
			
			var isBottom = (elemContainer.scrollTop() + elemContainer.innerHeight() + 50 >= elemContainer[0].scrollHeight);
			
			if(options.prepend)
				elemContainer.prepend(div);
			else
				elemContainer.append(div);
			
			if(isBottom) {
				elemContainer[0].scrollTop = elemContainer[0].scrollHeight;
				// elemContainer.slimScroll({ scrollTo: 'bottom' });
			}
			else if(options.prepend == false) {
				showMessageBottom(options.username);
			}
			
			if( ! $(".chat_compose_wrapper[pkey='"+options.username+"']").hasClass("sidebar-open") && options.show_alert) {
				showAlertUser(options);
			}
		}
		else if(options.show_alert) {
			showAlertUser(options);
		}
	}
	
	function getNumDays(date) {
		var p = String(date).split("-");
		var d1 = new Date(parseInt(p[0]), (parseInt(p[1]) - 1), parseInt(p[2]));
		var d2 = new Date();
		var dif = d2.getTime() - d1.getTime();
		return Math.floor(dif / (1000*60*60*24));
	}
	
	function getFechaEs(date) {
		var str = String(date).substr(0, 10);
		return str.split("-").reverse().join("/");
	}
	
	function formatDate(datetime) {
		var p = String(datetime).split(" ");
		
		var d = getNumDays(p[0]);
		var h = String(p[1]).substring(0, 5);
		
		if(d == 0) {
			return h;
		}
		else if(d == 1) {
			return "Ayer "+h;
		}
		return getFechaEs(p[0])+" "+h;
	}
	
	function showAlertUser(options) {
		if($("#dropdown-chat>li[pkey='"+options.username+"']").length) {
			var c = 1, spanAlert;
			if($("#dropdown-chat>li[pkey='"+options.username+"'] .alert-messages").length) {
				spanAlert = $("#dropdown-chat>li[pkey='"+options.username+"'] .alert-messages");
				c += spanAlert.data("count");
			}
			else {
				spanAlert = $('<span class="badge badge-info alert-messages animated"/>');
				$("#dropdown-chat>li[pkey='"+options.username+"']>.dropdown-messages-box").append(spanAlert);
			}
			spanAlert.data("count", c).text(c).removeClass("shake").addClass("shake");
		}
		showAlertToolbar();
	}
	
	function showAlertToolbar() {
		$("a.right-chat-toggle .label.count-messages").remove();
		var c = $("#dropdown-chat>li .alert-messages").length;
		if(c > 0) {
			$("a.right-chat-toggle").append('<span class="label label-primary count-messages animated bounce">'+c+'</span>');
		}
	}
	
	return {
		connect: connect
		,createHtml: createHtml
		,addUser: addUser
	}
}();