var skin_selected 		= _skin;
var skin_class_selected	= _class_skin;
$(function() {
	$(".btn-config-save").on("click", function(e) {
		e.preventDefault();
		var form = $(this).closest("form");
		
		var local = {}, post = [];
		$(".input-config", form).each(function() {
			if($.trim($(this).val()) != "")
				local[$(this).attr("name")] = $(this).val();
			
			post.push({clave: $(this).attr("name"), valor: $(this).val()});
		});
		var checkbox = ["collapsemenu","fixednavbar","boxedlayout","shownotification","disablechat","offline_users"];
		$.each(checkbox, function(i, val) {
			if($("#"+val).is(':checked'))
				ckb = "S";
			else
				ckb = "N";
			post.push({clave: val, valor: ckb});
		});
		post.push({clave: "skin", valor: skin_selected});
		post.push({clave: "class_skin", valor: skin_class_selected});

		ajax.post({url: _base_url+"home/default_values", data: {datos:post}}, function(res) {
			// localStorage, aceptara un array como value?
			// saveStorage("default_values", JSON.stringify(datos));
			setDefaultValue(local);
			toastr.success('Datos guardados correctamente.');
		});
		
		return false;
	});
	
	$('.img-circle').tooltip({placement: "right"});
	$(document).on("click","a#change_clave",function(){
		$("#modal-change-pass input").val('');
		$("#clave_nueva").attr('readonly','readonly');
		$("#modal-change-pass").modal("show");
		setTimeout(function(){
			$("#clave_anterior").focus();
		},500);
	});
	
	$("#btn-verificar-pass").click(function(){
		if($("#clave_anterior").required()){
			ajax.post({
				url: _base_url+"usuario/verificar_clave", 
				data: "clave_anterior="+$("#clave_anterior").val()
			},function(res) {
				// alert('En mantenimiento....');
				$(".sms_pass").empty().hide();
				if(res){
					$("#clave_nueva").removeAttr('readonly');
					// $(".sms_pass").html().show();
				}else{
					$("#clave_nueva").attr('readonly','readonly');
					$(".sms_pass").html("Clave Actual Incorrecto...!").show();
				}
			});
		}
	});
	
	$("#save_clave").click(function(){
		s = true && $("#clave_anterior").required();
		s = s    && $("#clave_nueva").required();
		if(s){
			ajax.post({
				url: _base_url+"usuario/change_pass", 
				data: "clave="+$("#clave_nueva").val()
			},function(res) {
				
				$("#modal-change-pass").modal("hide");
			});
		}
	});
	
	$(document).on("click","a#change_avatar",function(){
		$("#modal-change-avatar").modal("show");
	});
	
	$("#load_avatar").click(function() {
		$("#file_avatar").click();
	});
	
	$(document).on("click","#avatar_session",function() {
		$("#modal-change-avatar").modal("show");
	});
	
	$("#save_avatar").click(function(){
		Save_avatar("form-change-avatar");
	});
	
	$("#ver_alertas").click(function(e){
		e.preventDefault();
		open_url_windows("home/see_alert");
	});
	
	// Enable/disable fixed top navbar
    $('#fixednavbar').click(function (){
        if ($('#fixednavbar').is(':checked')){
            $(".navbar-static-top").removeClass('navbar-static-top').addClass('navbar-fixed-top');
            $("body").removeClass('boxed-layout');
            $("body").addClass('fixed-nav');
            $('#boxedlayout').prop('checked', false);

            if (localStorageSupport){
                localStorage.setItem("boxedlayout",'off');
            }

            if (localStorageSupport){
                localStorage.setItem("fixednavbar",'on');
            }
        } else{
            $(".navbar-fixed-top").removeClass('navbar-fixed-top').addClass('navbar-static-top');
            $("body").removeClass('fixed-nav');
            $("body").removeClass('fixed-nav-basic');
            $('#fixednavbar2').prop('checked', false);

            if (localStorageSupport){
                localStorage.setItem("fixednavbar",'off');
            }

            if (localStorageSupport){
                localStorage.setItem("fixednavbar2",'off');
            }
        }
		save_other_config();
    });

    // Enable/disable fixed top navbar
    $('#fixednavbar2').click(function (){
        if ($('#fixednavbar2').is(':checked')){
            $(".navbar-static-top").removeClass('navbar-static-top').addClass('navbar-fixed-top');
            $("body").removeClass('boxed-layout');
            $("body").addClass('fixed-nav').addClass('fixed-nav-basic');
            $('#boxedlayout').prop('checked', false);

            if (localStorageSupport){
                localStorage.setItem("boxedlayout",'off');
            }

            if (localStorageSupport){
                localStorage.setItem("fixednavbar2",'on');
            }
        } else {
            $(".navbar-fixed-top").removeClass('navbar-fixed-top').addClass('navbar-static-top');
            $("body").removeClass('fixed-nav').removeClass('fixed-nav-basic');
            $('#fixednavbar').prop('checked', false);

            if (localStorageSupport){
                localStorage.setItem("fixednavbar2",'off');
            }
            if (localStorageSupport){
                localStorage.setItem("fixednavbar",'off');
            }
        }
		save_other_config();
    });

    // Enable/disable fixed sidebar
    $('#fixedsidebar').click(function (){
        if ($('#fixedsidebar').is(':checked')){
            $("body").addClass('fixed-sidebar');
            $('.sidebar-collapse').slimScroll({
                height: '100%',
                railOpacity: 0.9
            });

            if (localStorageSupport){
                localStorage.setItem("fixedsidebar",'on');
            }
        } else{
            $('.sidebar-collapse').slimscroll({destroy: true});
            $('.sidebar-collapse').attr('style', '');
            $("body").removeClass('fixed-sidebar');

            if (localStorageSupport){
                localStorage.setItem("fixedsidebar",'off');
            }
        }
		save_other_config();
    });

    // Enable/disable collapse menu
    $('#collapsemenu').click(function (){
        if ($('#collapsemenu').is(':checked')){
            $("body").addClass('mini-navbar');
            SmoothlyMenu();

            if (localStorageSupport){
                localStorage.setItem("collapse_menu",'on');
            }

        } else{
            $("body").removeClass('mini-navbar');
            SmoothlyMenu();

            if (localStorageSupport){
                localStorage.setItem("collapse_menu",'off');
            }
        }
		save_other_config();
    });

    // Enable/disable boxed layout
    $('#boxedlayout').click(function (){
        if ($('#boxedlayout').is(':checked')){
            $("body").addClass('boxed-layout');
            $('#fixednavbar').prop('checked', false);
            $('#fixednavbar2').prop('checked', false);
            $(".navbar-fixed-top").removeClass('navbar-fixed-top').addClass('navbar-static-top');
            $("body").removeClass('fixed-nav');
            $("body").removeClass('fixed-nav-basic');
            $(".footer").removeClass('fixed');
            $('#fixedfooter').prop('checked', false);

            if (localStorageSupport){
                localStorage.setItem("fixednavbar",'off');
            }

            if (localStorageSupport){
                localStorage.setItem("fixednavbar2",'off');
            }

            if (localStorageSupport){
                localStorage.setItem("fixedfooter",'off');
            }


            if (localStorageSupport){
                localStorage.setItem("boxedlayout",'on');
            }
        } else{
            $("body").removeClass('boxed-layout');

            if (localStorageSupport){
                localStorage.setItem("boxedlayout",'off');
            }
        }
		save_other_config();
    });

    // Enable/disable fixed footer
    $('#fixedfooter').click(function (){
        if ($('#fixedfooter').is(':checked')){
            $('#boxedlayout').prop('checked', false);
            $("body").removeClass('boxed-layout');
            $(".footer").addClass('fixed');

            if (localStorageSupport){
                localStorage.setItem("boxedlayout",'off');
            }

            if (localStorageSupport){
                localStorage.setItem("fixedfooter",'on');
            }
        } else{
            $(".footer").removeClass('fixed');

            if (localStorageSupport){
                localStorage.setItem("fixedfooter",'off');
            }
        }
		save_other_config();
    });
	
	$('#shownotification').click(function (){
		if($(this).is(":checked")){
			$(".show_alert").show();
		}else{
			$(".show_alert").hide();
		}
		save_other_config();
    });
	
	$('#disablechat').click(function (){
		save_other_config();
    });
	
	$('#offline_users').click(function (){
		save_other_config();
    });
	
	if($.trim(_class_skin)!=''){
		$("."+_class_skin).trigger("click");
	}
	
	/*
	// Busqueda de contactos
	$("#fu").keyup(function(e) {
		$(".dlu ul li").css('display','none');
        
        var buscar = $.trim( $(this).val() );
        
        if(buscar != '') {
			$(".dlu ul li:contains('" + buscar + "')").css('display','block');
			$(".dlu ul li:contains('" + buscar.toUpperCase() + "')").css('display','block');
        }else
            $(".dlu ul li").css('display','block');
	});
	
	$("#dropdown-chat li").click(function(e){
		var li_			= $(this);
		var nombre_user	= li_.attr("data-user-name");
		var avatar_user	= li_.attr("data-avatar");
		var sucur_user	= li_.attr("data-sucursal-online");
		var status_user	= li_.attr("data-status");
		
		$("#name_user_chat").html(nombre_user);
		$("#sucursal_online").html(sucur_user);
		$("#avatar_user_chat").attr("src",avatar_user);
		$("#status_chat_user").removeClass("avatar-offline").removeClass("avatar-online").addClass(status_user);
		$('#chat_compose_wrapper').toggleClass('sidebar-open');
		
		if($('#chat_compose_wrapper').hasClass("sidebar-open")){
			$("#input-send").focus();
		}
	});
	
	$("#chat_compose_wrapper a#close_chat").click(function(){
		$('#chat_compose_wrapper').toggleClass('sidebar-open');
	});
	
	// Evento para enviar el mensaje de chat
	$("#input-send").keypress(function(e){
		console.log(e.keyCode);
		if(e.keyCode == 13){
			send_sms = "<div class='right'>";//right el que manda, left el que recibe
			send_sms+= "	<div class='author-name'>";
			send_sms+= "		YO";
			send_sms+= "		<small class='chat-date'>";
			send_sms+= "			11:24 am";
			send_sms+= "		</small>";
			send_sms+= "	</div>";
			send_sms+= "	<div class='chat-message'>";//añadir la clase 'active' para el que recibe
			send_sms+= "	"+$("#input-send").val();
			send_sms+= "	</div>";
			send_sms+= "</div>";
			
			$(".lg-chat-box div.content").append(send_sms);
			$("#input-send").val("")
		}
	});
	//*/
});

function save_other_config(skin,clase){
	if(skin_class_selected==clase)
		return;
	skin	= skin || skin_selected;
	clase	= clase || skin_class_selected;
	
	skin_selected = skin;
	skin_class_selected = clase;
	
	$(".btn-config-save").trigger("click");
}

function leer_archivo(f) {
    id='photoAvatar';

    var imagenAR = document.getElementById("file_avatar");
    if (imagenAR.files.length != 0 && imagenAR.files[0].type.match(/image.*/)) {
    var lecimg = new FileReader();
    lecimg.onload = function(e) { 
      var img = document.getElementById(id);
      img.src = e.target.result;
    } 
    lecimg.onerror = function(e) { 
	  ventana.alert({titulo: "Hey!", mensaje: "Error leyendo la imagen!!", tipo:"warning"});
    }
    lecimg.readAsDataURL(imagenAR.files[0]);
    } else {
      ventana.alert({titulo: "Hey!", mensaje: "Seleccione una imagen", tipo:"warning"});
    }
}

function Save_avatar(id){//id= id del formulario
  var fd = new FormData(document.getElementById(id));
    $.ajax({
      url: _base_url+"usuario/change_avatar",
      type: "POST",
      data: fd,
      enctype: 'multipart/form-data',
      processData: false,  // tell jQuery not to process the data
      contentType: false   // tell jQuery not to set contentType
    }).done(function( data ) {
		data = jQuery.parseJSON(data);
		if($.isNumeric(data.idusuario)){
			$("#avatar_session").attr("src",'app/img/usuario/'+data.avatar);
			$(".sms_avatar").html("Avatar cambiado correctamente...!").removeClass('alert-danger').addClass('alert-success').show();
			
			setTimeout(function(){
				$("#modal-change-avatar").modal("hide");
			},1000);
		}else{
			$(".sms_avatar").html(data).removeClass('alert-success').addClass('alert-danger').show();
			// ventana.alert({titulo: "Upss...!", mensaje: data, tipo:"warning"}, function() {
				
			// });
		}
    });
    return false;
}